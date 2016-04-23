<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woothemes.com/document/measurement-price-calculator/ for more information.
 *
 * @package   WC-Measurement-Price-Calculator/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2013, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Measurement Price Calculator Product Loop View Class
 *
 * @since 3.0
 */
class WC_Price_Calculator_Product_Loop {


	/**
	 * Construct and initialize the class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// make loop 'add to cart' link to the product page even for simple pricing calcuator products
		add_filter( 'add_to_cart_url',                   array( $this, 'add_to_cart_url' ) );
		add_filter( 'add_to_cart_text',                  array( $this, 'add_to_cart_text' ) );
		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'loop_add_to_cart_link' ), 10, 3 );  // WC >= 2.0
		} else {
			add_action( 'woocommerce_before_template_part', array( $this, 'change_product_type' ), 10, 3 );
			add_action( 'woocommerce_after_template_part',  array( $this, 'restore_product_type' ), 10, 3 );
		}

	}


	/** Frontend methods ******************************************************/


	/**
	 * Modify the 'add to cart' url for pricing calculator products to simply link to
	 * the product page, just like a variable product.  This is because the
	 * customer must supply whatever product measurements they require.
	 *
	 * @since 3.0
	 * @param string $link the URL
	 * @return string the URL
	 */
	public function add_to_cart_url( $link ) {
		global $product;

		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			$link = get_permalink( $product->id );
		}

		return $link;
	}


	/**
	 * Modify the 'add to cart' text for pricing calculator products to display
	 * 'Select options', just like a variable product.  This is because the
	 * customer must supply whatever product measurements they require.
	 *
	 * @since 3.0
	 * @param string $label the 'add to cart' label
	 * @return string the 'add to cart' label
	 */
	public function add_to_cart_text( $label ) {
		global $product;

		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			$label = __( 'Select options', WC_Measurement_Price_Calculator::TEXT_DOMAIN );
		}

		return $label;
	}


	/**
	 * Modify the loop 'add to cart' button class for price calculator products to
	 * link directly to the product page like a variable product.  This is
	 * because the customer must supply whatever product measurements they
	 * require.
	 *
	 * This is for WooCommerce 2.0.0+ support
	 *
	 * @since 3.0
	 * @param string $tag the 'add to cart' button tag html
	 * @param string $link the 'add to cart' url
	 * @param string $label the 'add to cart' label
	 * @return string the product type
	 */
	public function loop_add_to_cart_link( $tag, $link, $label ) {
		global $product;

		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			// otherwise, for simple type products, the page javascript would take over and
			//  try to do an ajax add-to-cart, when really we need the customer to visit the
			//  product page to supply whatever measurements they require
			$tag = str_replace( 'product_type_simple', 'product_type_variable', $tag );
		}

		return $tag;
	}


	/**
	 * Changes the product type to 'variable' in the product loop if the
	 * current product is a simple type with the pricing calculator enabled,
	 * so that clicking the 'add to cart' button brings the client to the
	 * product page (variable behavior) rather than performing an AJAX add
	 * to cart (simple behavior).  This is so the product pricing measurements
	 * can be supplied.
	 *
	 * Called before a WooCommerce template file is included.
	 *
	 * This is for WooCommerce 1.6.6 support
	 *
	 * @since 3.0
	 * @param string $template_name the template file relative to the
	 *        woocommerce/templates/ directory
	 * @param string $template_path path to template override directory within
	 *        a theme.  Ie 'woocommerce/'
	 * @param string $located the full path to the template file to include
	 */
	public function change_product_type( $template_name, $template_path, $located ) {
		global $product;

		if ( 'loop/add-to-cart.php' == $template_name && WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) && $product->is_type( 'simple' ) ) {
			$product->WC_Price_Calculator_Product_type_original = $product->product_type;
			$product->product_type = 'variable';
		}
	}


	/**
	 * Restores the original product type if it was altered in the product
	 * loop prior to the 'add to cart' template.
	 *
	 * This is called after a WooComerce template file is included
	 *
	 * This is for WooCommerce 1.6.6 support
	 *
	 * @since 3.0
	 * @param string $template_name the template file relative to the
	 *        woocommerce/templates/ directory
	 * @param string $template_path path to template override directory within
	 *        a theme.  Ie 'woocommerce/'
	 * @param string $located the full path to the template file to include
	 */
	public function restore_product_type( $template_name, $template_path, $located ) {
		global $product;

		if ( 'loop/add-to-cart.php' == $template_name && WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) && isset( $product->WC_Price_Calculator_Product_type_original ) ) {
			$product->product_type = $product->WC_Price_Calculator_Product_type_original;
			unset( $product->WC_Price_Calculator_Product_type_original );
		}
	}
}
