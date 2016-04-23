<?php
if ( !defined('__THEME__') ){
	// Define helper constants
	$get_theme_name = explode( '/wp-content/themes/', get_template_directory() );
	define( '__THEME__', next($get_theme_name) );
}

/**
 * Variables
 */
require_once locate_template('/lib/defines.php');
require_once locate_template('/defines.php');

/**
 * Roots includes
 */

require_once locate_template('/lib/classes.php');		// Utility functions
require_once locate_template('/lib/utils.php');			// Utility functions
require_once locate_template('/lib/init.php');			// Initial theme setup and constants
require_once locate_template('/lib/config.php');		// Configuration
require_once locate_template('/lib/activation.php');	// Theme activation
require_once locate_template('/lib/cleanup.php');		// Cleanup
require_once locate_template('/lib/nav.php');			// Custom nav modifications
require_once locate_template('/lib/rewrites.php');		// URL rewriting for assets
require_once locate_template('/lib/htaccess.php');		// HTML5 Boilerplate .htaccess
require_once locate_template('/lib/widgets.php');		// Sidebars and widgets
require_once locate_template('/lib/scripts.php');		// Scripts and stylesheets
require_once locate_template('/lib/customizer.php');	// Custom functions
require_once locate_template('/lib/shortcodes.php');	// Utility functions
require_once locate_template('/lib/woocommerce-hook.php');	// Utility functions
require_once locate_template('/lib/plugins/currency-converter/currency-converter.php'); // currency converter
require_once locate_template('/lib/less.php');			// Custom functions
require_once locate_template('/lib/visual-map.php');

function sv_change_sku_value( $sku, $product ) {

    // Change the generated SKU to use the product's post ID instead of the slug
    $sku = $product->get_post_data()->ID;
    return 'FSKU'.$sku;
}
add_filter( 'wc_sku_generator_sku', 'sv_change_sku_value', 10, 2 );

function new_mail_from($old) {
 return 'fashionretail172014@yahoo.com';
}
function new_mail_from_name($old) {
 return 'Fashion Shoppee';
}
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');

add_filter( 'the_title', 'shorten_woo_product_title', 10, 2 );
function shorten_woo_product_title( $title, $id ) {
    if ( is_shop() && get_post_type( $id ) === 'product' ) {
        return substr( $title, 0, 100 ).((strlen($title) > 100)?'...':'');
    }
    return $title;
}
 

 