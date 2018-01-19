<?php
/**
 * Plugin Name: Meta slider and carousel with lightbox
 * Plugin URI: http://www.wponlinesupport.com/
 * Description: Plugin add a gallery meta box in your post, page and create a Image gallery menu tab. Display with a lightbox. 
 * Author: WP Online Support 
 * Text Domain: meta-slider-and-carousel-with-lightbox
 * Domain Path: /languages/
 * Version: 1.1.2
 * Author URI: http://www.wponlinesupport.com/
 *
 * @package WordPress
 * @author SP Technolab
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !defined( 'WP_IGSP_VERSION' ) ) {
	define( 'WP_IGSP_VERSION', '1.1.2' ); // Version of plugin
}
if( !defined( 'WP_IGSP_DIR' ) ) {
    define( 'WP_IGSP_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if( !defined( 'WP_IGSP_URL' ) ) {
    define( 'WP_IGSP_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if( !defined( 'WP_IGSP_POST_TYPE' ) ) {
    define( 'WP_IGSP_POST_TYPE', 'wp_igsp_gallery' ); // Plugin post type
}
if( !defined( 'WP_IGSP_META_PREFIX' ) ) {
    define( 'WP_IGSP_META_PREFIX', '_wp_igsp_' ); // Plugin metabox prefix
}

/**
 * Load Text Domain
 * This gets the plugin ready for translation
 * 
 * @package Meta slider and carousel with lightbox
 * @since 1.0.0
 */
function wp_igsp_load_textdomain() {
	load_plugin_textdomain( 'meta-slider-and-carousel-with-lightbox', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action('plugins_loaded', 'wp_igsp_load_textdomain');

/**
 * Activation Hook
 * 
 * Register plugin activation hook.
 * 
 * @package Meta slider and carousel with lightbox
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'wp_igsp_install' );

/**
 * Deactivation Hook
 * 
 * Register plugin deactivation hook.
 * 
 * @package Meta slider and carousel with lightbox
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'wp_igsp_uninstall');

/**
 * Plugin Setup (On Activation)
 * 
 * Does the initial setup,
 * set default values for the plugin options.
 * 
 * @package Meta slider and carousel with lightbox
 * @since 1.0.0
 */
function wp_igsp_install() {
    
    // Register post type function
    wp_igsp_register_post_type();

    // IMP need to flush rules for custom registered post type
    flush_rewrite_rules();
}

/**
 * Plugin Setup (On Deactivation)
 * 
 * Delete  plugin options.
 * 
 * @package Meta slider and carousel with lightbox
 * @since 1.0.0
 */
function wp_igsp_uninstall() {
    
    // IMP need to flush rules for custom registered post type
    flush_rewrite_rules();
}

// Functions File
require_once( WP_IGSP_DIR . '/includes/wp-igsp-functions.php' );

// Plugin Post Type File
require_once( WP_IGSP_DIR . '/includes/wp-igsp-post-types.php' );

// Script File
require_once( WP_IGSP_DIR . '/includes/class-wp-igsp-script.php' );

// Admin Class File
require_once( WP_IGSP_DIR . '/includes/admin/class-wp-igsp-admin.php' );

// Shortcode File
require_once( WP_IGSP_DIR . '/includes/shortcode/wp-igsp-meta-gallery-slider.php' );
require_once( WP_IGSP_DIR . '/includes/shortcode/wp-igsp-meta-gallery-carousel.php' );

// How it work file, Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( WP_IGSP_DIR . '/includes/admin/igsp-how-it-work.php' );
}