<?php

/* 
Plugin Name: Onemi - WooCommerce Payment Gateway

Description: Extends WooCommerce by Adding the Onemi Payment Gateway.
Version: 1.0
Author: Dickson Chellakon

 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
// Include our Gateway Class and Register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'onemi_init', 0 );

function onemi_init() {
    // If the parent WC_Payment_Gateway class doesn't exist
    // it means WooCommerce is not installed on the site
    // so do nothing
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
	
    // If we made it this far, then include our Gateway Class
    include_once( 'woocommerce-onemi.php' );

    // Now that we have successfully included our class,
    // Lets add it too WooCommerce
    add_filter( 'woocommerce_payment_gateways', 'add_onemi_gateway' );
    function add_onemi_gateway( $methods ) {
        $methods[] = 'onemi_payment_gateway';
        return $methods;
    }
}


// Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'onemi_action_links' );
function onemi_action_links( $links ) {
    $plugin_links = array(
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'onemi_payment_gateway' ) . '</a>',
    );

    // Merge our new link with the default ones
    return array_merge( $plugin_links, $links );	
}