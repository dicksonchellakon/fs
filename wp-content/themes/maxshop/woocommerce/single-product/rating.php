<?php
/*
 * Single Product Rating
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.3.2
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
	return;
}
	$rating_count = $product->get_rating_count();
	$review_count = $product->get_review_count();
	$average      = $product->get_average_rating();
?>
<div class="reviews-content">
	<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*12 ).'px"></span>' : ''; ?></div>
		<a href="#reviews" class="woocommerce-review-link" rel="nofollow"><?php printf( _n( '%s vote', '%s vote(s)', $rating_count, 'woocommerce' ), '<span itemprop="ratingCount" class="count">' . $rating_count . '</span>' ); ?></a>
</div>
<?php $stock = ( $product->is_in_stock() )? 'in-stock' : 'out-stock' ; ?>
<div class="product-stock <?php echo esc_attr( $stock ); ?>">
	<?php _e( 'Availability', 'yatheme' ); ?>: 
	<span><?php echo ( $product->is_in_stock() )? __( 'In stock', 'yatheme' ) : __( 'Out stock', 'yatheme' ); ?></span>
</div>