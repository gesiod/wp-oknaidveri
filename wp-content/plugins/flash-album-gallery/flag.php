<?php
/*
Plugin Name: GRAND FlaGallery
Plugin URI: https://mypgc.co/
Description: The Grand Flagallery plugin - provides a comprehensive interface for managing photos and images through a set of admin pages, and it displays photos in a way that makes your web site look very professional.
Version: 5.1.7
Author: Rattus
Author URI: https://codeasily.com/
Text Domain: flash-album-gallery
Domain Path: /lang
-------------------

		Copyright 2009  Sergey Pasyuk  (email : pasyuk@gmail.com)

*/

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

//ini_set('display_errors', '1');
//ini_set('error_reporting', E_ALL);


if (!class_exists('flagLoad')) {
class flagLoad {

	var $version = '5.1.7';
	var $dbversion   = '5.0';
	var $minium_WP   = '3.7';
	var $minium_WPMU = '3.7';
	var $flagAdminPanel;
	var $options     = '';
	var $manage_page;
	var $add_PHP5_notice = false;
	var $shortcode = 0;

	function __construct() {

		// Load the language file
		$this->load_textdomain();

		// Stop the plugin if we missed the requirements
		if ( ( !$this->required_version() ) || ( !$this->check_memory_limit() ) )
			return;

		// Get some constants first
		$this->load_options();
		$this->define_constant();
		$this->define_tables();
		$this->load_dependencies();

		$this->plugin_name = plugin_basename(__FILE__);

		// Init options & tables during activation & deregister init option
		register_activation_hook( $this->plugin_name, array(&$this, 'activate') );
		add_action( 'init', array(&$this, 'wp_flag_tune_messages') );
		register_deactivation_hook( $this->plugin_name, array(&$this, 'deactivate') );

		// Register a uninstall hook to remove all tables & option automatic
		register_uninstall_hook( $this->plugin_name, array('flagLoader', 'uninstall') );

		// Start this plugin once all other plugins are fully loaded
		add_action( 'plugins_loaded', array(&$this, 'start_plugin') );

		if (version_compare(PHP_VERSION, '5.3', '<'))
			add_filter('transient_update_plugins', array(&$this, 'disable_upgrade'));

		//Add some message on the plugin page
		//add_action( 'after_plugin_row', array(&$this, 'flag_check_message_version') );

		add_action( 'init', array(&$this, 'flag_fullwindow_page_init') );
		add_action( 'add_meta_boxes', array(&$this, 'flag_fullwindow_page_add_meta_box') );
		add_action( 'save_post', array(&$this, 'flag_fullwindow_page_save_meta_box') );
		add_action( 'template_redirect', array(&$this, 'flag_fullwindow_page_template_redirect') );
		add_filter( 'media_buttons_context', array(&$this, 'addFlAGMediaIcon') );
		add_action( 'admin_print_scripts-widgets.php', array(&$this, 'flag_widgets_scripts') );
		add_filter( 'posts_orderby', 'sort_query_by_post_in', 10, 2 );

		add_action( 'wp_enqueue_scripts', array( &$this, 'register_scripts_frontend' ), 3 );

		add_action('activated_plugin', array(&$this, 'save_error') );
	}

	function save_error(){
		update_option('flag_plugin_error',  ob_get_contents());
	}

	function start_plugin() {

		// Content Filters
		add_filter('flag_gallery_name', 'sanitize_title');

		// Load the admin panel or the frontend functions
		if ( is_admin() ) {

			// Pass the init check or show a message
			if (get_option( "flag_init_check" ) != false )
				add_action( 'admin_notices', create_function('', 'echo \'<div id="message" class="error"><p><strong>' . get_option( "flag_init_check" ) . '</strong></p></div>\';') );

		} else {

			// Add MRSS to wp_head
			if ( $this->options['useMediaRSS'] )
				add_action('wp_head', array('flagMediaRss', 'add_mrss_alternate_link'));

			// Add the script and style files
			add_action('wp_print_scripts', array(&$this, 'load_scripts') );
			add_action( 'flag_footer_scripts', array( &$this, 'load_scripts' ) );

			// Add a version number to the header
			add_action('wp_head', create_function('', 'echo "\n<!-- <meta name=\'Grand Flagallery\' content=\'' . $this->version . '\' /> -->\n";') );

		}
	}

	function wp_flag_tune_messages() {
		if(get_option('flagVersion') != $this->version) {
			// upgrade plugin
			require_once(FLAG_ABSPATH . 'admin/tuning.php');
			$ok = flag_tune($show_error=false);

			include_once (dirname (__FILE__) . '/admin/flag_install.php');
			// check for tables
			flag_capabilities();
			update_option("flagVersion", FLAGVERSION);
		}
		// check for upgrade
		if( get_option( 'flag_db_version' ) < FLAG_DBVERSION ) {
			include_once ( dirname (__FILE__) . '/admin/functions.php' );
			include_once ( dirname (__FILE__) . '/admin/upgrade.php' );
			flag_upgrade();
			add_action( 'admin_notices', create_function('', 'echo \'<div id="message" class="updated"><p>\' . __(\'Grand Flagallery database upgraded\', "flash-album-gallery" ) . \'</p></div>\';') );

            if('5.0' === FLAG_DBVERSION){
                add_action('admin_notices', array( &$this, 'update_5_0_message' ));
            }
		}
	}

	function update_5_0_message() {
	    ?>
        <div id="message" class="updated"><?php _e('<h2>Flagallery Wordpress Plugin v5.0</h2>
<p>WordPress announced removing adobe flash based plugins from WordPress v4.9.<br />
Flagallery was reworked from the ground and here is the all new skins now built with HTML and JS.<br />
<b style="color:red;">Note:</b> all your old galleries now will be replaced with new default skin "Phantom".<br />
You can install new skins at the skins page and use them in flagallery shortcodes.</p>
<p><b><i>Changes:</i></b><br />
* All flash code replaced with HTML & JS<br />
* GRAND Page have new option for background: Background Size<br />
* GRAND Page added \'assist\' link for Background Image<br />
* iFrame tool now have embed code, which you can copy and paste whenever you need</p>
<p>* <b>New Skins:</b><br />
 - Phantom - free<br />
 - NivoGallery - free<br />
 - Nivo Slider - free Banner Rotator<br />
 - jQ Music Player - free Music Playlist<br />
 - WP Video Player - free Video Playlist<br />
 - Photomania - premium<br />
 - FlipGrid - premium with multicategory<br />
 - PhotoBox - premium<br />
 - Mosaic - premium<br />
 - AlbumsGrid - premium splash gallery<br />
 - AlbumsSwitcher - premium splash gallery<br />
 - PhotoCluster - premium splash gallery</p>', "flash-album-gallery" ); ?></p></div>
        <?php
    }

	function required_version() {

		global $wp_version, $wpmu_version;

		// Check for WPMU installation
		if (!defined ('IS_WPMU'))
			define('IS_WPMU', version_compare($wpmu_version, $this->minium_WPMU, '>=') );

 		// Check for WP version installation
		$wp_ok  =  version_compare($wp_version, $this->minium_WP, '>=');

		if ( ($wp_ok == FALSE) and (IS_WPMU == FALSE) ) {
			add_action('admin_notices', create_function('', 'global $flag; printf (\'<div id="message" class="error"><p><strong>\' . __(\'Sorry,GRAND FlaGallery works only under WordPress %s or higher\', "flash-album-gallery" ) . \'</strong></p></div>\', $flag->minium_WP );'));
			return false;
		}

		return true;

	}

	function check_memory_limit() {

		$memory_limit = (int) substr( ini_get('memory_limit'), 0, -1);
		//This works only with enough memory, 8MB is silly, wordpress requires already 7.9999
		if ( ($memory_limit != 0) && ($memory_limit < 12 ) ) {
			add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error"><p><strong>' . __('Sorry, GRAND FlaGallery works only with a Memory Limit of 16 MB higher', 'flash-album-gallery') . '</strong></p></div>\';'));
			return false;
		}

		return true;

	}

	function define_tables() {
		global $wpdb;

		// add database pointer
		$wpdb->flagpictures					= $wpdb->prefix . 'flag_pictures';
		$wpdb->flaggallery					= $wpdb->prefix . 'flag_gallery';
		$wpdb->flagcomments					= $wpdb->prefix . 'flag_comments';
		$wpdb->flagalbum					= $wpdb->prefix . 'flag_album';

	}

	function define_constant() {

		define('FLAGVERSION', $this->version);
		// Minimum required database version
		define('FLAG_DBVERSION', $this->dbversion);

		// required for Windows & XAMPP
		if ( !defined('WINABSPATH') ) {
			define('WINABSPATH', str_replace("\\", "/", ABSPATH) );
		}

		// define URL
		define('FLAGFOLDER', plugin_basename( dirname(__FILE__)) );

		define('FLAG_ABSPATH', str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/' ));
		define('FLAG_URLPATH', plugins_url() . '/' . plugin_basename( dirname(__FILE__) ) . '/' );

		// get value for safe mode
		if ( !defined('FLAG_SAFE_MODE') ) {
		    if(version_compare('5.3', phpversion(), '>')){
                if((gettype(ini_get('safe_mode')) == 'string')){
                    // if sever did in in a other way
                    if(ini_get('safe_mode') == 'off'){
                        define('FLAG_SAFE_MODE', false);
                    } else{
                        define('FLAG_SAFE_MODE', ini_get('safe_mode'));
                    }
                } else{
                    define('FLAG_SAFE_MODE', ini_get('safe_mode'));
                }
            } else{
                define('FLAG_SAFE_MODE', false);
            }
		}

	}

	function load_dependencies() {
        global $wp_version;

		// Load global libraries
		require_once (dirname (__FILE__) . '/lib/core.php');
		require_once (dirname (__FILE__) . '/lib/flag-db.php');
		require_once (dirname (__FILE__) . '/lib/image.php');
		require_once (dirname (__FILE__) . '/widgets/widgets.php');

		$current_plugins = get_option('active_plugins');
		if (in_array('wordpress-seo/wp-seo.php', $current_plugins)) {
			require_once (dirname (__FILE__) . '/lib/sitemap.php');
		}

		// We didn't need all stuff during a AJAX operation
		if ( defined('DOING_AJAX') )
			require_once (dirname (__FILE__) . '/admin/ajax.php');
		else {
			require_once (dirname (__FILE__) . '/lib/meta.php');
			require_once (dirname (__FILE__) . '/lib/media-rss.php');
            if(version_compare($wp_version, '4.5', '<')){
                include_once(dirname(__FILE__) . '/admin/tinymce/tinymce.php');
            }

			// Load backend libraries
			if ( is_admin() ) {
				require_once (dirname (__FILE__) . '/admin/admin.php');
				require_once (dirname (__FILE__) . '/admin/media-upload.php');
				$this->flagAdminPanel = new flagAdminPanel();

			// Load frontend libraries
			} else {
				require_once (dirname (__FILE__) . '/lib/skinobject.php');
				require_once (dirname (__FILE__) . '/lib/shortcodes.php');
			}
		}
	}

	function load_textdomain() {

		load_plugin_textdomain('flash-album-gallery', false, dirname( plugin_basename(__FILE__) ) . '/lang');

	}

	function register_scripts_frontend() {
	    global $wp_scripts;

        wp_register_style('flagallery', plugins_url('/flash-album-gallery/assets/flagallery.css'), array(), $this->version );
		wp_register_script('flagscript', plugins_url('/flash-album-gallery/assets/script.js'), array('jquery'), $this->version, true );
        wp_localize_script('flagscript', 'FlaGallery', array(
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('FlaGallery'),
            'license'       => strtolower($this->options['license_key']),
            'pack'          => $this->options['license_name']
        ));

        if( !wp_script_is('mediaelement', 'registered')){
            wp_register_style('mediaelement', plugins_url('/flash-album-gallery/assets/mediaelement/mediaelementplayer.min.css'), array(), '2.13.0', 'screen');
            wp_register_script('mediaelement', plugins_url('/flash-album-gallery/assets/mediaelement/mediaelement-and-player.min.js'), array('jquery'), '2.13.0', true);
        }

        if( !wp_script_is('wavesurfer', 'registered')){
            wp_register_script('wavesurfer', plugins_url('/flash-album-gallery/assets/wavesurfer/wavesurfer.min.js'), array('jquery'), '1.2.8', true);
        }

        if( !wp_script_is('swiper', 'registered') || version_compare($wp_scripts->registered['swiper']->ver, '3.4.0', '<')){
            wp_deregister_style('swiper');
            wp_deregister_script('swiper');
            wp_register_style('swiper', plugins_url('/flash-album-gallery/assets/swiper/swiper.min.css'), array(), '3.4.0', 'screen');
            wp_register_script('swiper', plugins_url('/flash-album-gallery/assets/swiper/swiper.jquery.min.js'), array('jquery'), '3.4.0', true);
        }

        if( !wp_script_is('magnific-popup', 'registered') || version_compare($wp_scripts->registered['magnific-popup']->ver, '1.1.0', '<')){
            wp_deregister_style('magnific-popup');
            wp_deregister_script('magnific-popup');
            wp_register_style('magnific-popup', plugins_url('/flash-album-gallery/assets/mag-popup/magnific-popup.css'), array(), '1.1.0', 'screen');
            wp_register_script('magnific-popup', plugins_url('/flash-album-gallery/assets/mag-popup/jquery.magnific-popup.min.js'), array('jquery'), '1.1.0', true);
        }

        if( !wp_script_is('photoswipe', 'registered') || version_compare($wp_scripts->registered['photoswipe']->ver, '3.0.5', '<=')){
            wp_deregister_style('photoswipe');
            wp_deregister_script('photoswipe');
            wp_register_style('photoswipe', plugins_url('/flash-album-gallery/assets/photoswipe/photoswipe.css'), array(), '3.0.5', 'screen');
            wp_register_script('photoswipe', plugins_url('/flash-album-gallery/assets/photoswipe/photoswipe.jquery.min.js'), array('jquery'), '3.0.5', true);
        }

        if( !wp_script_is('nivo-slider', 'registered') || version_compare($wp_scripts->registered['nivo-slider']->ver, '3.2', '<=')){
            wp_deregister_style('nivo-slider');
            wp_deregister_script('nivo-slider');
            wp_register_style('nivo-slider', plugins_url('/flash-album-gallery/assets/nivoslider/nivo-slider.css'), array(), '3.2', 'screen');
            wp_register_script('nivo-slider', plugins_url('/flash-album-gallery/assets/nivoslider/jquery.nivo.slider.pack.js'), array('jquery'), '3.2', true);
        }

        if( !wp_script_is('easing', 'registered') || ($wp_scripts->registered['easing']->ver !== false && version_compare($wp_scripts->registered['easing']->ver, '1.3.0', '<'))){
            wp_deregister_script('easing');
            wp_register_script('easing', plugins_url('/flash-album-gallery/assets/jq-plugins/jquery.easing.js'), array('jquery'), '1.3.0', true);
        }
        if( !wp_script_is('fancybox', 'registered') || ($wp_scripts->registered['fancybox']->ver !== false && version_compare($wp_scripts->registered['fancybox']->ver, '1.3.4', '<'))){
            if( !defined('FANCYBOX_VERSION')){
                wp_deregister_style('fancybox');
                wp_register_style('fancybox', plugins_url('/flash-album-gallery/assets/fancybox/jquery.fancybox-1.3.4.css'), array(), '1.3.4');
            }
            wp_deregister_script('fancybox');
            wp_register_script('fancybox', plugins_url('/flash-album-gallery/assets/fancybox/jquery.fancybox-1.3.4.pack.js'), array('jquery', 'easing'), '1.3.4', true);
        }


        if( !wp_script_is('jplayer', 'registered') || version_compare($wp_scripts->registered['jplayer']->ver, '2.6.4', '<')){
            wp_deregister_script('jplayer');
            wp_register_script('jplayer', plugins_url('/flash-album-gallery/assets/jplayer/jquery.jplayer.min.js'), array('jquery'), '2.6.4', true);
        }

        wp_register_script('mousetrap', plugins_url('/flash-album-gallery/assets/mousetrap/mousetrap.min.js'), array(), '1.5.2', true);

        $this->load_scripts();
    }

	function load_scripts() {
        wp_enqueue_style('flagallery');

		wp_enqueue_script('jquery');
		wp_enqueue_script('flagscript');

	}

	function flag_widgets_scripts() {

		wp_enqueue_script('widgets_admin', plugins_url('/flash-album-gallery/admin/js/widgets_admin.js'), array('jquery'), '1.0');

	}

	function load_options() {
		// Load the options
		$this->options = get_option('flag_options');
	}

	function activate() {
		include_once (dirname (__FILE__) . '/admin/flag_install.php');
		// check for tables
		flag_install();
		$this->flag_fullwindow_page_init();
		flush_rewrite_rules();
	}

	function deactivate() {
		// remove & reset the init check option
		delete_option( 'flag_init_check' );
	}

	function uninstall() {
	  	include_once (dirname (__FILE__) . '/admin/flag_install.php');
	    flag_uninstall();
	}

	function disable_upgrade($option){

	 	$this_plugin = plugin_basename(__FILE__);

		// PHP5.3 is required for FlAG V5.0
		if ( version_compare($option->response[ $this_plugin ]->new_version, '5.0', '>=') )
			return $option;

	    if( isset($option->response[ $this_plugin ]) ){
	        //TODO:Clear its download link, not now but maybe later
	        //$option->response[ $this_plugin ]->package = '';

	        //Add a notice message
	        if ($this->add_PHP5_notice == false){
   	    		add_action( "in_plugin_update_message-$this->plugin_name", create_function('', 'echo \'<br /><span style="color:red">Please update to PHP5.3 or newer as soon as possible, the plugin is not tested under PHP5.2 anymore</span>\';') );
	    		$this->add_PHP5_notice = true;
			}
		}
	    return $option;
	}

	// PLUGIN MESSAGE ON PLUGINS PAGE
	function flag_check_message_version($file)
	{
		static $this_plugin;
		global $wp_version;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

		if ($file == $this_plugin ){
			$checkfile = "https://codeasily.com/flagallery.chk";

			$message = wp_remote_fopen($checkfile);

			if($message)
			{
				preg_match( '|flag040:(.*)$|mi', $message, $theMessage );

				$columns = substr($wp_version, 0, 3) == "2.8" ? 3 : 5;

				if ( !empty( $theMessage ) )
				{
					$theMessage = trim($theMessage[1]);
					echo '<td colspan="'.$columns.'" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div id="flag-update-msg" style="padding-bottom:1px;" >'.$theMessage.'</div></td>';
				} else {
					return;
				}
			}
		}
	}

	function flag_fullwindow_page_init() {
		if(current_user_can('FlAG Use TinyMCE')){
			$visibility = true;
		} else {
			$visibility = false;
		}
	  $labels = array(
	    'name' => _x('GRAND Galleries', 'post type general name', 'flash-album-gallery'),
	    'singular_name' => __('FlAGallery Page', 'flash-album-gallery'),
	    'add_new' => __('Add New Gallery Page', 'flash-album-gallery'),
	    'add_new_item' => __('Add New Gallery Page', 'flash-album-gallery'),
	    'edit_item' => __('Edit Gallery Page', 'flash-album-gallery'),
	    'new_item' => __('New Gallery Page', 'flash-album-gallery'),
	    'all_items' => __('All GRAND Galleries', 'flash-album-gallery'),
	    'view_item' => __('View Gallery Page', 'flash-album-gallery'),
	    'search_items' => __('Search GRAND Galleries', 'flash-album-gallery'),
	    'not_found' =>  __('No GRAND Galleries found', 'flash-album-gallery'),
	    'not_found_in_trash' => __('No GRAND Galleries found in Trash', 'flash-album-gallery'),
	    'parent_item_colon' => '',
	    'menu_name' => 'GRAND Pages'

	  );
	  $args = array(
	    'labels' => $labels,
	    'description' => __('This is the page template for displaing Grand Flagallery galleries in full width and height of browser window.', 'flash-album-gallery'),
	    'public' => true,
	    'publicly_queryable' => true,
	    'show_ui' => true,
	    'show_in_menu' => $visibility,
	    'menu_position' => 20,
	    'menu_icon' => FLAG_URLPATH .'admin/images/flag.png',
	    'capability_type' => 'post',
	    'hierarchical' => true,
	    'supports' => array('title','author','thumbnail','excerpt','page-attributes'),
	    'has_archive' => true,
		'rewrite' => array( 'slug' => 'flagallery','with_front' => FALSE),
	    'query_var' => true,
	  );
		  register_post_type('flagallery',$args);
	}

	/* Adds a meta box to the main column on the flagallery edit screens */
	function flag_fullwindow_page_add_meta_box() {
	    add_meta_box( 'flag_gallery', __( 'Photo Gallery Page Generator', 'flash-album-gallery' ), array(&$this, 'flag_fullwindow_page_meta_box'), 'flagallery', 'normal', 'high' );
	}

	/* Prints the meta box content */
	function flag_fullwindow_page_meta_box( $post ) {

	  // Use nonce for verification
	  wp_nonce_field( plugin_basename( __FILE__ ), 'flag_meta_box' );

	  include_once(dirname(__FILE__) . '/admin/meta_box.php');
	}

	/* When the post is saved, saves our custom data */
	function flag_fullwindow_page_save_meta_box( $post_id ) {
	  // verify if this is an auto save routine.
	  // If it is our form has not been submitted, so we dont want to do anything
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	      return;

	  // verify this came from the our screen and with proper authorization,
	  // because save_post can be triggered at other times

	  if ( !isset($_POST['flag_meta_box']) || !wp_verify_nonce( $_POST['flag_meta_box'], plugin_basename( __FILE__ ) ) )
	      return;

	  // Check permissions
	  if ( 'flagallery' == $_POST['post_type'] )
	  {
	    if ( !current_user_can( 'edit_page', $post_id ) )
	        return;
	  }
	  else
	  {
	    if ( !current_user_can( 'edit_post', $post_id ) )
	        return;
	  }
	  // OK, we're authenticated: we need to save the data
	  update_post_meta($post_id, "mb_items_array", $_POST["mb_items_array"]);
	  update_post_meta($post_id, "mb_skinname", $_POST["mb_skinname"]);
	  update_post_meta($post_id, "mb_scode", $_POST["mb_scode"]);
	  update_post_meta($post_id, "mb_button", $_POST["mb_button"]);
	  update_post_meta($post_id, "mb_button_link", $_POST["mb_button_link"]);
	  update_post_meta($post_id, "mb_bg_link", $_POST["mb_bg_link"]);
	  update_post_meta($post_id, "mb_bg_pos", $_POST["mb_bg_pos"]);
	  update_post_meta($post_id, "mb_bg_repeat", $_POST["mb_bg_repeat"]);
	  update_post_meta($post_id, "mb_bg_size", $_POST["mb_bg_size"]);

  	}

	// Template selection
	function flag_fullwindow_page_template_redirect() {
		global $wp;
		global $wp_query;
		if (isset($wp->query_vars["post_type"]) && $wp->query_vars["post_type"] == "flagallery")
		{
			// Let's look for the full_window_template.php template file
			if (have_posts())
			{
				include(FLAG_ABSPATH . 'full_window_template.php');
				die();
			}
			else
			{
				$wp_query->is_404 = true;
			}
		}
	}

	function addFlAGMediaIcon($context){
		if(current_user_can('FlAG Use TinyMCE')){
			$flag_upload_iframe_src = add_query_arg(array('action' => 'flag_shortcode_helper', 'media_button' => 'true'), admin_url( 'admin-ajax.php' ));
			$title = __('Add Grand Flagallery');
			$button = '<a href="'.$flag_upload_iframe_src.'&amp;TB_iframe=1&amp;width=360&amp;height=210" class="thickbox button" id="add_flagallery" title="'.$title.'"><span style="margin:0 5px;">FlAGallery</span></a>';
		} else {
			$button = '';
		}
	  return $context.$button;
	}


}
	// Let's start the holy plugin
	global $flag;
	$flag = new flagLoad();

}
if(!function_exists('sort_query_by_post_in')){
	function sort_query_by_post_in( $sortby, $thequery ) {
		if ( !empty($thequery->query['post__in']) && isset($thequery->query['orderby']) && $thequery->query['orderby'] == 'post__in' )
			$sortby = "find_in_set(ID, '" . implode( ',', $thequery->query['post__in'] ) . "')";

		return $sortby;
	}
}
if(!function_exists('sanitize_flagname')){
	function sanitize_flagname( $filename ) {

		$filename = wp_strip_all_tags( $filename );
		$filename = remove_accents( $filename );
		// Kill octets
		$filename = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $filename );
		$filename = preg_replace( '/&.+?;/', '', $filename ); // Kill entities
		$filename = preg_replace( '|[^a-zA-Z0-9 _.\-]|i', '', $filename );
		$filename = preg_replace('/[\s-]+/', '-', $filename);
		$filename = trim($filename, '.-_ ');

		return $filename;
	}
}
