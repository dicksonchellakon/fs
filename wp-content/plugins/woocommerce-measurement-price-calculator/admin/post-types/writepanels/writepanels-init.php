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
 * @package   WC-Measurement-Price-Calculator/Admin/Write-Panels
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2013, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * WooCommerce Measurement Price Calculator Write Panels
 *
 * Sets up the write panels used by the measurement price calculator plugin
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once( 'writepanel-product_data.php' );
include_once( 'writepanel-product_data-calculator.php' );
include_once( 'writepanel-product-type-variable.php' );


/**
 * Loads the WooCommerce settings if not already available
 *
 * NOTE: I'm not in love with this solution, it would be nice if there were
 * a different way of getting the available units without loading the entire
 * settings structure
 *
 * @since 3.0
 */
function wc_measurement_price_calculator_load_wc_settings() {
	global $woocommerce, $woocommerce_settings;

	if ( ! $woocommerce_settings ) {
		if ( is_readable( $woocommerce->plugin_path() . '/admin/settings/settings-init.php' ) )
			require_once( $woocommerce->plugin_path() . '/admin/settings/settings-init.php' );
	}
}


/**
 * Returns all available weight units
 *
 * @since 3.0
 * @return array of weight units
 */
function wc_measurement_price_calculator_get_weight_units() {
	global $woocommerce_settings;

	wc_measurement_price_calculator_load_wc_settings();

	if ( $woocommerce_settings ) {
		foreach ( $woocommerce_settings['catalog'] as $setting ) {
			if ( 'woocommerce_weight_unit' == $setting['id'] ) {
				return $setting['options'];
			}
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'g', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'kg', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 't', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'oz', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'lbs', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'tn', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
	);
}


/**
 * Returns all available dimension units
 *
 * @since 3.0
 * @return array of dimension units
 */
function wc_measurement_price_calculator_get_dimension_units() {
	global $woocommerce_settings;

	wc_measurement_price_calculator_load_wc_settings();

	if ( $woocommerce_settings ) {
		foreach ( $woocommerce_settings['catalog'] as $setting ) {
			if ( 'woocommerce_dimension_unit' == $setting['id'] ) {
				return $setting['options'];
			}
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'mm', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'cm', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'm',  WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'km', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'ft', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'yd', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'mi', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
	);
}


/**
 * Returns all available area units
 *
 * @since 3.0
 * @return array of area units
 */
function wc_measurement_price_calculator_get_area_units() {
	global $woocommerce_settings;

	wc_measurement_price_calculator_load_wc_settings();

	if ( $woocommerce_settings ) {
		foreach ( $woocommerce_settings['catalog'] as $setting ) {
			if ( 'woocommerce_area_unit' == $setting['id'] ) {
				return $setting['options'];
			}
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'sq mm',   WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'sq cm',   WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'sq m',    WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'ha',      WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'sq km',   WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'sq. in.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'sq. ft.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'sq. yd.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'acs',     WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'sq. mi.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
	);
}


/**
 * Returns all available volume units
 *
 * @since 3.0
 * @return array of volume units
 */
function wc_measurement_price_calculator_get_volume_units() {
	global $woocommerce_settings;

	wc_measurement_price_calculator_load_wc_settings();

	if ( $woocommerce_settings ) {
		foreach ( $woocommerce_settings['catalog'] as $setting ) {
			if ( 'woocommerce_volume_unit' == $setting['id'] ) {
				return $setting['options'];
			}
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'ml',      WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'l',       WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'cu m',    WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'cup',     WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'pt',      WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'qt',      WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'gal',     WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'fl. oz.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'cu. in.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'cu. ft.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		__( 'cu. yd.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
	);
}


/**
 * Output a radio input box.
 *
 * @access public
 * @param array $field with required fields 'id' and 'rbvalue'
 * @return void
 */
function wc_measurement_price_calculator_wp_radio( $field ) {
	global $thepostid, $post;

	if ( ! $thepostid ) $thepostid = $post->ID;
	if ( ! isset( $field['class'] ) ) $field['class'] = 'radio';
	if ( ! isset( $field['wrapper_class'] ) ) $field['wrapper_class'] = '';
	if ( ! isset( $field['name'] ) ) $field['name'] = $field['id'];
	if ( ! isset( $field['value'] ) ) $field['value'] = get_post_meta( $thepostid, $field['name'], true );

	echo '<p class="form-field ' . $field['id'] . '_field ' . $field['wrapper_class'] . '"><label for="' . $field['id'].'">' . $field['label'] . '</label><input type="radio" class="' . $field['class'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" value="' . $field['rbvalue'] . '" ';

	checked( $field['value'], $field['rbvalue'] );

	echo ' /> ';

	if ( isset( $field['description'] ) && $field['description'] ) echo '<span class="description">' . $field['description'] . '</span>';

	echo '</p>';
}
