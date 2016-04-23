<?php
	/**
		** Shortcode countdown
		** Author: Smartaddons
	**/
class Ya_Woo_Countdown_Shortcode{
	public $id = 1;
	function __construct(){
		add_shortcode('ya_woo_countdown', array($this, 'ya_woo_countdown'));
	}
	function ya_woo_countdown( $atts, $content = NULL ){
		extract( shortcode_atts( array(
		'title' =>'',
		'style_title'=>'',
		'category_id'=>'',
		'orderby'=>'',
		'order'=>'',
		'numberposts' =>5,
		'length'=>'',
		'columns'=>'',
		'columns1'=>'',
		'columns2'=>'',
		'columns3'=>'',
		'columns4'=>'',
		'interval'=>'',
		'speed' => 1000,
		'autoplay' => 'false',
		'interval' => 5000,
		'number_slided' => 1,
		'template'=>'',
		'el_class'=>''
		), $atts ) );
	$this -> id = $this -> id + 1;
	$id = $this -> id;
	$slider_id = 'responsive_countdown_slider_'.$id;
	$args = array();
	if( $category_id != 0 ){
	$args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'ignore_sticky_posts'   => 1,
		'orderby' => $orderby,
		'order' => $order,
		'posts_per_page' => $numberposts,
		'tax_query'	=> array(
		array(
			'taxonomy'	=> 'product_cat',
			'field'		=> 'id',
			'terms'		=> $category_id)
			),
		'meta_query' => array(
			array(
				'key' => '_visibility',
				'value' => array('catalog', 'visible'),
				'compare' => 'IN'
			),
			array(
				'key' => '_sale_price',
				'value' => 0,
				'compare' => '>',
				'type' => 'NUMERIC'
			),
			array(
				'key' => '_sale_price_dates_to',
				'value' => 0,
				'compare' => '>',
				'type' => 'NUMERIC'
			)
		)
	);
	}else{
		$args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'ignore_sticky_posts'   => 1,
		'orderby' => $orderby,
		'order' => $order,
		'posts_per_page' => $numberposts,
		'meta_query' => array(
			array(
				'key' => '_visibility',
				'value' => array('catalog', 'visible'),
				'compare' => 'IN'
			),
			array(
				'key' => '_sale_price',
				'value' => 0,
				'compare' => '>',
				'type' => 'NUMERIC'
			),
			array(
				'key' => '_sale_price_dates_to',
				'value' => 0,
				'compare' => '>',
				'type' => 'NUMERIC'
			)
		)
	);
}
$i = 0;
$list = new WP_Query( $args );
$output ='<div  id="'.$slider_id.'" class="sw-countdown-product vc_element">';
 if($title != ''){
$output.= '<div class="block-title '.esc_attr( $style_title ).'">';
if($style_title == 'title3'){
	$wordChunks = explode(" ", $title);
	$firstchunk = $wordChunks[0];
	$secondchunk = $wordChunks[1];
$output.='<h2> <span>'.esc_html( $firstchunk ).'</span> <span class="text-color"> '.esc_html( $secondchunk ).' </span></h2></div>';
}else{
$output.=	'<h2>';
$output.= '<span>'.esc_html( $title ).'</span>';
$output.='</h2>';
$output.='</div>';
}
 }
$output.='<div class="row">';
$output.='<div class="carousel-inner responsive" data-speed="'.esc_attr( $interval ).'" data-slideshow1="'.esc_attr( $columns ).'" data-slideshow2="'.esc_attr( $columns1 ).'" data-slideshow3="'.esc_attr( $columns2 ).'" data-slideshow4="'.esc_attr( $columns3 ).'" data-slideshow5="'.esc_attr( $columns4 ).'">';
 while($list->have_posts()): $list->the_post();
	global $product, $post, $wpdb, $average,$yith_wcwl;
$yith_compare = new YITH_Woocompare_Frontend(); 	
	  $tag_id ='sw_woo_countdown_'.rand().time();
	$start_time = get_post_meta( $post->ID, '_sale_price_dates_from', true );
	$countdown_time = get_post_meta( $post->ID, '_sale_price_dates_to', true );	
	$orginal_price = get_post_meta( $post->ID, '_regular_price', true );	
	$symboy = get_woocommerce_currency_symbol( get_woocommerce_currency() );
		
	$output.='<div class="item">';
$output.='<div id="product_'.$id.$post->ID.'" class="item-product" data-scroll-reveal="enter top move 20px wait '.(0.2*$i).'s">';
$output.='<div class="item-wrap">';							
		if(has_post_thumbnail()){ 
$output.='<div class="item-img item-height">';
$output.='<div class="item-img-info products-thumb">';
$output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">';
ob_start();
        do_action( 'woocommerce_before_shop_loop_item_title' ); 
  $below_shortcode = ob_get_contents();
    ob_end_clean();
$output.= $below_shortcode;
$output.='</a>';
$output.=' </div></div>';
		 } 
$output.='<div class="product-countdown" data-price="'.esc_attr( $symboy.$orginal_price ).'" data-starttime="'.esc_attr( $start_time ).'" data-cdtime="'.esc_attr( $countdown_time ).'" data-id="product_'.$id.$post->ID.'"></div>';
$output.='<div class="item-content">';
		$average      = $product->get_average_rating();
$output.='<div class="reviews-content">';
$output.='<div class="star">'.( $average > 0 ) ?'<span style="width:'. ( $average*14 ).'px"></span>' : ''.' </div>';		
$output.=	'</div>';
$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.esc_html( $post->post_title ).'</a></h4>';
if ( $price_html = $product->get_price_html() ){
$output.='<div class="item-price">
				<span>
					'.$price_html.'
				</span>
			</div>';
			} 
			if( $length > 0 ){ 
									$output.='<div class="item-desc">'; 
									$text = $post->post_excerpt;
										$content = wp_trim_words($text, $length);
								 $output.= esc_html($content);
									$output.='</div>';				
									 } 
			$output.='<div class="item-bottom-grid clearfix">';
									$output.= apply_filters( 'woocommerce_loop_add_to_cart_link',
											sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button  %s product_type_%s"  title = "add to cart" >%s</a>',
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
										   $output.='<div class="woocommerce product compare-button">';
										    $output.='<a href=" '.esc_url($yith_compare->add_product_url( $product->id )).'" class="compare button" title="Add to Compare" data-product_id="'.esc_attr($product->id).'">'. esc_html('compare').'</a>';
											$output.='</div>';
											}
												if ( in_array( 'yith-woocommerce-wishlist/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
													$output.= do_shortcode( "[yith_wcwl_add_to_wishlist]" );
												}
											
										}
											$nonce = wp_create_nonce("ya_quickviewproduct_nonce");
											$link = admin_url('admin-ajax.php?ajax=true&amp;action=ya_quickviewproduct&amp;post_id='.$product->id.'&amp;nonce='.$nonce);
											$linkcontent ='<a href="'. esc_url( $link ) .'" data-fancybox-type="ajax" class="fancybox fancybox.ajax sm_quickview_handler" title="Quick View Product">'.apply_filters( 'out_of_stock_add_to_cart_text', __( 'Quick View', 'yatheme' ) ).'</a>';
											$output.= $linkcontent;
									
							$output.='</div>
		</div>											
	</div>
</div>';

	$output.='</div>';
	endwhile;
	wp_reset_postdata();
$output.='</div></div>
	</div>';
 return $output;
}
}
new Ya_Woo_Countdown_Shortcode();