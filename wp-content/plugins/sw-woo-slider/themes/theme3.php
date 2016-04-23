<?php 	
	if( $category != 0 ){
	$default = array(
		'post_type' => 'product',
		'tax_query'	=> array(
		array(
			'taxonomy'	=> 'product_cat',
			'field'		=> 'id',
			'terms'		=> $category)),
		
		'orderby'		=> $orderby,
		'order'			=> $order,
		'post_status' 	=> 'publish',
		'showposts' 	=> $numberposts
	);
	}else{
		$default = array(
			'post_type' => 'product',
			'orderby' => $orderby,
			'order' => $order,
			'post_status' => 'publish',
			'showposts' => $numberposts
		);
	}
	$list = new WP_Query( $default );
$termchildren = get_term_children( $category,'product_cat');
$terms = get_term_by('id', $category, 'product_cat');	
$thumbnail_id = get_woocommerce_term_meta( $category, 'thumbnail_id', true );
$image = wp_get_attachment_url( $thumbnail_id );
echo '<a href="' . get_term_link( $terms,'product_cat' ) . '">'.$terms->name.'</a>';
echo '<img src="'.$image.'" alt="'.$terms->name.'"/>';
echo '<ul>';
foreach ( $termchildren as $child ) {
	$term = get_term_by( 'id', $child, 'product_cat' );
	echo '<li><a href="' . get_term_link( $child,'product_cat' ) . '">' . $term->name . '</a></li>';
}
echo '</ul>';
	do_action( 'before' ); 
	if ( count($list) > 0 ){
		$tag_id ='sw_woo_slider_'.rand().time();
	?>
		<?php if( $title1 != '' || $desc != '' ){ ?>
        <div class="box-recommend-title">
		<div class="order-title">
			<?php
					$titles = strpos($title1, ' ');
					$title = ($titles !== false) ? '<span>' . substr($title1, 0, $titles) . '</span>' .' '. substr($title1, $titles + 1): $title1 ;
					echo '<h3>'. $title .'</h3>';
				?>
		</div>
        </div>
		<?php } ?>
	<div id="<?php echo $tag_id; ?>" class="sw-woo-container-slider recommend-product">
			<div class="page-button">
				<ul class="control-button preload">
					<li class="preview"></li>
					<li class="next"></li>
				</ul>		
			</div>		
		<?php 
		$count_items = 0;
		if($numberposts >= $list->found_posts){$count_items = $list->found_posts; }else{$count_items = $numberposts;}
		//var_dump($list);
		if($columns > $count_items){
			$columns = $count_items;
		}
		
		if($columns1 > $count_items){
			$columns1 = $count_items;
		}
		
		if($columns2 > $count_items){
			$columns2 = $count_items;
		}
		
		if($columns3 > $count_items){
			$columns3 = $count_items;
		}
		
		if($columns4 > $count_items){
			$columns4 = $count_items;
		}
		
		$deviceclass_sfx = 'preset01-'.$columns.' '.'preset02-'.$columns1.' '.'preset03-'.$columns2.' '.'preset04-'.$columns3.' '.'preset05-'.$columns4;
		
		?>
		<div class="slider not-js cols-6 <?php echo $deviceclass_sfx; ?>" data-interval="<?php echo $interval?>" data-effect="<?php echo $effect?>" data-hover="<?php echo $hover?>">
			<div class="vpo-wrap">
				<div class="vp">
					<div class="vpi-wrap">
					<?php while($list->have_posts()): $list->the_post();global $product, $post, $wpdb, $average;?>
						<div class="item">
							<div class="item-wrap">							
								<?php if(has_post_thumbnail()){ ?>
								<div class="item-img item-height">
									<div class="item-img-info products-thumb">
										<a href="<?php the_permalink();?>" title="<?php the_title_attribute();?>">
											<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
										</a>
										<?php
											$nonce = wp_create_nonce("ya_quickviewproduct_nonce");
											$link = admin_url('admin-ajax.php?ajax=true&action=ya_quickviewproduct&post_id='.$product->id.'&nonce='.$nonce);
											$linkcontent ='<a href="'. $link .'" data-fancybox-type="ajax" class="fancybox fancybox.ajax" title="Quick View Product">'.apply_filters( 'out_of_stock_add_to_cart_text', __( 'Quick View', 'yatheme' ) ).'</a>';
											echo $linkcontent;
										?>
									</div>
								</div>
								<?php } ?>
								<div class="item-content">
									<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h4>
									<?php
										$count = $wpdb->get_var("
											SELECT COUNT(meta_value) FROM $wpdb->commentmeta
											LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
											WHERE meta_key = 'rating'
											AND comment_post_ID = $post->ID
											AND comment_approved = '1'
											AND meta_value > 0
										");

										$rating = $wpdb->get_var("
											SELECT SUM(meta_value) FROM $wpdb->commentmeta
											LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
											WHERE meta_key = 'rating'
											AND comment_post_ID = $post->ID
											AND comment_approved = '1'
										");
										?>
										<div class="reviews-content">
										<?php
											if( $count > 0 ){
												$average = number_format($rating / $count, 1);
										?>
											<div class="star"><span style="width: <?php echo ($average*14).'px'; ?>"></span></div>
											
										<?php } else { ?>
										
											<div class="star"></div>
											
										<?php } ?>
											<div class="item-number-rating">
												<?php echo $count; _e(' review(s)', 'yatheme');?>
											</div>
									</div>
									<?php if ( $price_html = $product->get_price_html() ){?>
									<div class="item-price">
										<span>
											<?php echo $price_html; ?>
										</span>
									</div>
									<?php } ?>
									<?php if( $length > 0 ){ ?>
									<div class="item-desc">
										<?php 
											$content = $post->post_excerpt;
											echo $this->ya_trim_words($content, $length, '...');
										?>
									</div>				
									<?php } ?>
									<div class="item-bottom clearfix">
										<?php echo apply_filters( 'woocommerce_loop_add_to_cart_link',
											sprintf( '<a href="%s" rel="nofollow" title="Add To Cart" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
												esc_url( $product->add_to_cart_url() ),
												esc_attr( $product->id ),
												esc_attr( $product->get_sku() ),
												$product->is_purchasable() ? 'add_to_cart_button' : '',
												esc_attr( $product->product_type ),
												esc_html( $product->add_to_cart_text() )
											),
										$product );
										if ( in_array( 'yith-woocommerce-compare/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || in_array( 'yith-woocommerce-wishlist/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
											if ( in_array( 'yith-woocommerce-compare/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
												echo do_shortcode('[yith_compare_button]');
											}
											if ( in_array( 'yith-woocommerce-wishlist/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
												echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
											}
										}
										?>
									</div>
								</div>											
							</div>
						</div>
					<?php endwhile; wp_reset_query();?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
<?php
} 
?>