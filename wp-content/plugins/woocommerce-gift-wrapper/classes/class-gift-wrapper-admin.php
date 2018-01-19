<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'GiftWrapperAdmin' ) ) :

class GiftWrapperAdmin {

    public static function init() {
        // Update the order meta with field value
		add_action( 'woocommerce_checkout_update_order_meta',   			array( __CLASS__, 'update_order_meta' ), 10, 1 );
		// Display field value on the order edit page
		add_action( 'woocommerce_admin_order_data_after_billing_address',   array( __CLASS__, 'display_admin_order_meta'), 10, 1 );
		// Display field vlue on the thank you page
		add_action ( 'woocommerce_thankyou',                                array( __CLASS__, 'display_thank_you_meta'), 11, 1 );
        // Add the field to order emails
		add_filter( 'woocommerce_email_order_meta_keys',					array( __CLASS__, 'order_meta_keys'), 10, 1 );
    }
    
    /*
    * Update the order meta with field value 
    * @param int order ID
    * @return void
    */
	public static function update_order_meta( $order_id ) {

		if ( isset( WC()->session->gift_wrap_notes ) ) {
			if ( WC()->session->gift_wrap_notes !='' ) {
				update_post_meta( $order_id, '_gift_wrap_notes', sanitize_text_field( WC()->session->gift_wrap_notes ) );
			}	
            WC()->session->__unset( 'gift_wrap_notes' );
            WC()->session->__unset( 'gift_wrap_set' );
		}

	}
	
    /*
 	* Display field value on the order edit page
 	* @param array Order
	* @return void
 	*/
	public static function display_admin_order_meta( $order ) {
	
	    if ( self::version_check( '3.0' ) === TRUE ) {
    		if ( get_post_meta( $order->get_id(), '_gift_wrap_notes', true ) !== '' ) {
        		echo '<p><strong>' . __( 'Gift Wrap Note', 'woocommerce-gift-wrapper' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_gift_wrap_notes', true ) . '</p>';
    	    }
    	} else {
            if ( get_post_meta( $order->id, '_gift_wrap_notes', true ) !== '' ) {
    		    echo '<p><strong>'.__( 'Gift Wrap Note', 'wc-gift-wrapper' ).':</strong> ' . get_post_meta( $order->id, '_gift_wrap_notes', true ) . '</p>';
    	    }
    	}
    	
	}

	/*
 	* Display field value on the thank you page
 	* @param string Order ID
	* @return void
 	*/	
	public static function display_thank_you_meta( $order_id ) {
	
        if ( get_post_meta( $order_id, '_gift_wrap_notes', true ) !== '' ) {
    		echo '<h3>' . __( 'Gift Wrap Note', 'woocommerce-gift-wrapper' ) . ':</h3><p>' . get_post_meta( $order_id, '_gift_wrap_notes', true ) . '</p>';
    	}	}
	
	/*
	* Add the field to order emails
 	* @param array Keys
	* @return array
 	*/ 
	public static function order_meta_keys( $keys ) {

		$keys[ __( 'Gift Wrap Note', 'woocommerce-gift-wrapper' ) ] = '_gift_wrap_notes';
		return $keys;

	}
	
	/*
	* Check if version of Woo > 3.0
	* @param string Woo version
	* @return bool
 	*/ 
	public static function version_check( $version = '3.0' ) {
        if ( class_exists( 'WooCommerce' ) ) {
            if ( version_compare( WC()->version, $version, ">=" ) ) {
                return true;
            }
        }
        return false;
    }
	
}
endif;

GiftWrapperAdmin::init();