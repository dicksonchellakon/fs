<?php
/**
 * storefront engine room
 *
 * @package storefront
 */

/**
 * Initialize all the things.
 */
require get_template_directory() . '/inc/init.php';

/**
 * Note: Do not add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * http://codex.wordpress.org/Child_Themes
 */

function sv_change_sku_value( $sku, $product ) {

    // Change the generated SKU to use the product's post ID instead of the slug
    $sku = $product->get_post_data()->ID;
    return 'FSKU'.$sku;
}
add_filter( 'wc_sku_generator_sku', 'sv_change_sku_value', 10, 2 );
