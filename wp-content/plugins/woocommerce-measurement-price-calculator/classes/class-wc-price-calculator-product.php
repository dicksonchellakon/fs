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
 * Measurement Price Calculator Product Helper Class
 *
 * @since 3.0
 */
class WC_Price_Calculator_Product {


	/**
	 * Gets the identified product. Compatible with WC 2.0 and backwards
	 * compatible with previous versions
	 *
	 * @since 3.0
	 * @param int $product_id the product identifier
	 * @param array $args optional array of arguments
	 *
	 * @return WC_Product the product
	 */
	public static function get_product( $product_id, $args = array() ) {
		$product = null;

		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
			// WC 2.0
			$product = get_product( $product_id, $args );
		} else {

			// old style, get the product or product variation object
			if ( isset( $args['parent_id'] ) && $args['parent_id'] ) {
				$product = new WC_Product_Variation( $product_id, $args['parent_id'] );
			} else {
				$product = new WC_Product( $product_id );
			}
		}

		return $product;
	}


	/**
	 * Helper function to retrieve a custom field from a product, compatible
	 * both with WC < 2.0 and WC >= 2.0
	 *
	 * @since 3.0
	 * @param WC_Product $product the product object
	 * @param string $field_name the field name, without a leading underscore
	 *
	 * @return mixed the value of the member named $field_name, or null
	 */
	public static function get_meta( $product, $field_name ) {
		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
			// even in WC >= 2.0 product variations still use the product_custom_fields array apparently
			if ( $product->variation_id && isset( $product->product_custom_fields[ '_' . $field_name ][0] ) && '' !== $product->product_custom_fields[ '_' . $field_name ][0] ) {
				return $product->product_custom_fields[ '_' . $field_name ][0];
			}
			// use magic __get
			return $product->$field_name;
		} else {
			// use product custom fields array

			// variation support: return the value if it's defined at the variation level
			if ( isset( $product->variation_id ) && $product->variation_id ) {
				if ( ( $value = get_post_meta( $product->variation_id, '_' . $field_name, true ) ) !== '' ) return $value;
				// otherwise return the value from the parent
				return get_post_meta( $product->id, '_' . $field_name, true );
			}

			// regular product
			return isset( $product->product_custom_fields[ '_' . $field_name ][0] ) ? $product->product_custom_fields[ '_' . $field_name ][0] : null;
		}

		return null;
	}


	/**
	 * Returns true if a calculator is enabled for the given product
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return boolean true if the measurements calculator is enabled and
	 *         should be displayed for the product, false otherwise
	 */
	public static function calculator_enabled( $product ) {

		// basic checks
		if ( $product->is_virtual() ||
		     $product->is_type( 'grouped' ) ||
		     ! self::get_meta( $product, 'wc_price_calculator' ) ) return false;

		// see whether a calculator is configured for this product
		$settings = new WC_Price_Calculator_Settings( $product );

		return $settings->is_calculator_enabled();
	}


	/**
	 * Returns true if the price calculator is enabled for the given product
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return boolean true if the price calculator is enabled
	 */
	public static function pricing_calculator_enabled( $product ) {

		if ( self::calculator_enabled( $product ) ) {
			// see whether a calculator is configured for this product
			$settings = new WC_Price_Calculator_Settings( $product );
			return $settings->is_pricing_calculator_enabled();
		}

		return false;
	}


	/**
	 * Returns true if the price for the given product should be displayed "per
	 * unit" regardless of the calculator type (quantity or pricing)
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return boolean true if the price should be displayed "per unit"
	 */
	public static function pricing_per_unit_enabled( $product ) {

		if ( self::calculator_enabled( $product ) ) {
			// see whether a calculator is configured for this product
			$settings = new WC_Price_Calculator_Settings( $product );
			return $settings->is_pricing_enabled();
		}

		return false;
	}


	/**
	 * Returns true if the price calculator and stock management are enabled for the given product
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return boolean true if the price calculator and stock management are enabled
	 */
	public static function pricing_calculator_inventory_enabled( $product ) {
		// TODO: also verify that stock is being managed for the product?  use case: stock management turned on, pricing calculator inventory enabled, stock management is diabled
		if ( self::calculator_enabled( $product ) ) {
			// see whether a calculator is configured for this product
			$settings = new WC_Price_Calculator_Settings( $product );
			return $settings->is_pricing_inventory_enabled();
		}

		return false;
	}


	/**
	 * Returns true if the price calculator and calculated weight are enabled for the given product
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return boolean true if the price calculator and stock management are enabled
	 */
	public static function pricing_calculated_weight_enabled( $product ) {

		if ( self::calculator_enabled( $product ) ) {

			if ( 'no' !== get_option( 'woocommerce_enable_weight', true ) ) {
				// see whether a calculator is configured for this product
				$settings = new WC_Price_Calculator_Settings( $product );
				return $settings->is_pricing_calculated_weight_enabled();
			}
		}

		return false;
	}


	/**
	 * Gets the total physical property measurement for the given product
	 * that is the product length/width/height, area, volume or weight, depending
	 * on the current calculator type.
	 *
	 * So for instance, if the calculator type is Area or Area (LxW) the returned
	 * measurment will be an area measurement, with the area value taken from the
	 * product configuration dimensions (length x width) or area.
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @param WC_Price_Calculator_Settings $settings the measurement price calculator settings
	 * @return WC_Price_Calculator_Measurement physical property measurement or null
	 */
	public static function get_product_measurement( $product, $settings ) {
		switch( $settings->get_calculator_type() ) {
			case 'dimension':      return self::get_dimension_measurement( $product, $settings->get_calculator_measurements() );

			case 'area':
			case 'area-dimension': return self::get_area_measurement( $product );

			case 'volume':
			case 'volume-dimension':
			case 'volume-area':    return self::get_volume_measurement( $product );

			case 'weight':         return self::get_weight_measurement( $product );

			// just a specially presented area calculator
			case 'wall-dimension': return self::get_area_measurement( $product );
		}

		// should never happen
		return null;
	}


	/**
	 * Gets a dimension (length, width or height) of the product, based on
	 * $measurements, and in woocommerce dimension units
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @param array of WC_Price_Calculator_Measurement.  Actually just one, representing width, length or height
	 * @return WC_Price_Calculator_Measurement measurement object in product units
	 */
	public static function get_dimension_measurement( $product, $measurements ) {

		// get the one (and only) measurement object
		list( $measurement ) = $measurements;

		$unit = get_option( 'woocommerce_dimension_unit' );

		$measurement_name = $measurement->get_name();

		return new WC_Price_Calculator_Measurement( $unit, $product->$measurement_name, $measurement_name, ucwords( $measurement_name ) );
	}


	/**
	 * Gets the area of the product, if one is defined, in woocommerce product units
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return WC_Price_Calculator_Measurement total area measurement for the product
	 */
	public static function get_area_measurement( $product ) {
		$measurement = null;

		// if a length and width are defined, use that
		if ( $product->length && $product->width ) {
			$area = $product->length * $product->width;
			$unit = WC_Price_Calculator_Measurement::to_area_unit( get_option( 'woocommerce_dimension_unit' ) );
			$measurement = new WC_Price_Calculator_Measurement( $unit, $area, 'area', __( 'Area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );

			// convert to the product area units
			$measurement->set_unit( get_option( 'woocommerce_area_unit' ) );
		}

		// if they overrode the length/width with an area value, use that
		$area = self::get_meta( $product, 'area' );

		if ( $area ) {
			$unit = get_option( 'woocommerce_area_unit' );
			$measurement = new WC_Price_Calculator_Measurement( $unit, $area, 'area', __( 'Area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );
		}

		// if no measurement, just create a default empty one
		if ( ! $measurement ) {
			$unit = get_option( 'woocommerce_area_unit' );
			$measurement = new WC_Price_Calculator_Measurement( $unit, 0, 'area', __( 'Area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );
		}

		return $measurement;
	}


	/**
	 * Gets the volume of the product, if one is defined, in woocommerce product units
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return WC_Price_Calculator_Measurement total volume measurement for the product, or null
	 */
	public static function get_volume_measurement( $product ) {
		$measurement = null;

		// if a length and width are defined, use that.  We allow large and small dimensions
		//  (mm, km, mi) which don't make much sense to use as volumes, but
		//  we have no choice but to support them to some extent, so convert
		//  them to something more reasonable
		if ( $product->length && $product->width && $product->height ) {
			$volume = $product->length * $product->width * $product->height;

			switch( get_option( 'woocommerce_dimension_unit' ) ) {
				case 'mm':
					$volume *= .001;        // convert to ml
					$unit = 'ml';
					break;
				case 'km':
					$volume *= 1000000000;  // convert to cu m
					$unit = 'cu m';
					break;
				case 'mi':
					$volume *= 5451776000;  // convert to cu yd
					$unit = 'cu. yd.';
					break;
			}

			$unit = WC_Price_Calculator_Measurement::to_volume_unit( get_option( 'woocommerce_dimension_unit' ) );
			$measurement = new WC_Price_Calculator_Measurement( $unit, $volume, 'volume', __( 'Volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );

			// convert to the product volume units
			$measurement->set_unit( get_option( 'woocommerce_volume_unit' ) );
		}

		// if there's an area and height, next use that
		$area = self::get_meta( $product, 'area' );
		if ( $area && $product->height ) {
			$area_unit   = get_option( 'woocommerce_area_unit' );
			$area_measurement = new WC_Price_Calculator_Measurement( $area_unit, $area );

			$dimension_unit = get_option( 'woocommerce_dimension_unit' );
			$dimension_measurement = new WC_Price_Calculator_Measurement( $dimension_unit, $product->height );

			// determine the volume, in common units
			$dimension_measurement->set_common_unit( $area_measurement->get_unit_common() );
			$volume = $area_measurement->get_value_common() * $dimension_measurement->get_value_common();
			$volume_unit = WC_Price_Calculator_Measurement::to_volume_unit( $area_measurement->get_unit_common() );
			$measurement = new WC_Price_Calculator_Measurement( $volume_unit, $volume, 'volume', __( 'Volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );

			// and convert to final volume units
			$measurement->set_unit( get_option( 'woocommerce_volume_unit' ) );
		}

		// finally if they overrode the length/width/height with a volume value, use that
		$volume = self::get_meta( $product, 'volume' );
		if ( $volume ) {
			$measurement = new WC_Price_Calculator_Measurement( get_option( 'woocommerce_volume_unit' ), $volume, 'volume', __( 'Volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );
		}

		// if no measurement, just create a default empty one
		if ( ! $measurement ) {
			$measurement = new WC_Price_Calculator_Measurement( get_option( 'woocommerce_volume_unit' ), 0, 'volume', __( 'Volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );
		}

		return $measurement;
	}


	/**
	 * Gets the weight of the product, if one is defined, in woocommerce product units
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return WC_Price_Calculator_Measurement weight measurement for the product
	 */
	public static function get_weight_measurement( $product ) {
		return new WC_Price_Calculator_Measurement( get_option( 'woocommerce_weight_unit' ), $product->get_weight(), 'weight', __( 'Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );
	}


	/**
	 * Get the min/max quantity range for this given product.  At least, do
	 * the best we can.  The issue is that this is controlled ultimately by
	 * template files, which could be changed by the user/theme.
	 *
	 * @see woocommerce-template.php woocommerce_quantity_input()
	 * @see woocommerce/templates/single-product/add-to-cart/simple.php
	 * @see woocommerce/templates/single-product/add-to-cart/variable.php
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return array associative array with keys 'min_value' and 'max_value'
	 */
	public static function get_quantity_range( $product ) {

		// get the quantity min/max for this product
		$defaults = array(
			'input_name'  => 'quantity',
			'input_value' => '1',
			'max_value'   => '',
			'min_value'   => '0',
		);

		$args = array();
		if ( $product->is_type( 'simple' ) ) {
			$args = array( 'min_value' => 1, 'max_value' => $product->backorders_allowed() ? '' : $product->get_stock_quantity(), );
		}

		return apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults  ) );
	}


	/**
	 * Returns the price html for the pricing rules table associated with $product.
	 * Ie:
	 * * "From -$5 ft- Free!"
	 * * "$5 ft"
	 * * "Free!"
	 * * etc
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return string pricing rules price html string
	 */
	public static function get_pricing_rules_price_html( $product ) {

		$settings = new WC_Price_Calculator_Settings( $product );

		$price_html = '';
		$price = $min_price = $settings->get_pricing_rules_minimum_price();
		$min_regular_price = $settings->get_pricing_rules_minimum_regular_price();
		$max_price = $settings->get_pricing_rules_maximum_price();

		// Get the price
		if ( $price > 0 ) {
			// Regular price

			if ( $settings->pricing_rules_is_on_sale()  && $min_regular_price !== $price ) {

				if ( ! $min_price || $min_price !== $max_price )
					$price_html .= $product->get_price_html_from_text();

				$price_html .= self::get_price_html_from_to( $min_regular_price, $price, __( $settings->get_pricing_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );

			} else {

				if ( $min_price !== $max_price )
					$price_html .= $product->get_price_html_from_text();

				$price_html .= woocommerce_price( $price ) . ' ' . __( $settings->get_pricing_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN );

			}
		} elseif ( '' === $price ) {
			// no-op (for now)
		} elseif ( 0 == $price ) {
			// Free price

			if ( $settings->pricing_rules_is_on_sale() && $min_regular_price !== $price ) {

				if ( $min_price !== $max_price )
					$price_html .= $product->get_price_html_from_text();

				$price_html .= self::get_price_html_from_to( $min_regular_price, __( 'Free!', WC_Measurement_Price_Calculator::TEXT_DOMAIN ), __( $settings->get_pricing_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN ) );

			} else {

				if ( $min_price !== $max_price )
					$price_html .= $product->get_price_html_from_text();

				$price_html .= __( 'Free!', WC_Measurement_Price_Calculator::TEXT_DOMAIN );

			}

		}

		return $price_html;
	}


	/**
	 * Functions for getting parts of a price, in html, used by get_price_html.
	 *
	 * @since 3.0
	 * @param mixed $from the 'from' price or string
	 * @param mixed $to the 'to' price or string
	 * @param $pricing_label the pricing label to display
	 * @return string the pricing from-to string
	 */
	public static function get_price_html_from_to( $from, $to, $pricing_label ) {
		return '<del>' . ( ( is_numeric( $from ) ) ? woocommerce_price( $from ) . ' ' . $pricing_label : $from ) . '</del> <ins>' . ( ( is_numeric( $to ) ) ? woocommerce_price( $to ) . ' ' . $pricing_label : $to ) . '</ins>';
	}


	/**
	 * Returns an array of measurements for the given product
	 *
	 * @since 3.0
	 * @param WC_Product $product the product
	 * @return array of WC_Price_Calculator_Measurement objects for the product
	 */
	public static function get_product_measurements( $product ) {
		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			$settings = new WC_Price_Calculator_Settings( $product );

			return $settings->get_calculator_measurements();
		}
	}


	/**
	 * Sync variable product prices with the children lowest/highest price per
	 * unit.
	 *
	 * Code based on WC_Product_Variable version 2.0.0
	 * @see WC_Product_Variable::variable_product_sync()
	 * @see WC_Price_Calculator_Product::variable_product_unsync()
	 *
	 * @since 3.0
	 * @param WC_Product_Variable $product the variable product
	 * @param WC_Price_Calculator_Settings $settings the calculator settings
	 */
	public static function variable_product_sync( $product, $settings ) {

		// save the original values so we can restore the product
		$product->wcmpc_min_variation_price         = $product->min_variation_price;
		$product->wcmpc_min_variation_regular_price = $product->min_variation_regular_price;
		$product->wcmpc_min_variation_sale_price    = $product->min_variation_sale_price;
		$product->wcmpc_max_variation_price         = $product->max_variation_price;
		$product->wcmpc_max_variation_regular_price = $product->max_variation_regular_price;
		$product->wcmpc_max_variation_sale_price    = $product->max_variation_sale_price;
		$product->wcmpc_price                       = $product->price;

		$product->min_variation_price = $product->min_variation_regular_price = $product->min_variation_sale_price = $product->max_variation_price = $product->max_variation_regular_price = $product->max_variation_sale_price = '';

		foreach ( $product->get_children() as $variation_product_id ) {

			$variation_product = WC_Price_Calculator_Product::get_product( $variation_product_id );

			$child_price         = $variation_product->price;
			$child_regular_price = $variation_product->regular_price;
			$child_sale_price    = $variation_product->sale_price;

			// get the product measurement
			$measurement = WC_Price_Calculator_Product::get_product_measurement( $variation_product, $settings );
			$measurement->set_unit( $settings->get_pricing_unit() );

			if ( ( '' === $child_price && '' === $child_regular_price ) || ! $measurement->get_value() )
				continue;

			// convert to price per unit
			if ( '' !== $child_price ) $child_price /= $measurement->get_value();

			// Regular prices
			if ( $child_regular_price !== '' ) {

				// convert to price per unit
				$child_regular_price /= $measurement->get_value();

				if ( ! is_numeric( $product->min_variation_regular_price ) || $child_regular_price < $product->min_variation_regular_price )
					$product->min_variation_regular_price = $child_regular_price;

				if ( ! is_numeric( $product->max_variation_regular_price ) || $child_regular_price > $product->max_variation_regular_price )
					$product->max_variation_regular_price = $child_regular_price;
			}

			// Sale prices
			if ( $child_sale_price !== '' ) {

				// convert to price per unit
				$child_sale_price /= $measurement->get_value();

				if ( $child_price == $child_sale_price ) {
					if ( ! is_numeric( $product->min_variation_sale_price ) || $child_sale_price < $product->min_variation_sale_price )
						$product->min_variation_sale_price = $child_sale_price;

					if ( ! is_numeric( $product->max_variation_sale_price ) || $child_sale_price > $product->max_variation_sale_price )
						$product->max_variation_sale_price = $child_sale_price;
				}
			}

			// Actual prices
			if ( $child_price !== '' ) {
				if ( $child_price > $product->max_variation_price )
					$product->max_variation_price = $child_price;

				if ( $product->min_variation_price === '' || $child_price < $product->min_variation_price )
					$product->min_variation_price = $child_price;
			}
		}

		// as seen in WC_Product_Variable::get_price_html()
		$product->price = $product->min_variation_price;
	}


	/**
	 * Restores the given variable $product min/max pricing back to the original
	 * values found before variable_product_sync() was invoked
	 *
	 * @see WC_Price_Calculator_Product::variable_product_sync()
	 *
	 * @since 3.0
	 * @param WC_Product_Variable $product the variable product
	 */
	public static function variable_product_unsync( $product ) {
		// restore the variable product back to normal
		$product->min_variation_price         = $product->wcmpc_min_variation_price;
		$product->min_variation_regular_price = $product->wcmpc_min_variation_regular_price;
		$product->min_variation_sale_price    = $product->wcmpc_min_variation_sale_price;
		$product->max_variation_price         = $product->wcmpc_max_variation_price;
		$product->max_variation_regular_price = $product->wcmpc_max_variation_regular_price;
		$product->max_variation_sale_price    = $product->wcmpc_max_variation_sale_price;
		$product->price                       = $product->wcmpc_price;
	}


}
