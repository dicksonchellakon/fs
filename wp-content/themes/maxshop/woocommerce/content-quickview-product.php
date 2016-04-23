<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
?>
<div id="quickview-container-<?php the_ID(); ?>">
	<div class="quickview-container woocommerce">
		<?php
        global $product;
            /**
             * woocommerce_before_single_product hook
             *
             * @hooked woocommerce_show_messages - 10
             */
             do_action( 'woocommerce_before_single_product' );
        ?>
        <div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class("product single-product"); ?>>
           <div class="single-product-top clearfix">
			   <?php
					/**
					 * woocommerce_show_product_images hook
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action( 'woocommerce_before_single_product_summary' );
				?>
			
				<div class="product-summary col-lg-6">
					  
					<?php
						/**
						 * woocommerce_single_product_summary hook
						 *
						 * @hooked woocommerce_template_single_title - 5
						 * @hooked woocommerce_template_single_price - 10
						 * @hooked woocommerce_template_single_excerpt - 20
						 * @hooked woocommerce_template_single_add_to_cart - 30
						 * @hooked woocommerce_template_single_meta - 40
						 * @hooked woocommerce_template_single_sharing - 50
						 */
						//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
						do_action( 'woocommerce_single_product_summary' );
					?>
					 <?php get_social(); ?>
				</div>
           </div><!-- .summary -->
		</div>
        
        <?php do_action( 'woocommerce_after_single_product' ); ?>
        <div class="clearfix"></div>
    </div>
</div>
<?php
	global $post, $wp, $woocommerce;
	$ajax_cart_en         = get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes' ? true : false;
	$assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
	$frontend_script_path = $assets_path . 'js/frontend/';
	$admin_url = admin_url('admin-ajax.php');
	$cart_url = $woocommerce->cart->get_cart_url();
?>
<script type='text/javascript'>
/* <![CDATA[ */
var wc_add_to_cart_params = {"ajax_url":"<?php echo str_replace('/', '\/', $admin_url); ?>","ajax_loader_url":"<?php echo str_replace('/', '\/', $assets_path); ?>\/images\/ajax-loader@2x.gif","i18n_view_cart":"View Cart","cart_url":"<?php echo str_replace('/', '\/', $cart_url); ?>","is_cart":"","cart_redirect_after_add":"no"};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo $frontend_script_path; ?>/add-to-cart.min.js'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var woocommerce_params = {"ajax_url":"<?php echo str_replace('/', '\/', $admin_url); ?>","ajax_loader_url":"<?php echo str_replace('/', '\/', $assets_path); ?>\/images\/ajax-loader@2x.gif"};
/* ]]> */
</script>
<script type='text/javascript'>
/* <![CDATA[ */
var wc_cart_fragments_params = {"ajax_url":"<?php echo str_replace('/', '\/', $admin_url); ?>","fragment_name":"wc_fragments"};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo $frontend_script_path; ?>/cart-fragments.min.js'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var wc_add_to_cart_variation_params = {"i18n_no_matching_variations_text":"Sorry, no products matched your selection. Please choose a different combination.","i18n_unavailable_text":"Sorry, this product is unavailable. Please choose a different combination."};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo $frontend_script_path; ?>/add-to-cart-variation.min.js'></script>