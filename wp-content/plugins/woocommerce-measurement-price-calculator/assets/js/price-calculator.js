jQuery(document).ready(function($) {

	// TODO: can't have functions declared within the if blocks below unfortunately for strict mode
	// "use strict";

	// this is the best we can do to determine when a variable product is
	//  configured such that no variation is selected, and the 'add to cart'
	//  button is hidden
	$(document).bind('reset_image', function(event) {
		wc_price_calculator_params['product_price']             = '';
		wc_price_calculator_params['product_measurement_value'] = '';
		wc_price_calculator_params['product_measurement_unit']  = '';

		$('.variable_price_calculator').hide();
	});


	/**
	 * Gets the price for the given measurement from the pricing rules
	 *
	 * @param float measurement the product measurement
	 * @return object the rule, if any
	 */
	function getPricingRule( measurement ) {

		var foundRule = null;

		$.each( wc_price_calculator_params['pricing_rules'], function( index, rule ) {
			if ( measurement >= parseFloat( rule['range_start'] ) && ( '' === rule['range_end'] || measurement <= rule['range_end'] ) ) {
				foundRule = rule;
				return false;
			}
		} );

		return foundRule;
	}


	/** Pricing Calculator ********************************************/


	if ('undefined' != typeof wc_price_calculator_params && 'pricing' == wc_price_calculator_params['calculator_type']) {

		/**
		 * if all required measurements are provided, calculate and display the total product price
		 */
		$( 'form.cart' ).bind( 'wc-measurement-price-calculator-update', function() {
			var totalMeasurement;

			// for each user-supplied measurement:  allow other plugins a chance to modify things
			$('input.amount_needed, select.amount_needed').each(function(index, el) {
				el = $(el);
				var val = el.val().replace(wc_price_calculator_params['woocommerce_price_decimal_sep'], ".");    // allow for other decimal separators
				var measurementValue = parseFloat(val);

				el.trigger('wc-measurement-price-calculator-product-measurement-change', [measurementValue]);
			});

			// for each user-supplied measurement multiply it by the preceding ones to derive the Area or Volume
			$('input.amount_needed, select.amount_needed').each(function(index, el) {
				el = $(el);
				var val = el.val().replace(wc_price_calculator_params['woocommerce_price_decimal_sep'], ".");    // allow for other decimal separators
				var measurementValue = parseFloat(val);

				// if no measurement value, or negative, we can't get a total measurement so break the loop
				if (!measurementValue || measurementValue < 0) {
					totalMeasurement = 0;
					return false;
				}

				// convert to the common measurement unit so as we multiply measurements together to dervice an area or volume, we do so in a single known "common" unit
				measurementValue = convertUnits( measurementValue, el.data( 'unit' ), el.data( 'common-unit' ) );

				if (!totalMeasurement) {
					// first or single measurement
					totalMeasurement = measurementValue;
				} else {
					// multiply to get either the area or volume measurement
					totalMeasurement *= measurementValue;
				}
			});

			// now totalMeasurement is in 'product_total_measurement_common_unit', convert to pricing units
			totalMeasurement = convertUnits( totalMeasurement, wc_price_calculator_params['product_total_measurement_common_unit'], wc_price_calculator_params['product_price_unit'] );

			// round to configured precision
			totalMeasurement = parseFloat( totalMeasurement.toFixed( wc_price_calculator_params['measurement_precision'] ) );

			// is there a pricing rule which matches the customer-supplied measurement?
			if ( wc_price_calculator_params['pricing_rules'] ) {
				var rule = getPricingRule( totalMeasurement );

				if ( rule ) {
					wc_price_calculator_params['product_price'] = parseFloat( rule['price'] );
					$( '.single_variation' ).html( rule['price_html'] );
				} else {
					wc_price_calculator_params['product_price'] = '';
					$( '.single_variation' ).html( '' );
				}
			}

			// set the measurement needed, so we can easily multiply (measurement needed) * (price per unit) to get the final product price on the backend
			$( '#_measurement_needed' ).val( totalMeasurement );
			$( '#_measurement_needed_unit' ).val( wc_price_calculator_params['product_price_unit'] );
			var price = '';

			if (totalMeasurement) {
				// calculate the price based on the total measurement
				price = wc_price_calculator_params['product_price'] * totalMeasurement;

				// check for a minimum price
				if ( wc_price_calculator_params['minimum_price'] > price ) price = parseFloat( wc_price_calculator_params['minimum_price'] );

				// set the price
				$('.product_price').html(woocommerce_price(price)).trigger('wc-measurement-price-calculator-product-price-change', [totalMeasurement, price]);

			} else {
				$('.product_price').html('').trigger('wc-measurement-price-calculator-product-price-change');
			}

			// display the total amount, in display untis, if the "total amount" element is available
			if ( $( '.wc-measurement-price-calculator-total-amount' ) ) {
				var totalAmount = convertUnits( totalMeasurement, wc_price_calculator_params['product_price_unit'], $( '.wc-measurement-price-calculator-total-amount' ).data( 'unit' ) );
				totalAmount = parseFloat( totalMeasurement.toFixed( wc_price_calculator_params['measurement_precision'] ) );
				$( '.wc-measurement-price-calculator-total-amount' ).text( totalAmount );
			}

			// add support for WooCommerce Product Addons by feeding the calculated product price in and triggering the addons update
			if ( 'undefined' != typeof woocommerce_addons_params && $( 'form.cart' ).find( '#product-addons-total' ).length > 0 ) {
				var productPrice = '' === price ? 0 : price;

				woocommerce_addons_params.product_price = productPrice.toFixed( 2 );
				$( 'form.cart' ).trigger( 'woocommerce-product-addons-update' );
			}
		} );

		// display pricing on page load if we can
		$( 'form.cart' ).trigger( 'wc-measurement-price-calculator-update' );

		// pricing calculator measurement changed: update product pricing
		$('input.amount_needed').keyup( function() {
			var $cart = $( this ).closest( 'form.cart' );
			$cart.trigger( 'wc-measurement-price-calculator-update' );
		} );
		$('select.amount_needed').change( function() {
			var $cart = $( this ).closest( 'form.cart' );
			$cart.trigger( 'wc-measurement-price-calculator-update' );
		} );


		// called when a variable product is fully configured and the 'add to cart'
		//  button is displayed
		$(document).bind('show_variation', function(event,variation) {
			var price = parseFloat(variation.price);
			wc_price_calculator_params['product_price'] = price;  // set the current variation product price
			$( 'form.cart' ).trigger( 'wc-measurement-price-calculator-update' );
			$('.variable_price_calculator').show();
		});


		// support for product addons 2.0.9+ by adding the calculated price in after the addon price is updated, and then triggering another addons update
		$( 'body' ).bind( 'updated_addons', function() {

			var $cart         = $( 'form.cart' );
			var $totals       = $cart.find( '#product-addons-total' );
			var product_price = $totals.data( 'price' );

			// avoid infinite loop
			if ( product_price != woocommerce_addons_params.product_price && $totals.length > 0 ) {
				$totals.data( 'price', woocommerce_addons_params.product_price );

				$cart.trigger( 'woocommerce-product-addons-update' );
			}

		} );
	}


	/** Quantity calculator ********************************************/


	if ('undefined' != typeof wc_price_calculator_params && 'quantity' == wc_price_calculator_params['calculator_type']) {

		/**
		 * quantity changed, update any amount needed inputs, actual amount fields, and total price
		 */
		$( 'form.cart' ).bind( 'wc-measurement-price-calculator-quantity-changed', function( event, quantity ) {

			if (!wc_price_calculator_params['product_measurement_value']) return;

			// update the amount needed/amount actual fields
			$('.amount_needed, .amount_actual').each(function(index,el) {
				el = $(el);

				// if we're dealing with more than one input, it's impossible to estimate the amounts needed based on quantity
				if (el.hasClass('amount_needed') && $('.amount_needed').length > 1) return true;

				// convert the product measurement value from product units to frontend display units
				var amount = convertUnits(wc_price_calculator_params['product_measurement_value'], wc_price_calculator_params['product_measurement_unit'], el.data('unit'));
				// TODO: I'm not saying that rounding to two decimal places is the ideal/best solution, but it's a tough problem and hopefully reasonable for now
				amount = parseFloat((amount * quantity).toFixed(2));

				if (el.is('input')) {
					el.val(amount);
				} else {
					el.text(amount);
				}
			});

			// set total price
			$('.total_price').html(woocommerce_price(quantity * wc_price_calculator_params['product_price'])).trigger('wc-measurement-price-calculator-quantity-total-price-change', [quantity, wc_price_calculator_params['product_price']]);
		} );


		/**
		 * "Compile" the product measurements down to a single value (dimension,
		 * area, volume or weight) if enough measurements are provided by the customer, and
		 * update the quantity, total price and actual amount fields
		 */
		$( 'form.cart' ).bind( 'wc-measurement-price-calculator-update', function() {

			if (!wc_price_calculator_params['product_measurement_value']) return;

			var totalMeasurement;

			// for each user-supplied measurement multiply it by the preceding ones to derive the Area or Volume
			$('input.amount_needed').each(function(index, el) {
				el = $(el);
				var val = el.val().replace(wc_price_calculator_params['woocommerce_price_decimal_sep'], ".");    // allow for other decimal separators
				var measurementValue = parseFloat(val);

				// if no measurement value, or negative, we can't get a total measurement so break the loop
				if (!measurementValue || measurementValue < 0) {
					totalMeasurement = 0;
					return false;
				}

				// convert to the common measurement unit so as we multiply measurements together to dervice an area or volume, we do so in a single known "common" unit
				measurementValue = convertUnits( measurementValue, el.data( 'unit' ), el.data( 'common-unit' ) );

				if (!totalMeasurement) {
					// first or single measurement
					totalMeasurement = measurementValue;
				} else {
					// multiply to get either the area or volume measurement
					totalMeasurement *= measurementValue;
				}
			});

			if (totalMeasurement) {
				// convert the product measurement to total measurement units

				var productMeasurement = convertUnits( wc_price_calculator_params['product_measurement_value'], wc_price_calculator_params['product_measurement_unit'], wc_price_calculator_params['product_total_measurement_common_unit'] );

				// determine the quantity based on the amount of product needed / amount of product in a quantity of 1
				//  note that we toFixed() to limit the amount of precision used since there's the chance of getting
				//  a value like 1.0000003932 when converting between different systems of measurement, and we wouldn't want to make that '2'
				var quantity = Math.ceil( ( totalMeasurement / productMeasurement ).toFixed( wc_price_calculator_params['measurement_precision'] ) );

				if (quantity < parseFloat(wc_price_calculator_params['quantity_range_min_value'])) {
					quantity = parseFloat(wc_price_calculator_params['quantity_range_min_value']);
				}

				if (parseFloat(wc_price_calculator_params['quantity_range_max_value']) && quantity > parseFloat(wc_price_calculator_params['quantity_range_max_value'])) {
					quantity = parseFloat(wc_price_calculator_params['quantity_range_max_value']);
				}

				// update the quantity
				$('input[name=quantity]').val(quantity);

				// update the amount actual fields
				$('.amount_actual').each(function(index,el) {
					el = $(el);

					// convert the product measurement value from product units to frontend display units
					var amount = convertUnits(wc_price_calculator_params['product_measurement_value'], wc_price_calculator_params['product_measurement_unit'], el.data('unit'));
					// TODO: I'm not saying that rounding to two decimal places is the ideal/best solution, but it's a tough problem and hopefully reasonable for now
					amount = parseFloat((amount * quantity).toFixed(2));

					if (el.is('input')) {
						el.val(amount);
					} else {
						el.text(amount);
					}
				});

				// update the total price
				$('.total_price').html(woocommerce_price(quantity * wc_price_calculator_params['product_price'])).trigger('wc-measurement-price-calculator-total-price-change', [quantity, wc_price_calculator_params['product_price']]);
			}
		} );


		// pricing calculator measurement changed: update product quantity
		$('input.amount_needed').keyup( function() {
			var $cart = $( this ).closest( 'form.cart' );
			$cart.trigger( 'wc-measurement-price-calculator-update' );
		} );


		// user typed a new quantity (change which we bind to below, only fires when the quantity field loses focus)
		$('input[name=quantity]').keyup( function(evt) {
			var $cart = $( this ).closest( 'form.cart' );
			$cart.trigger( 'wc-measurement-price-calculator-quantity-changed', [ evt.target.value ] );
		} );


		// quantity +/- buttons, and set price on initial page load
		$('input[name=quantity]').bind('change', function(evt) {
			var $cart = $( this ).closest( 'form.cart' );
			$cart.trigger( 'wc-measurement-price-calculator-quantity-changed', [ evt.target.value ] );
		} ).change();


		// called when a variable product is fully configured and the 'add to cart'
		//  button is displayed
		$(document).bind('show_variation', function(event,variation) {

			wc_price_calculator_params['product_price']             = parseFloat(variation.price);  // set the current variation product price
			wc_price_calculator_params['product_measurement_value'] = parseFloat(variation.product_measurement_value);
			wc_price_calculator_params['product_measurement_unit']  = variation.product_measurement_unit;

			if (variation.product_measurement_value) {
				if ($('input.amount_needed').length > 0) {
					if (!$('input.amount_needed').val()) {
						// first time a variation is selected, no amount needed, so set the amount actual/total price based on the starting quantity
						$( 'form.cart' ).trigger( 'wc-measurement-price-calculator-quantity-changed', [ $('input[name=quantity]').val() ] );
					} else {
						// measurement inputs, so update the quantity, price for the current product
						$( 'form.cart' ).trigger( 'wc-measurement-price-calculator-update' );
					}
				} else {
					// otherwise no measurement inputs, so just update the amount actual
					$( 'form.cart' ).trigger( 'wc-measurement-price-calculator-quantity-changed', [ $('input[name=quantity]').val() ] );
				}
				$('.variable_price_calculator').show();
			} else {
				// variation does not have all required physical attributes defined, so hide the calculator
				$('.variable_price_calculator').hide();
			}
		});
	}


	/** Core PHP Function Ports ********************************************/


	/**
	 * http://phpjs.org/functions/number_format/
	 */
	function number_format(number, decimals, dec_point, thousands_sep) {
		// Strip all characters but numerical ones.
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
	}


	/**
	 * http://phpjs.org/functions/preg_quote/
	 */
	function preg_quote(str, delimiter) {
	  return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
	}


	/** Custom PHP Function Ports ********************************************/


	/**
	 * Convert value from the current unit to a new unit
	 *
	 * @param numeric value the value in fromUnit units
	 * @param string fromUnit the unit that value is in
	 * @param string toUnit the unit to convert to
	 * @return numeric value in toUnit untis
	 */
	function convertUnits( value, fromUnit, toUnit ) {

		// fromUnit to its corresponding standard unit
		if ( 'undefined' != typeof( wc_price_calculator_params['unit_normalize_table'][ fromUnit ] ) ) {
			if ( 'undefined' != typeof( wc_price_calculator_params['unit_normalize_table'][ fromUnit ]['inverse'] ) && wc_price_calculator_params['unit_normalize_table'][ fromUnit ]['inverse'] )
				value /= wc_price_calculator_params['unit_normalize_table'][ fromUnit ]['factor'];
			else
				value *= wc_price_calculator_params['unit_normalize_table'][ fromUnit ]['factor'];

			fromUnit = wc_price_calculator_params['unit_normalize_table'][ fromUnit ]['unit'];
		}

		// standard unit to toUnit
		if ( 'undefined' != typeof( wc_price_calculator_params['unit_conversion_table'][ fromUnit ] ) && 'undefined' != typeof( wc_price_calculator_params['unit_conversion_table'][ fromUnit ][ toUnit ] ) ) {
			if ( 'undefined' != typeof( wc_price_calculator_params['unit_conversion_table'][ fromUnit ][ toUnit ]['inverse'] ) && wc_price_calculator_params['unit_conversion_table'][ fromUnit ][ toUnit ]['inverse'] )
				value /= wc_price_calculator_params['unit_conversion_table'][ fromUnit ][ toUnit ]['factor'];
			else
				value *= wc_price_calculator_params['unit_conversion_table'][ fromUnit ][ toUnit ]['factor'];
		}

		return value;
	}


	/** WooCommerce Function Ports ********************************************/


	/**
	 * Returns the price formatted according to the WooCommerce settings
	 */
	function woocommerce_price(price) {
		var formatted_price = '';

		var num_decimals    = wc_price_calculator_params['woocommerce_price_num_decimals'];
		var currency_pos    = wc_price_calculator_params['woocommerce_currency_pos'];
		var currency_symbol = wc_price_calculator_params['woocommerce_currency_symbol'];

		price = number_format(price, num_decimals, wc_price_calculator_params['woocommerce_price_decimal_sep'], wc_price_calculator_params['woocommerce_price_thousand_sep'] );

		if ('yes' == wc_price_calculator_params['woocommerce_price_trim_zeros'] && num_decimals > 0) {
			price = woocommerce_trim_zeros(price);
		}

		switch ( currency_pos ) {
			case 'left' :
				formatted_price = '<span class="amount">' + currency_symbol + price + '</span>';
			break;
			case 'right' :
				formatted_price = '<span class="amount">' + price + currency_symbol + '</span>';
			break;
			case 'left_space' :
				formatted_price = '<span class="amount">' + currency_symbol + '&nbsp;' + price + '</span>';
			break;
			case 'right_space' :
				formatted_price = '<span class="amount">' + price + '&nbsp;' + currency_symbol + '</span>';
			break;
		}

		return formatted_price;
	}


	/**
	 * Trim trailing zeros off prices.
	 */
	function woocommerce_trim_zeros(price) {
		return price.replace(new RegExp(preg_quote(wc_price_calculator_params['woocommerce_price_decimal_sep'], '/') + '0+$'), '');
	}

});
