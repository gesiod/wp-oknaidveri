<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'GiftWrapperSettings' ) ) :

class GiftWrapperSettings {

    public static function init() {
    
        // settings link on the plugins listing page
    	add_filter( 'plugin_action_links_' . GIFT_PLUGIN_BASE_FILE,     array( __CLASS__, 'add_settings_link' ), 10, 1 );
        // Add settings SECTION under Woocommerce->Settings->Products
    	add_filter( 'woocommerce_get_sections_products',                array( __CLASS__, 'add_section' ), 10, 1 );
    	// Add settings to the section we created with add_section()
		add_filter( 'woocommerce_get_settings_products',                array( __CLASS__, 'settings' ), 10, 2);

    }
    
	/*
    * Add settings link to WP plugin listing
    */
	public static function add_settings_link( $links ) {

		$settings = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wc-settings&tab=products&section=wcgiftwrapper' ), __( 'Settings', 'woocommerce-gift-wrapper' ) );
		array_unshift( $links, $settings );
		return $links;

	}

 	/*
    * Add settings SECTION under Woocommerce->Settings->Products
    * @param array $sections
    * @return array
    */
    public static function add_section( $sections ) {
    
		$sections['wcgiftwrapper'] = __( 'Gift Wrapping', 'woocommerce-gift-wrapper' );
		return $sections;

	}

	/*
	* Add settings to the section we created with add_section()
	* @param array Settings
	* @param string Current Section
	* @return array
    */
	public static function settings( $settings, $current_section ) {

 		if ( $current_section == 'wcgiftwrapper' ) {
			$category_args = array(
				'orderby' 			=> 'id',
				'order' 			=> 'ASC',
				'taxonomy' 			=> 'product_cat',
				'hide_empty' 		=> '0',
				'hierarchical' 		=> '1'
			);

			$gift_cats = array();
			$gifts_categories = ( $gifts_categories = get_categories( $category_args ) ) ? $gifts_categories : array();
			foreach ( $gifts_categories as $gifts_category )
				$gift_cats[ $gifts_category->term_id ] = $gifts_category->name;

			$settings_slider = array();
 
			$settings_slider[] = array( 
				'id' 				=> 'wcgiftwrapper',
				'name' 				=> __( 'Gift Wrapping Options', 'woocommerce-gift-wrapper' ), 
				'type' 				=> 'title', 
				'desc' 				=> '<strong>1.</strong> ' . sprintf(__( 'Start by <a href="%s" target="_blank">adding at least one product</a> called "Gift Wrapping" or something similar.', 'woocommerce-gift-wrapper' ), admin_url( 'post-new.php?post_type=product' ) ) . '<br /><strong>2.</strong> ' . __( 'Create a unique product category for this/these gift wrapping product(s), and add them to this category.', 'woocommerce-gift-wrapper' ) . '<br /><strong>3.</strong> ' . __( 'Then consider the options below.', 'woocommerce-gift-wrapper' ),
			);
 
			$settings_slider[] = array(
				'id'       			=> 'giftwrap_display',
				'name'     			=> __( 'Where to Show Gift Wrapping', 'woocommerce-gift-wrapper' ),
				'desc_tip' 			=> __( 'Choose where to show gift wrap options to the customer on the cart page. You may choose more than one.', 'woocommerce-gift-wrapper' ),
				'type'     			=> 'multiselect',
				'default'         	=> 'after_coupon',
				'options'     		=> array(
					'after_coupon'		=> __( 'Under Coupon Field in Cart', 'woocommerce-gift-wrapper' ),
                    'before_cart'      => __( 'Before Cart', 'woocommerce-gift-wrapper' ),
					'after_cart'		=> __( 'Above Totals Field in Cart', 'woocommerce-gift-wrapper' ),
					'before_checkout'	=> __( 'Before Checkout', 'woocommerce-gift-wrapper' ),
				),
				'css'      			=> 'min-width:300px;',
			);

			$settings_slider[]	= array(
				'id'				=> 'giftwrap_category_id',
				'title'           	=> __( 'Gift Wrap Category', 'woocommerce-gift-wrapper' ),
				'desc_tip'			=> __( 'Define the category which holds your gift wrap product(s).', 'woocommerce-gift-wrapper' ),
				'type'            	=> 'select',
				'default'         	=> '',
				'options'         	=> $gift_cats,
				'class'           	=> 'chosen_select',				
				'custom_attributes'	=> array(
					'data-placeholder' => __( 'Define a Category', 'woocommerce-gift-wrapper' )
				),
			);

			$settings_slider[] = array(
				'id'       			=> 'giftwrap_show_thumb',
				'name'     			=> __( 'Show Gift Wrap Thumbs in Cart', 'woocommerce-gift-wrapper' ),
				'desc_tip' 			=> __( 'Should gift wrap product thumbnail images be visible in the cart?', 'woocommerce-gift-wrapper' ),
				'type'     			=> 'select',
				'default'         	=> 'yes',
				'options'     		=> array(
					'yes'	=> __( 'Yes', 'woocommerce-gift-wrapper' ),
					'no'	=> __( 'No', 'woocommerce-gift-wrapper' ),
				),
				'css'      			=> 'min-width:100px;',
			);

			$settings_slider[] = array(
				'id'       			=> 'giftwrap_number',
				'name'     			=> __( 'Allow more than one gift wrap product in cart?', 'woocommerce-gift-wrapper' ),
				'desc_tip' 			=> __( 'If yes, customers can buy more than one gift wrapping product in one order.', 'woocommerce-gift-wrapper' ),
				'type'     			=> 'select',
				'default'         	=> 'no',
				'options'     		=> array(
					'yes'				=> __( 'Yes', 'woocommerce-gift-wrapper' ),
					'no'				=> __( 'No', 'woocommerce-gift-wrapper' ),
				),
				'css'      			=> 'min-width:100px;',
			);

			$settings_slider[] = array(
				'id'       			=> 'giftwrap_modal',
				'name'     			=> __( 'Should Gift Wrap option open in pop-up?', 'woocommerce-gift-wrapper' ),
				'desc_tip' 			=> __( 'If checked, there will be a link ("header") in the cart, which when clicked will open a window for customers to choose gift wrapping options. It can be styled and might be a nicer option for your site. NOTE: modal does not work with the Avada theme.', 'woocommerce-gift-wrapper' ),
				'type'     			=> 'select',
				'default'         	=> 'yes',
				'options'     		=> array(
					'yes'				=> __( 'Yes', 'woocommerce-gift-wrapper' ),
					'no'				=> __( 'No', 'woocommerce-gift-wrapper' ),
				),
				'css'      			=> 'min-width:100px;',
			);

			$settings_slider[] = array(
				'id'       			=> 'giftwrap_details',
				'name'     			=> __( 'Gift Wrap Details', 'woocommerce-gift-wrapper' ),
				'desc_tip' 			=> __( 'Optional text to give any details or conditions of your gift wrap offering. This text is not translatable by Wordpress i18n.', 'woocommerce-gift-wrapper' ),
				'type'     			=> 'textarea',
				'default'         	=> '',
				'css'      			=> 'min-width:450px;',
			);

			$settings_slider[] = array(
				'id'       			=> 'giftwrap_textarea_limit',
				'name'     			=> __( 'Textarea Character Limit', 'woocommerce-gift-wrapper' ),
				'desc_tip' 			=> __( 'How many characters your customer can type when creating their own note for giftwrapping. Defaults to 1000 characters; lower this number if you want more brief comments from your customers.', 'woocommerce-gift-wrapper' ),
				'type'     			=> 'number',
 				'default'         	=> 1000,
			);
		
			$settings_slider[] = array(
				'id' => 'wcgiftwrapper',
				'type' => 'sectionend',
			);
 
		    return $settings_slider;
	
		} else {
		
			return $settings;
			
		}
	
	}
	
}
endif;

GiftWrapperSettings::init();