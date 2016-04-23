<?php
	add_theme_support( 'woocommerce' );
	
	if ( is_single() && get_post_type() == 'product' ) {
			
		get_template_part('woocommerce/single', 'product');

	} elseif ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
		
		get_template_part('woocommerce/archive', 'product');

	} elseif ( is_post_type_archive( 'product' ) || is_page( woocommerce_get_page_id( 'shop' ) ) ) {
		
		get_template_part('woocommerce/archive', 'product');

	}
	
?>