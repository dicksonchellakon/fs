<?php
	/**
		** Shortcode slideshow
		** Author: Smartaddons
	**/
class Ya_Woo_Slider_Shortcode{
	function __construct(){
		add_shortcode('ya_woo_slide', array($this, 'ya_woo_slider'));
	}
	function ya_woo_slider( $atts, $content = NULL ){
		extract( shortcode_atts( array(
		'title' =>'',
		'image'=>'',
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
		'effect'=>'slide',
		'hover'=>'hover',
		'swipe'=>'yes',
		'template'=>'',
		'el_class'=>''
		), $atts ) );
	$yith_compare = new YITH_Woocompare_Frontend(); 	
if($template == 'theme3'){
	if( $category_id != 0 ){
	$default = array(
		'post_type' => 'product',
		'tax_query'	=> array(
		array(
			'taxonomy'	=> 'product_cat',
			'field'		=> 'id',
			'terms'		=> $category_id)),
		
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
$terms = get_term_by('id', $category_id, 'product_cat');
if($terms != null) {	
global $yith_wcwl,$product;
$args = array('child_of' => $category_id, 'parent' => $category_id);
$termchildren = get_terms( 'product_cat', $args);
//print '<pre>';var_dump($termchildren); print '</pre>';
//$termchildren = get_term_children( $category_id,'product_cat');
  $tag_id ='sw_woo_slider_'.rand().time();
  $output ='<div id="'.esc_attr( $tag_id ).'" class="sw-woo-container-slider flex-slider vc_element '.$el_class.'">';
  $output.= '<div class="block-title '.$style_title.'">';
  $output.=	'<h2>';
	$output.= '<span><a title="'.esc_attr( $terms->name ).'" href="' . get_term_link( $terms,'product_cat' ) . '">'.esc_html( $terms->name ).'</a></span>';
	$output.='</h2>';
	$output.=	'<div class="category-wrap-cat">
															<ul class="cat-list">';
						foreach ( $termchildren as $child ) {
							//var_dump($child);
							//$term = get_term_by( 'id', $child, 'product_cat' );
							$output.='<li class="item">
									<a href="' . get_term_link( $child,'product_cat' ) . '">' . esc_html( $child->name ) . '</a>
									</li>';
						}
	$output.='</ul>					
		</div>
		</div>';
	$output.='<div class="supercat-des">
			<a class="img-class" href="#" title="'.esc_attr( $terms->name ).'">'.wp_get_attachment_image( $image, 'full' ).'</a>		
			</div>';
	}else {
		  $tag_id ='sw_woo_slider_'.rand().time();
		  $output ='<div id="'.esc_attr( $tag_id ).'" class="sw-woo-container-slider flex-slider vc_element '.$el_class.'">';
		  $output.= '<div class="block-title '.$style_title.'">';
		  $output.=	'<h2>';
		  $output.= '<span>All Category</span>';
		  $output.='</h2>';
		  $output.='</div>';
		  
	}
	$output.='<div class="page-button">
				<ul class="control-button preload">
					<li class="preview"></li>
					<li class="next"></li>
				</ul>		
			</div>';
    
}
if($template=='theme4')	{
    if( $category_id != 0 ){
	$default = array(
		'post_type' => 'product',
		'tax_query'	=> array(
		array(
			'taxonomy'	=> 'product_cat',
			'field'		=> 'id',
			'terms'		=> $category_id)),
		
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
		$terms = get_term_by('id', $category_id, 'product_cat');	
		if($terms != null) {	
		global $yith_wcwl,$product;
		$yith_compare = new YITH_Woocompare_Frontend(); 	
		$args = array('child_of' => $category_id, 'parent' => $category_id);
		$termchildren = get_terms( 'product_cat', $args);
		  $tag_id ='sw_woo_slider_'.rand().time();
		  $output ='<div id="'.esc_attr( $tag_id ).'" class="sw-woo-container-slider  flex-slider vc_element '.$el_class.'">';
		  $output.= '<div class="block-title '.$style_title.'">';
		  $output.=	'<h2>';
			$output.= '<span><a title="'.esc_attr( $terms->name ).'" href="' . get_term_link( $terms,'product_cat' ) . '">'.esc_html( $terms->name ).'</a></span>';
			$output.='</h2>';
		}else {
			  $tag_id ='sw_woo_slider_'.rand().time();
			  $output ='<div id="'.esc_attr( $tag_id ).'" class="sw-woo-container-slider flex-slider vc_element '.$el_class.'">';
			  $output.= '<div class="block-title '.$style_title.'">';
			  $output.=	'<h2>';
			  $output.= '<span>All Category</span>';
			  $output.='</h2>';
			  $output.='</div>';
			  
		}
			$output.='<div class="page-button-index5">
				<ul class="customNavigation">
					<li class="preview btn-bs prev-bs icon-angle-left"></li>
					<li class="next btn-bs next-bs icon-angle-right"></li>
				</ul>		
			</div>';
			$output.='</div>';
		
			
}
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
if($template == 'theme4'){
	$output.='<div class="left-child">
				<div class="sub-super-category">
					<div class="sub-wrapper-cat">
					<a class="img-class" href="#" title="'.esc_attr( $terms->name ).'">'.wp_get_attachment_image( $image, 'full' ).'</a>																			
					    <ul class="cat-list">';
							foreach ( $termchildren as $child ) {
							//var_dump($child);
							//$term = get_term_by( 'id', $child, 'product_cat' );
							$output.='<li class="item">
									<a href="' . get_term_link( $child,'product_cat' ) . '">' . esc_html( $child->name ) . '</a>
									</li>';
						}
		$output.='</ul>					
				</div>
				</div>
			</div>';
			$output.='<div class="right-child slider not-js cols-6 '.$deviceclass_sfx.'" data-interval="'.esc_attr( $interval ).'" data-effect="'.esc_attr( $effect ).'" data-hover="'.esc_attr( $hover ).'" data-swipe="'.esc_attr( $swipe ).'">';
}else{
$output.='<div class="slider not-js cols-6 '.$deviceclass_sfx.'" data-interval="'.esc_attr( $interval ).'" data-effect="'.esc_attr( $effect ).'" data-hover="'.esc_attr( $hover ).'" data-swipe="'.esc_attr( $swipe ).'">';
}
$output.='<div class="vpo-wrap">
				<div class="vp">
					<div class="vpi-wrap">';
					while($list->have_posts()): $list->the_post();global $product, $post, $wpdb, $average;
$output.='<div class="item">
							<div class="item-wrap">';							
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
$output.='</div></div>';
	} 
$output.='<div class="item-content">';
									
	$average      = $product->get_average_rating();
	$output.='<div class="reviews-content">';
	$output.='<div class="star">'.( $average > 0 ) ?'<span style="width:'. ( $average*14 ).'px"></span>' : ''.' </div>';		
	$output.=	'</div>';
									$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.esc_html( $post->post_title ).'</a></h4>';
									 if ( $price_html = $product->get_price_html() ){
									$output.='<div class="item-price">
										<span>';
									$output.=$price_html; 
									$output.='</span>
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
											sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s"  class="button  %s product_type_%s" title="add to cart">%s</a>',
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
					 endwhile; wp_reset_postdata();
					$output.='</div>
				</div>
			</div>
		</div>';		
	$output.= '</div>';
 return $output;
}
}
new Ya_Woo_Slider_Shortcode();