<?php

/**
 * flagAdminPanel - Admin Section for FlaGallery
 *
 */
class flagAdminPanel {

	// constructor
	function __construct() {

		// Add the admin menu
		add_action( 'admin_menu', array( &$this, 'add_menu' ) );
		add_action( 'init', array( &$this, 'wp_flag_check_options' ), 2 );

        // Add the script and style files
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'), 20);

		// Add the script and style files
		add_action( 'admin_print_scripts', array( &$this, 'load_scripts' ) );
		add_action( 'admin_print_styles', array( &$this, 'load_styles' ) );

		add_filter( 'contextual_help', array( &$this, 'show_help' ), 10, 2 );
		add_filter( 'screen_meta_screen', array( &$this, 'edit_screen_meta' ) );

        add_filter('admin_head', array (&$this, 'wp_flag_ins_button' ), 5);

    }

	function wp_flag_check_options() {
		global $flag;
		require_once( dirname( __FILE__ ) . '/flag_install.php' );

        if (isset($_GET['page']) && 'flag-overview' === $_GET['page'] && isset($_POST['uninstall'])) {
            check_admin_referer('flag_uninstall');
            flag_uninstall();
        }


        $default_options = flag_list_options();
		$flag_db_options = get_option( 'flag_options' );
		if ( $flag_db_options ) {
			if ( function_exists( 'array_diff_key' ) ) {
				$flag_new_options = array_diff_key( $default_options, $flag_db_options );
			} else {
				$flag_new_options = $this->PHP4_array_diff_key( $default_options, $flag_db_options );
			}
			$flag_options = array_merge( $flag_db_options, $flag_new_options );
			update_option( 'flag_options', $flag_options );
		} else {
			update_option( 'flag_options', $default_options );
		}
	}

	function PHP4_array_diff_key() {
		$arrs   = func_get_args();
		$result = array_shift( $arrs );
		foreach ( $arrs as $array ) {
			foreach ( $result as $key => $v ) {
				if ( array_key_exists( $key, $array ) ) {
					unset( $result[ $key ] );
				}
			}
		}

		return $result;
	}

	// integrate the menu
	function add_menu() {

		add_menu_page( __( 'GRAND FlaGallery overview', 'flash-album-gallery' ), __( 'FlAGallery' ), 'FlAG overview', 'flag-overview', array(
			&$this,
			'show_menu'
		), FLAG_URLPATH . 'admin/images/flag.png' );
		add_submenu_page( 'flag-overview', __( 'GRAND FlaGallery overview', 'flash-album-gallery' ), __( 'Overview', 'flash-album-gallery' ), 'FlAG overview', 'flag-overview', array(
			&$this,
			'show_menu'
		) );
		add_submenu_page( 'flag-overview', __( 'FlAG Manage gallery', 'flash-album-gallery' ), __( 'Manage Galleries', 'flash-album-gallery' ), 'FlAG Manage gallery', 'flag-manage-gallery', array(
			&$this,
			'show_menu'
		) );
		add_submenu_page( 'flag-overview', __( 'FlAG Music Box', 'flash-album-gallery' ), __( 'Music Box', 'flash-album-gallery' ), 'FlAG Manage music', 'flag-music-box', array(
			&$this,
			'show_menu'
		) );
		add_submenu_page( 'flag-overview', __( 'FlAG Video Box', 'flash-album-gallery' ), __( 'Video Box', 'flash-album-gallery' ), 'FlAG Manage video', 'flag-video-box', array(
			&$this,
			'show_menu'
		) );
		add_submenu_page( 'flag-overview', __( 'FlAG Banner Box', 'flash-album-gallery' ), __( 'Banner Box', 'flash-album-gallery' ), 'FlAG Manage banners', 'flag-banner-box', array(
			&$this,
			'show_menu'
		) );
		add_submenu_page( 'flag-overview', __( 'FlAG Manage skins', 'flash-album-gallery' ), __( 'Skins', 'flash-album-gallery' ), 'FlAG Change skin', 'flag-skins', array(
			&$this,
			'show_menu'
		) );
		add_submenu_page( 'flag-overview', __( 'FlAG Change options', 'flash-album-gallery' ), __( 'Options', 'flash-album-gallery' ), 'FlAG Change options', 'flag-options', array(
			&$this,
			'show_menu'
		) );
		add_submenu_page( 'flag-overview', __( 'Flagallery in iframe', 'flash-album-gallery' ), __( 'Iframe', 'flash-album-gallery' ), 'FlAG iFrame page', 'flag-iframe', array(
			&$this,
			'show_menu'
		) );
		if ( flag_wpmu_site_admin() ) {
			add_submenu_page( 'wpmu-admin.php', __( 'GRAND FlaGallery', 'flash-album-gallery' ), __( 'Grand Flagallery', 'flash-album-gallery' ), 'activate_plugins', 'flag-wpmu', array(
				&$this,
				'show_menu'
			) );
		}

		//register the column fields
		$this->register_columns();

	}

	// load the script for the defined page and load only this code	
	function show_menu() {

		global $flag;

		// Set installation date
		if ( empty( $flag->options['installDate'] ) ) {
			$flag->options['installDate'] = time();
			update_option( 'flag_options', $flag->options );
		}

		switch ( $_GET['page'] ) {
			case "flag-manage-gallery" :
				include_once( dirname( __FILE__ ) . '/functions.php' );    // admin functions
				include_once( dirname( __FILE__ ) . '/manage.php' );        // flag_admin_manage_gallery
				// Initate the Manage Gallery page
				$flag->manage_page = new flagManageGallery();
				// Render the output now, because you cannot access a object during the constructor is not finished
				$flag->manage_page->controller();

				break;
			case "flag-music-box" :
				include_once( dirname( __FILE__ ) . '/music-box.php' );    // flag_music_box
				flag_music_controler();
				break;
			case "flag-video-box" :
				include_once( dirname( __FILE__ ) . '/video-box.php' );    // flag_video_box
				flag_video_controler();
				break;
			case "flag-banner-box" :
				include_once( dirname( __FILE__ ) . '/banner-box.php' );    // flag_banner_box
				flag_banner_controler();
				break;
			case "flag-options" :
				include_once( dirname( __FILE__ ) . '/settings.php' );        // flag_admin_options
				flag_admin_options();
				break;
			case "flag-skins" :
				include_once( dirname( __FILE__ ) . '/skins.php' );        // flag_manage_skins
				break;
			case "flag-iframe" :
				include_once( dirname( __FILE__ ) . '/flagframe-tool.php' );        // flagframe-tool
				break;
			case "flag-wpmu" :
				include_once( dirname( __FILE__ ) . '/wpmu.php' );            // flag_wpmu_admin
				flag_wpmu_setup();
				break;
			default :
				include_once( dirname( __FILE__ ) . '/overview.php' );    // flag_admin_overview
				flag_admin_overview();
				break;
		}
	}

	function enqueue_scripts($hook) {
        // no need to go on if it's not a plugin page
        if('admin.php' != $hook && isset($_GET['page']) && in_array($_GET['page'], array('flag-overview', 'flag-manage-gallery', 'flag-music-box', 'flag-video-box', 'flag-banner-box', 'flag-skins', 'flag-options', 'flag-iframe'))){
            global $wp_scripts, $wp_styles;
            foreach($wp_scripts->registered as $handle => $wp_script){
                if(((false !== strpos($wp_script->src, '/plugins/')) || (false !== strpos($wp_script->src, '/themes/'))) && (false === strpos($wp_script->src, 'flash-album-gallery'))){
                    if(in_array($handle, $wp_scripts->queue)){
                        wp_dequeue_script($handle);
                    }
                    wp_deregister_script($handle);
                }
            }
            foreach($wp_styles->registered as $handle => $wp_style){
                if(((false !== strpos($wp_style->src, '/plugins/')) || (false !== strpos($wp_style->src, '/themes/'))) && (false === strpos($wp_style->src, 'flash-album-gallery'))){
                    if(in_array($handle, $wp_styles->queue)){
                        wp_dequeue_style($handle);
                    }
                    wp_deregister_style($handle);
                }
            }
        }

    }

	function load_scripts() {

		wp_register_script( 'flag-ajax', FLAG_URLPATH . 'admin/js/flag.ajax.js', array( 'jquery' ), '1.4.1' );
		wp_localize_script( 'flag-ajax', 'flagAjaxSetup', array(
			'url'        => admin_url( 'admin-ajax.php' ),
			'action'     => 'flag_ajax_operation',
			'operation'  => '',
			'nonce'      => wp_create_nonce( 'flag-ajax' ),
			'ids'        => '',
			'permission' => __( 'You do not have the correct permission', 'flash-album-gallery' ),
			'error'      => __( 'Unexpected Error', 'flash-album-gallery' ),
			'failure'    => __( 'A failure occurred', 'flash-album-gallery' )
		) );
		wp_register_script( 'flag-progressbar', FLAG_URLPATH . 'admin/js/flag.progressbar.js', array( 'jquery' ), '1.0.1' );

		if ( isset( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case 'flag-overview' :
					wp_enqueue_script( 'postbox' );
				case "flag-manage-gallery" :
					print "<script type='text/javascript' src='" . FLAG_URLPATH . "admin/js/tabs.js'></script>\n";

					wp_enqueue_style( 'jquery-ui-smoothness', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.min.css', array(), '1.10.4', 'screen' );
					wp_enqueue_script( 'jquery-ui-full', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js', array( 'jquery' ), '1.10.4' );

					wp_enqueue_script( 'jquery-ui-droppable' );

					wp_enqueue_script( 'multifile', FLAG_URLPATH . 'admin/js/jquery.MultiFile.js', array( 'jquery' ), '1.4.6' );

					wp_enqueue_script( 'flag-plupload', FLAG_URLPATH . 'admin/js/plupload/plupload.full.min.js', array(
						'jquery',
						'jquery-ui-full'
					), '2.1.1' );

					wp_enqueue_style( 'jquery.ui.plupload', FLAG_URLPATH . 'admin/js/plupload/jquery.ui.plupload/css/jquery.ui.plupload.css', array( 'jquery-ui-smoothness' ), '2.1.1', 'screen' );
					wp_enqueue_script( 'jquery.ui.plupload', FLAG_URLPATH . 'admin/js/plupload/jquery.ui.plupload/jquery.ui.plupload.min.js', array(
						'flag-plupload',
						'jquery-ui-full'
					), '2.1.1' );


					wp_enqueue_script( 'dataset', FLAG_URLPATH . 'admin/js/jquery.dataset.js', array( 'jquery' ), '0.1.0' );
					wp_enqueue_script( 'postbox' );
					wp_enqueue_script( 'flag-ajax' );
					wp_enqueue_script( 'flag-progressbar' );
					add_thickbox();
					break;
				case "flag-music-box" :
					wp_enqueue_script( 'swfobject' );
					wp_enqueue_script( 'thickbox' );
					break;
				case "flag-video-box" :
					wp_enqueue_script( 'swfobject' );
					wp_enqueue_script( 'thickbox' );
					break;
				case "flag-banner-box" :
					wp_enqueue_script( 'thickbox' );
					break;
				case "flag-options" :
					print "<script type='text/javascript' src='" . FLAG_URLPATH . "admin/js/tabs.js'></script>\n";
					break;
				case "flag-skins" :
					wp_enqueue_script( 'thickbox' );
                    // Add the color picker assets
                    wp_enqueue_style( 'wp-color-picker' );
                    wp_enqueue_script( 'wp-color-picker' );
					print "<script type='text/javascript' src='" . FLAG_URLPATH . "admin/js/tabs.js'></script>\n";
					break;
			}
		}
	}

	function load_styles() {

		if ( isset( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case 'flag-overview' :
					wp_enqueue_style( 'flagadmin', FLAG_URLPATH . 'admin/css/flagadmin.css', false, '5.0.0', 'screen' );
					wp_admin_css( 'css/dashboard' );
					break;
				case "flag-options" :
					wp_enqueue_style( 'farbtastic' );
				case "flag-manage-gallery" :
					wp_enqueue_style( 'flagtabs', FLAG_URLPATH . 'admin/css/tabs.css', false, '5.0.0', 'screen' );
				case "flag-music-box" :
				case "flag-video-box" :
				case "flag-banner-box" :
					wp_enqueue_style( 'thickbox' );
					wp_enqueue_style( 'flagadmin', FLAG_URLPATH . 'admin/css/flagadmin.css', false, '5.0.0', 'screen' );
					break;
				case "flag-skins" :
					wp_enqueue_style( 'thickbox' );
					wp_enqueue_style( 'farbtastic' );
					wp_enqueue_style( 'flagtabs', FLAG_URLPATH . 'admin/css/tabs.css', false, '5.0.0', 'screen' );
					wp_enqueue_style( 'flagadmin', FLAG_URLPATH . 'admin/css/flagadmin.css', false, '5.0.0', 'screen' );
					wp_admin_css( 'css/dashboard' );
					break;
			}
		}
	}

	function show_help( $help, $screen ) {

		$link = '';
		// menu title is localized...
		$i18n = strtolower( _n( 'Gallery', 'Galleries', 1, 'flash-album-gallery' ) );

		switch ( $screen ) {
			case 'toplevel_page_' . 'flag-overview' :
			case "{$i18n}_page_flag-manage-gallery" :
			case "flag-manage-gallery":
			case "flag-manage-images":
			case "{$i18n}_page_flag-skins" :
			case "{$i18n}_page_flag-options" :
				$link = '<a href="http://codeasily.com/wordpress-plugins/flag/" target="_blank">CodEasily.com</a>';
				break;
		}

		if ( ! empty( $link ) ) {
			$help = '<h5>' . __( 'Get help with Grand Flagallery', 'flash-album-gallery' ) . '</h5>';
			$help .= '<div class="metabox-prefs">';
			$help .= $link;
			$help .= "</div>\n";
			$help .= '<h5>' . __( 'More Help & Info', 'flash-album-gallery' ) . '</h5>';
			$help .= '<div class="metabox-prefs">';
			$help .= '<a href="http://codeasily.com/community/topic/video-tutorials/" target="_blank">' . __( 'Grand Flagallery Video Tutorial', 'flash-album-gallery' ) . '</a>';
			$help .= ' | <a href="http://codeasily.com/community/forum/flagallery-wordpress-plugin/" target="_blank">' . __( 'Forum', 'flash-album-gallery' ) . '</a>';
			$help .= ' | <a href="http://codeasily.com/community/topic/language-files/" target="_blank">' . __( 'Get your language pack', 'flash-album-gallery' ) . '</a>';
			$help .= ' | <a href="http://codeasily.com/portfolio/grand-flagallery-skins/" target="_blank">' . __( 'Skins for Grand Flagallery', 'flash-album-gallery' ) . '</a>';
			$help .= "</div>\n";
		}

		return $help;
	}

	function edit_screen_meta( $screen ) {

		// menu title is localized, so we need to change the toplevel name
		$i18n = strtolower( _n( 'Gallery', 'Galleries', 1, 'flash-album-gallery' ) );

		switch ( $screen ) {
			case "{$i18n}_page_flag-manage-gallery" :
				// we would like to have screen option only at the manage images / gallery page
				if ( isset ( $_POST['sortGallery'] ) ) {
					//$screen = $screen;
				} else if ( ( $_GET['mode'] == 'edit' ) || isset ( $_POST['backToGallery'] ) ) {
					$screen = 'flag-manage-images';
				} else if ( ( $_GET['mode'] == 'sort' ) ) {
					//$screen = $screen;
				} else {
					$screen = 'flag-manage-gallery';
				}
				break;
		}

		return $screen;
	}

	function register_column_headers( $screen, $columns ) {
		global $_wp_column_headers;

		if ( ! isset( $_wp_column_headers ) ) {
			$_wp_column_headers = array();
		}

		$_wp_column_headers[ $screen ] = $columns;
	}

	function register_columns() {
		include_once( dirname( __FILE__ ) . '/manage-images.php' );
		$this->register_column_headers( 'flag-manage-images', flag_manage_gallery_columns() );
	}

    function wp_flag_ins_button() {

        if( strpos($_SERVER['REQUEST_URI'], 'post.php')
            || strstr($_SERVER['PHP_SELF'], 'page-new.php')
            || strstr($_SERVER['PHP_SELF'], 'page.php')
            || strstr($_SERVER['PHP_SELF'], 'post-new.php') )
        {
            ?>
            <script type="text/javascript">
                <!--
                function bind_resize() {
                    if(!window.flag_bind_resize){
                        jQuery(window).bind("resize", tb_position);
                    }
                    window.flag_bind_resize = true;
                }
                //-->
            </script>
            <?php
        }
    }

}

function flag_wpmu_site_admin() {
	// Check for site admin
	if ( function_exists( 'is_site_admin' ) ) {
		if ( is_super_admin() ) {
			return true;
		}
	}

	return false;
}
