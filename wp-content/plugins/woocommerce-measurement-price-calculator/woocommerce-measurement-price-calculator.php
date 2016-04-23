<?php
/**
 * Plugin Name: WooCommerce Measurement Price Calculator
 * Plugin URI: http://www.woothemes.com/products/measurement-price-calculator/
 * Description: WooCommerce plugin to provide price and quantity calculations based on product measurements
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com
 * Version: 3.1.2
 * Text Domain: wc-measurement-price-calculator
 * Domain Path: /languages/
 * Tested up to: 3.5
 *
 * Copyright: (c) 2012-2013 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Measurement-Price-Calculator
 * @author    SkyVerge
 * @category  Plugin
 * @copyright Copyright (c) 2012-2013, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'be4679e3d3b24f513b2266b79e859bab', '18735' );

// Check if WooCommerce is active
if ( ! is_woocommerce_active() ) return;

/**
 * The WC_Measurement_Price_Calculator global object
 * @name $wc_measurement_price_calculator
 * @global WC_Measurement_Price_Calculator $GLOBALS['wc_measurement_price_calculator']
 */
$GLOBALS['wc_measurement_price_calculator'] = new WC_Measurement_Price_Calculator();


/**
 * # Main WooCommerce Measurement Price Calculator Class
 *
 * ## Plugin Overview
 *
 * This measurement price calculator plugin actually provides two seemingly
 * related but distinct operational modes:  a quantity calculator, and a
 * pricing calculator.
 *
 * The quantity calculator operates on the configured product dimensions and
 * allows the customer to specify the measurements they require.  This is
 * useful for a merchant that sells a product like boxed tiles which cover a
 * known square footage.  If a box covers 25 sq ft and the customer requires
 * 30 sq ft then the calculator will set the quantity to '2'.
 *
 * The pricing calculator allows the shopkeeper to define a price per unit
 * (ie $/ft) and then the customer supplies the measurements they want.  This
 * is ideal for a merchant that sells a product which is customized to order,
 * such as fabric.  They define a price per unit, and the customer enters the
 * dimensions required, and the quantity.  The customer-supplied measurements
 * are added as order meta data.
 *
 * ## WC 2.0 Support/Pre-2.0 Support
 *
 * This plugin largely works with pre-2.0 woocommerce, with the exception of
 * the product pricing calculator for variations, which can't function unless
 * the class-wc-cart.php file is patched to include the third $variation_id
 * parameter in the woocommerce_add_cart_item_data filter (and the
 * WC_PRE_2_PATCHED constant of this class is set to true) and so is disabled
 * by default.  Also, from the product category pages (loop) the 'add to cart'
 * button for pricing calculator products will call the AJAX method rather than
 * linking directly to the product page for the customer to supply the
 * measurements required for checkout, but at least with that one the shop
 * owner can override the loop/add-to-cart.php template.
 *
 * ## Terminology
 *
 * + `Total measurement` - the total measurement for a product is the length/width/
 *   height for a dimension product, the area for an area/area (LxW) product,
 *   the volume for a volume/volume (AxH)/volume (LxWxH) and the weight for a
 *   weight product.
 *   Related terms: derived measurement, compound measurement
 *
 * + `Common unit` - a single unit "family" used when deriving a compound measurement from
 *   a set of simple measurements.  For instance when finding the Volume (AxH)
 *   the standard units for area and height could be 'sq. ft.' and 'ft' which
 *   when multiplied yield  the known unit 'cu. ft.'.  Without a common unit
 *   you could end up multiplying acres * cm, and what unit does that yield?
 *
 * + `Standard unit` - one of a limited number of units to which all other units
 *   are converted as an intermediate step before converting to a final desired
 *   unit.  This is used to solve the many-to-many problem of converting between
 *   two arbitrary units.  Using a set of standard units means we only need to
 *   know how to convert any arbitrary unit *to* one of the standard units, and
 *   *from* the set of standard units to any other arbitrary unit, which is a
 *   vastly simpler problem than knowing how to convert directly between any
 *   two arbitrary units.  A set of standard units is defined for each system
 *   of measurement (English and SI) so that unit conversion can generally take
 *   place within a single system of measurement, because converting between
 *   systems of measurments results in a loss of precision and accuracy and
 *   requires complex rounding rules to compensate for.
 *
 * ## Admin Considerations
 *
 * ### Global Settings
 *
 * This plugin adds two product measurements to the WooCommerce > Catalog
 * global configuration: area and volume.  Additionally a few new units are
 * added to the core Weight/Dimension measurements
 *
 * ### Product Configuration
 *
 * In the product edit screen a new tab named Measurement is added to the Product
 * Data panel.  This allows the measurement price calculator to be configured
 * for a given product, and the settings here can change other parts of the edit
 * product admin by changing labels, hiding fields, etc.
 *
 * An area and volume measurement field is added to the Shipping tab.
 *
 * ## Frontend
 *
 * ### Cart Item Data
 *
 * The following cart item data is added for pricing calculator products:
 *
 * pricing_item_meta_data => Array(
 *   _price                   => (float) the total product price,
 *   _measurement_needed      => (float) the total measurment needed,
 *   _measurement_needed_unit => (string) the total measurement units,
 *   _quantity                => (int) the quantity added by the customer,
 *   <measurement name>       => (float) measurement amount provided by the customer and depends on the calculator type.  For instance 'length' => 2
 * )
 *
 * ## Database
 *
 * ### Order Item Meta
 *
 * + `<measurement label> (<unit>)` - Visible measurement label and unit for
 *   the pricing calculator product measurements, with associated value supplied
 *   by the customer Ie: "Length (ft): 2"
 *
 * + `Total <measurement> (<unit>)` - Visble total measurement label and unit
 *   for the pricing calculator product measurements, with associated value supplied
 *   by the customer.  Ie: "Total Area (sq. ft.): 4"
 *
 * + `_measurement_data` - Serialized array of pricing calculator product
 *   measurements so that a customized product can be re-ordered:
 *   Array(
 *     <measurement name> => Array(
 *       value => (numeric) the value,
 *       unit  => (string) the unit,
 *     ),
 *     _measurement_needed      => (numeric) the total product measurement,
 *     _measurement_needed_unit => (string) the unit for _measurement_needed
 *   )
 *
 * @since 1.0
 */
class WC_Measurement_Price_Calculator {

	/** Database option version name */
	const DB_VERSION_OPTION_NAME = 'wc_measurement_price_calculator_db_version';

	/** plugin text domain */
	const TEXT_DOMAIN = 'wc-measurement-price-calculator';

	/** plugin version */
	const VERSION = '3.1.2';

	/**
	 * Indicates whether there is a WC < 2.0 that has had the class-wc-cart.php
	 * file patched to include the third $variation_id parameter in the
	 * woocommerce_add_cart_item_data filter, enabling variation support for the
	 * pricing calculator.  This is really for development purposes.
	 */
	const WC_PRE_2_PATCHED = false;

	/** @var string the plugin path */
	private $plugin_path;

	/** @var string the plugin url */
	private $plugin_url;

	/**
	 * The pricing calculator inventory handling class
	 * @var WC_Price_Calculator_Inventory
	 */
	private $pricing_calculator_inventory;

	/**
	 * The pricing calculator cart class
	 * @var WC_Price_Calculator_Cart
	 */
	private $cart;

	/**
	 * The pricing calculator frontend product loop class
	 * @var WC_Price_Calculator_Product_loop
	 */
	private $product_loop;

	/**
	 * The pricing calculator frontend product page class
	 * @var WC_Price_Calculator_Product_page
	 */
	private $product_page;


	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {

		// include required files
		$this->includes();

		// Installation
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) $this->install();

		add_action( 'init',             array( $this, 'load_translation' ) );
		add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );
	}


	/**
	 * Handle localization, WPML compatible
	 */
	public function load_translation() {

		// localization (remember symlinks will break this)
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	}


	/**
	 * Init Measurement Price Calculator when WooCommerce initializes
	 */
	public function woocommerce_init() {

		// include files which depend on WooCommerce being loaded
		// load the pricing calculator inventory handling class if WC 2.0.4 or greater
		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.4" ) >= 0 ) {
			require_once( 'classes/class-wc-price-calculator-inventory.php' );
			$this->pricing_calculator_inventory = new WC_Price_Calculator_Inventory();
		}

		// frontend product loop handling
		$this->product_loop = new WC_Price_Calculator_Product_Loop();

		// frontend product page handling
		$this->product_page = new WC_Price_Calculator_Product_Page();

		// frontend cart handling
		$this->cart = new WC_Price_Calculator_Cart();

		// add pricing table shortcode
		add_shortcode( 'wc_measurement_price_calculator_pricing_table', array( $this, 'pricing_table_shortcode' ) );
	}


	/** Shortcodes ******************************************************/


	/**
	 * Pricing table shortcode: renders a table of product prices
	 *
	 * @since 3.0
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public function pricing_table_shortcode( $atts ) {
		global $woocommerce;

		require( 'classes/shortcodes/class-wc-price-calculator-shortcode-pricing-table.php' );

		return $woocommerce->shortcode_wrapper( array( 'WC_Price_Calculator_Shortcode_Pricing_Table', 'output' ), $atts, array( 'class' => 'wc-measurement-price-calculator' ) );
	}


	/** Helper methods ******************************************************/


	/**
	 * Include required files
	 */
	private function includes() {

		require_once( 'classes/class-wc-price-calculator-cart.php' );
		require_once( 'classes/class-wc-price-calculator-measurement.php' );
		require_once( 'classes/class-wc-price-calculator-product-loop.php' );
		require_once( 'classes/class-wc-price-calculator-product-page.php' );
		require_once( 'classes/class-wc-price-calculator-product.php' );
		require_once( 'classes/class-wc-price-calculator-settings.php' );

		if ( is_admin() ) $this->admin_includes();
	}


	/**
	 * Include required admin files
	 */
	private function admin_includes() {
		include( 'admin/woocommerce-measurement-price-calculator-admin-init.php' );  // Admin section
	}


	/**
	 * Get the plugin path
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		// gets the absolute path to this plugin directory
		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


	/**
	 * Get the plugin url, ie http://127.0.0.1/wp-content/plugins/woocommerce-measurement-price-calculator
	 *
	 * @return string the plugin url
	 */
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 */
	private function install() {

		global $wpdb;

		$installed_version = get_option( self::DB_VERSION_OPTION_NAME );

		if ( false === $installed_version ) {

			// set the default units for our custom measurement types
			add_option( 'woocommerce_area_unit',   WC_Price_Calculator_Settings::DEFAULT_AREA );
			add_option( 'woocommerce_volume_unit', WC_Price_Calculator_Settings::DEFAULT_VOLUME );

			// Upgrade path from 1.x to 2.0
			// get all old-style measurement price calculator products
			$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM " . $wpdb->postmeta . " WHERE meta_key='_measurement_price_calculator'" );

			foreach ( $rows as $row ) {
				if ( $row->meta_value ) {
					// calculator is enabled
					$product_custom_fields = get_post_custom( $row->post_id );

					// as long as the product doesn't also already have a new-style price calculator settings
					if ( ! isset( $product_custom_fields['_wc_price_calculator'][0] ) || ! $product_custom_fields['_wc_price_calculator'][0] ) {

						$settings = new WC_Price_Calculator_Settings();
						$settings = $settings->get_raw_settings();  // we want the underlying raw settings array

						switch ( $row->meta_value ) {
							case 'dimensions':
								$settings['calculator_type'] = 'dimension';
								// the previous version of the plugin allowed this weird multi-dimension tied input thing,
								//  I don't think anyone actually used it, and it didn't make much sense, so I'm not supporting
								//  it any longer
								if ( 'yes' == $product_custom_fields['_measurement_dimension_length'][0] ) {
									$settings['dimension']['length']['enabled']  = 'yes';
									$settings['dimension']['length']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
									$settings['dimension']['length']['editable'] = $product_custom_fields['_measurement_dimension_length_editable'][0];
								} elseif ( 'yes' == $product_custom_fields['_measurement_dimension_width'][0] ) {
									$settings['dimension']['width']['enabled']  = 'yes';
									$settings['dimension']['width']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
									$settings['dimension']['width']['editable'] = $product_custom_fields['_measurement_dimension_width_editable'][0];
								} elseif ( 'yes' == $product_custom_fields['_measurement_dimension_height'][0] ) {
									$settings['dimension']['height']['enabled']  = 'yes';
									$settings['dimension']['height']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
									$settings['dimension']['height']['editable'] = $product_custom_fields['_measurement_dimension_height_editable'][0];
								}
							break;
							case 'area':
								$settings['calculator_type'] = 'area';
								$settings['area']['area']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['area']['area']['editable'] = $product_custom_fields['_measurement_editable'][0];
							break;
							case 'volume':
								$settings['calculator_type'] = 'volume';
								$settings['volume']['volume']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['volume']['volume']['editable'] = $product_custom_fields['_measurement_editable'][0];
							break;
							case 'weight':
								$settings['calculator_type'] = 'weight';
								$settings['weight']['weight']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['weight']['weight']['editable'] = $product_custom_fields['_measurement_editable'][0];
							break;
							case 'walls':
								$settings['calculator_type'] = 'wall-dimension';
								$settings['wall-dimension']['length']['unit'] = $product_custom_fields['_measurement_display_unit'][0];
								$settings['wall-dimension']['width']['unit']  = $product_custom_fields['_measurement_display_unit'][0];
							break;
						}

						update_post_meta( $row->post_id, '_wc_price_calculator', $settings );
					}
				}
			}
		}

		if ( -1 === version_compare( $installed_version, self::VERSION ) );
			$this->upgrade( $installed_version );
	}


	/**
	 * Perform any version-related changes. Changes to custom db tables should be handled by the migrate() method
	 *
	 * @since 3.0
	 * @param int $installed_version the currently installed version of the plugin
	 */
	private function upgrade( $installed_version ) {

		if ( version_compare( $installed_version, "3.0" ) < 0 ) {
			global $wpdb;

			// updating 3.0: From 2.0 to 3.0, the '_wc_price_calculator'
			// product post meta calculator settings structure changed: 'calculator'
			// was added to the 'pricing' option

			$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_wc_price_calculator'" );

			foreach ( $rows as $row ) {
				if ( $row->meta_value ) {
					// calculator settings found

					$settings = new WC_Price_Calculator_Settings();
					$settings = $settings->set_raw_settings( $row->meta_value );  // we want the updated underlying raw settings array

					$updated = false;
					foreach ( WC_Price_Calculator_Settings::get_measurement_types() as $measurement_type ) {
						if ( isset( $settings[ $measurement_type ]['pricing']['enabled'] ) && 'yes' == $settings[ $measurement_type ]['pricing']['enabled'] ) {
							// enable the pricing calculator in the new settings data structure
							$settings[ $measurement_type ]['pricing']['calculator'] = array( 'enabled' => 'yes' );
							$updated = true;
						}
					}

					if ( $updated )
						update_post_meta( $row->post_id, '_wc_price_calculator', $settings );
				}
			}
		}

		// new db version
		update_option( self::DB_VERSION_OPTION_NAME, self::VERSION );
	}
}
