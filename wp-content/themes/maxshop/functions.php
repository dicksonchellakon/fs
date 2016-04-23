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
 
/*remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );


add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 40 );*/

/*Remove product categories from shop page*/
add_action( 'pre_get_posts', 'custom_pre_get_posts_query' );

function custom_pre_get_posts_query( $q ) {

	if ( ! $q->is_main_query() ) return;
	if ( ! $q->is_post_type_archive() ) return;
	
	if ( ! is_admin() && is_shop() ) {

		$q->set( 'tax_query', array(array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => array( 'corporate-gifts' ), // Don't display products in the knives category on the shop page
			'operator' => 'NOT IN'
		)));
	
	}

	remove_action( 'pre_get_posts', 'custom_pre_get_posts_query' );

}

/*Exclude a category from the WooCommerce category widget*/
add_filter( 'woocommerce_product_categories_widget_args', 'woo_product_cat_widget_args' );

function woo_product_cat_widget_args( $cat_args ) {
	
	$cat_args['exclude'] = array('72');
	
	return $cat_args;
}

function fashionshoppee_scripts_styles() {

    wp_enqueue_style( 'fs-style', get_template_directory_uri() . '/style.css' ); 

}

add_action( 'wp_enqueue_scripts', 'fashionshoppee_scripts_styles' );

/**
* WooCommerce: Show only one custom product attribute above Add-to-cart button on single product page.
*/
function isa_woo_get_one_pa(){
 
    // Edit below with the title of the attribute you wish to display
    $desired_att = array('Brand', 'Color');
  
    global $product;
    $attributes = $product->get_attributes();
     
    if ( ! $attributes ) {
        return;
    }
     
    $out = '';
  foreach ( $desired_att as $attrributeValue ) {
    foreach ( $attributes as $attribute ) {
         
        if ( $attribute['is_taxonomy'] ) {
         
            // sanitize the desired attribute into a taxonomy slug
            $tax_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $attrributeValue)));
         
            // if this is desired att, get value and label
             
            if ( $attribute['name'] == 'pa_' . $tax_slug ) {
             
                $terms = wp_get_post_terms( $product->id, $attribute['name'], 'all' );
                 
                // get the taxonomy
                $tax = $terms[0]->taxonomy;
                 
                // get the tax object
                $tax_object = get_taxonomy($tax);
                 
                // get tax label
                if ( isset ($tax_object->labels->name) ) {
                    $tax_label = $tax_object->labels->name;
                } elseif ( isset( $tax_object->label ) ) {
                    $tax_label = $tax_object->label;
                }
                 
                foreach ( $terms as $term ) {
      
                    $out .= $tax_label . ': ';
                    $out .= $term->name . '<br />';
                      
                }           
             
            } // our desired att
             
        } else {
         
            // for atts which are NOT registered as taxonomies
             
            // if this is desired att, get value and label
            if ( $attribute['name'] == $attrributeValue ) {
                $out .= $attribute['name'] . ': ';
                $out .= $attribute['value'];
            }
        }       
         
     
    }
    }
     
    echo $out;
}
add_action('woocommerce_single_product_summary', 'isa_woo_get_one_pa');

function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/fs-login-image.png);
            
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Your shopping ends here.';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function my_loginfooter() { ?>
    <p style="text-align: center; margin-top: 1em;">
    <a style="color: #4da28f; text-decoration: none;" href="mailto:customercare@fashionshoppee.in">For any support contact us.
        </a>
    </p>
<?php }
add_action('login_footer','my_loginfooter');

function admin_login_redirect( $redirect_to, $request, $user )
{
global $user;
if( isset( $user->roles ) && is_array( $user->roles ) ) {
if( in_array( "administrator", $user->roles ) ) {
return $redirect_to;
} else {
return home_url();
}
}
else
{
return $redirect_to;
}
}
add_filter("login_redirect", "admin_login_redirect", 10, 3);

function login_checked_remember_me() {
add_filter( 'login_footer', 'rememberme_checked' );
}
add_action( 'init', 'login_checked_remember_me' );

function rememberme_checked() {
echo "<script>document.getElementById('rememberme').checked = true;</script>";
}

function modify_contact_methods($profile_fields) {

    // Add new fields
	$profile_fields['mobile'] = 'Mobile Phone No';
	
	// Remove old fields
	//unset($profile_fields['aim']);

	return $profile_fields;

}
add_filter('user_contactmethods', 'modify_contact_methods');
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 24;' ), 20 );
add_filter( 'woocommerce_subcategory_count_html', 'hide_category_count' );
function hide_category_count() {
	// No count
}

/**
 * Optimize WooCommerce Scripts
 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
 */
add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );

function child_manage_woocommerce_styles() {
	//remove generator meta tag
	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

	//first check that woo exists to prevent fatal errors
	if ( function_exists( 'is_woocommerce' ) ) {
		//dequeue scripts and styles
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			wp_dequeue_style( 'woocommerce_frontend_styles' );
			wp_dequeue_style( 'woocommerce_fancybox_styles' );
			wp_dequeue_style( 'woocommerce_chosen_styles' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_script( 'wc_price_slider' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-cart' );
			wp_dequeue_script( 'wc-chosen' );
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'jquery-blockui' );
			wp_dequeue_script( 'jquery-placeholder' );
			wp_dequeue_script( 'fancybox' );
			wp_dequeue_script( 'jqueryui' );
		}
	}

}


/**
 * @snippet       WooCommerce Display Sale Saving Amount with Percentage
 * @author        Dickson CHellakon
 * @compatible    WooCommerce 2.4.7
 */
add_filter( 'woocommerce_sale_price_html', 'woocommerce_custom_sales_price', 10, 2 );
function woocommerce_custom_sales_price( $price, $product ) {
    if(is_single()) {
      $saved = wc_price( $product->regular_price - $product->sale_price );
      $percentage = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
      return $price . sprintf( __('<span style="display:block">You Save: %s (%s) </span>', 'woocommerce' ), $saved, $percentage.'%' );
    }
    else{
        return $price;
    }
}

/**
 * @snippet       WooCommerce Disable Payment Gateway for a Specific PinCode
 * @author        Dickson CHellakon
 * @compatible    WooCommerce 2.4.7
 */
 
function payment_gateway_disableby_pincode( $available_gateways ) {
  global $table_prefix, $wpdb, $woocommerce;

  $isPincodeExist = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM ".$table_prefix."check_pincode_p WHERE pincode = %d" , $woocommerce->customer->shipping_postcode ) );

  if ( isset( $available_gateways['ccavenue'] ) && !($woocommerce->customer->shipping_postcode > '600000' && $woocommerce->customer->shipping_postcode < '600121') ) {
    unset( $available_gateways['ccavenue'] );
  }
  if ( isset( $available_gateways['cod'] ) && !($isPincodeExist) ) {
    unset( $available_gateways['cod'] );
  }
  return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'payment_gateway_disableby_pincode' );