<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;

$new_product = get_post_meta( $post->ID, 'new_product', true );
?>
<div class="product-images col-lg-6 col-md-6 col-sm-12 col-xs-12 slider-loading">
    <?php do_action('woocommerce_product_thumbnails'); ?>
	<div id="flexslider-product-<?php echo esc_attr( $product->id ); ?>" class="flexslider-product">
	  <ul class="slides">
	    <?php if ( has_post_thumbnail() ) : ?>

		<?php endif; ?>
	    <?php
			$attachments = $product->get_gallery_attachment_ids();
			
			if ($attachments) {

				foreach ( $attachments as $key => $attachment ) { ?>
					
					<li>
						<?php if ($product->is_on_sale()) : ?>

							<?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'woocommerce' ).'</span>', $post, $product); ?>

						<?php endif; ?>
						<a href="<?php echo wp_get_attachment_url( $attachment,'full') ?> " rel="position:'inside',showTitle:false,adjustX:-4,adjustY:-4" class="cloud-zoom"><?php echo wp_get_attachment_image( $attachment, 'large' ); ?></a>
						
						<?php
							if( $new_product == 'yes' ){
								echo '<span class="new-product"></span>';
							}
						?>
					</li>
				
				<?php 
				}

			} else { 
				
				$image_id = get_post_thumbnail_id(); $image_url = wp_get_attachment_image_src($image_id,'large', true); ?>
				
				<li>
					<?php if ($product->is_on_sale()) : ?>

						<?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'woocommerce' ).'</span>', $post, $product); ?>

					<?php endif; ?>
					<a title="<?php the_title(); ?>" href="<?php echo esc_attr( $image_url[0] );  ?>" rel="position:'inside',showTitle:false,adjustX:-4,adjustY:-4" class="cloud-zoom"><?php the_post_thumbnail('full'); ?></a>
				</li>
				
			<?php } ?> 
		
	  </ul>				  
	</div>

	
	
<script tyle="text/javascript">
(function($) {
  "use strict";
	jQuery(document).ready(function(){
	  jQuery("#flex-thumbnail-<?php echo esc_attr( $product->id ); ?>").flexslider({
		animation: "slide",
		controlNav: false,
		direction: "vertical",
		animationLoop: true,
		slideshow: false,
		itemWidth: 98,				
		asNavFor: "#flexslider-product-<?php echo esc_attr( $product->id ); ?>"
	  });

	  jQuery("#flexslider-product-<?php echo esc_attr( $product->id ); ?>").flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: true,
		slideshow: false,
		sync: "#flex-thumbnail-<?php echo esc_attr( $product->id ); ?>",
		start: function(slider){
		  jQuery("body").removeClass("loading");
		}
	  });
	  jQuery('.product-images').removeClass('slider-loading');
	});
})(jQuery);
</script>
</div>
