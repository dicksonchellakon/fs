<?php
add_theme_support( 'woocommerce' );
/*

/*minicart via Ajax*/
add_filter('add_to_cart_fragments', 'ya_add_to_cart_fragment', 100);
 
function ya_add_to_cart_fragment( $fragments ) {
	ob_start();
	?>
	<?php get_template_part( 'woocommerce/minicart-ajax' ); ?>
	<?php
	$fragments['.top-form-minicart'] = ob_get_clean();
	return $fragments;
	
}
/*
add_filter( 'woocommerce_variable_price_html', 'ya_price_html', 100, 2 );
function ya_price_html( $price, $product ){
	$variation_id = get_post_meta( get_the_id(), '_min_regular_price_variation_id', true );
	$price        = get_post_meta( $variation_id, '_regular_price', true );
	return $price;
}*/
/* change position */
remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10);
remove_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt',20);
add_action('woocommerce_single_product_summary','woocommerce_template_single_price',20);
add_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt',10);
/*remove woo breadcrumb*/
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

/*YITH wishlist*/
if ( in_array( 'yith-woocommerce-wishlist/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
	//woocommerce_after_single_variation
	add_action( 'woocommerce_after_single_variation', 'ya_add_wishlist_variation', 10 );
	add_action( 'woocommerce_single_product_summary', 'ya_before_addcart', 28);
	//add_action( 'woocommerce_after_add_to_cart_button', 'ya_after_addcart', 38);
	add_action('woocommerce_after_shop_loop_item','ya_add_loop_compare_link', 20);
	add_action( 'woocommerce_after_shop_loop_item', 'ya_add_loop_wishlist_link', 25 );
	add_action( 'woocommerce_after_add_to_cart_button', 'ya_add_wishlist_link', 10);
	function ya_before_addcart(){
		echo '<div class="product-summary-bottom clearfix">';
	}
	function ya_after_addcart(){
		echo '</div>';
	}
	function ya_add_loop_compare_link(){
     echo do_shortcode("[yith_compare_button]");										
	}
	function ya_add_loop_wishlist_link(){
		echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
	}
	function ya_add_wishlist_link(){
		global $product;
		$yith_compare = new YITH_Woocompare_Frontend();
		add_shortcode( 'yith_compare_button', array( $yith_compare , 'compare_button_sc' ) );
		if( $product->product_type != 'variable' ){
			
			echo do_shortcode( "[yith_compare_button]" );
			echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
			
		}else{
			return ;
		}
	}
	function ya_add_wishlist_variation(){	
		$yith_compare = new YITH_Woocompare_Frontend();
		add_shortcode( 'yith_compare_button', array( $yith_compare , 'compare_button_sc' ) );

		echo do_shortcode( "[yith_compare_button]" );
		echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
	}
}

/*add second thumbnail loop product*/
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'ya_woocommerce_template_loop_product_thumbnail', 10 );
	function ya_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
		global $post;
		$html = '';
		$id = get_the_ID();
		$gallery = get_post_meta($id, '_product_image_gallery', true);
		$attachment_image = '';
		/*if(!empty($gallery)) {
			$gallery = explode(',', $gallery);
			$first_image_id = $gallery[0];
			$attachment_image = wp_get_attachment_image($first_image_id , $size, false, array('class' => 'hover-image back'));
		}*/
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), '' );
		if ( has_post_thumbnail() ){
			if( $attachment_image ){
				$html .= '<div class="product-thumb-hover">';
				$html .= (get_the_post_thumbnail( $post->ID, $size )) ? get_the_post_thumbnail( $post->ID, $size ): '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.$size.'.png" alt="No thumb">';
				$html .= $attachment_image;
				$html .= '</div>';
			}else{
				$html .= (get_the_post_thumbnail( $post->ID, $size )) ? get_the_post_thumbnail( $post->ID, $size ): '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.$size.'.png" alt="No thumb">';
			}			
			return $html;
		}else{
			$html .= '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.$size.'.png" alt="No thumb">';
			return $html;
		}
	}
	function ya_woocommerce_template_loop_product_thumbnail(){
		echo ya_product_thumbnail();
	}
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
/*
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'ya_woocommerce_template_single_excerpt', 35 );
function ya_woocommerce_template_single_excerpt() {
	wc_get_template( 'single-product/short-description.php' );
}
*/
/*filter order*/
function ya_addURLParameter($url, $paramName, $paramValue) {
     $url_data = parse_url($url);
     if(!isset($url_data["query"]))
         $url_data["query"]="";

     $params = array();
     parse_str($url_data['query'], $params);
     $params[$paramName] = $paramValue;
     $url_data['query'] = http_build_query($params);
     return ya_build_url($url_data);
}


function ya_build_url($url_data) {
 $url="";
 if(isset($url_data['host']))
 {
	 $url .= $url_data['scheme'] . '://';
	 if (isset($url_data['user'])) {
		 $url .= $url_data['user'];
			 if (isset($url_data['pass'])) {
				 $url .= ':' . $url_data['pass'];
			 }
		 $url .= '@';
	 }
	 $url .= $url_data['host'];
	 if (isset($url_data['port'])) {
		 $url .= ':' . $url_data['port'];
	 }
 }
 if (isset($url_data['path'])) {
	$url .= $url_data['path'];
 }
 if (isset($url_data['query'])) {
	 $url .= '?' . $url_data['query'];
 }
 if (isset($url_data['fragment'])) {
	 $url .= '#' . $url_data['fragment'];
 }
 return $url;
}

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action('woocommerce_before_shop_loop', 'ya_woocommerce_catalog_ordering', 30);
add_action('woocommerce_before_shop_loop', 'ya_woocommerce_pagination', 35);
add_action('woocommerce_after_shop_loop', 'ya_woocommerce_catalog_ordering', 15);
add_action('woocommerce_before_shop_loop','ya_woommerce_view_mode_wrap',15);
add_action( 'woocommerce_after_shop_loop', 'ya_woommerce_view_mode_wrap', 5 );
remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
add_action('woocommerce_message','wc_print_notices', 10);

function ya_woommerce_view_mode_wrap () {
	$product_grid = isset($_COOKIE['product_grid']) ? $_COOKIE['product_grid'] : 0;
	$grid_sel = ( $product_grid == 1 ) ? 'sel' : '';
	$list_sel = ( $product_grid == 0 ) ? 'sel' : '';
	$html  = '';
	$html .= '<ul class="view-mode-wrap">
		<li class="view-grid '.$list_sel.'">
			<a></a>
		</li>
		<li class="view-list '.$grid_sel.'">
			<a></a>
		</li>
	</ul>';
	echo $html;
}

function ya_woocommerce_pagination() {
	wc_get_template( 'loop/pagination.php' );
}

function ya_woocommerce_catalog_ordering() {
	global $data;

	parse_str($_SERVER['QUERY_STRING'], $params);

	$query_string = '?'.$_SERVER['QUERY_STRING'];

	// replace it with theme option
	if($data['woo_items']) {
		$per_page = $data['woo_items'];
	} else {
		$per_page = 8;
	}

	$pob = !empty($params['product_orderby']) ? $params['product_orderby'] : 'date';
	$po = !empty($params['product_order'])  ? $params['product_order'] : 'desc';
	$pc = !empty($params['product_count']) ? $params['product_count'] : $per_page;

	$html = '';
	$html .= '<div class="catalog-ordering clearfix">';

	$html .= '<div class="orderby-order-container">';

	$html .= '<ul class="orderby order-dropdown">';
	$html .= '<li>';
	$html .= '<span class="current-li"><span class="current-li-content"><a>'.__('Sort by', 'yatheme').'</a></span></span>';
	$html .= '<ul>';
	$html .= '<li class="'.(($pob == 'default') ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_orderby', 'default').'">'.__('Sort by ', 'yatheme').__('Default', 'yatheme').'</a></li>';
	$html .= '<li class="'.(($pob == 'name') ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_orderby', 'name').'">'.__('Sort by ', 'yatheme').__('Name', 'yatheme').'</a></li>';
	$html .= '<li class="'.(($pob == 'price') ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_orderby', 'price').'">'.__('Sort by ', 'yatheme').__('Price', 'yatheme').'</a></li>';
	$html .= '<li class="'.(($pob == 'date') ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_orderby', 'date').'">'.__('Sort by ', 'yatheme').__('Date', 'yatheme').'</a></li>';
	$html .= '<li class="'.(($pob == 'rating') ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_orderby', 'rating').'">'.__('Sort by ', 'yatheme').__('Rating', 'yatheme').'</a></li>';
	$html .= '</ul>';
	$html .= '</li>';
	$html .= '</ul>';
    $html .= '<ul class="order">';
	if($po == 'desc'):
	$html .= '<li class="desc"><a href="'.ya_addURLParameter($query_string, 'product_order', 'asc').'"><i class="icon-arrow-up"></i></a></li>';
	endif;
	if($po == 'asc'):
	$html .= '<li class="asc"><a href="'.ya_addURLParameter($query_string, 'product_order', 'desc').'"><i class="icon-arrow-down"></i></a></li>';
	endif;
	$html .= '</ul>';
	$html .= '<ul class="sort-count order-dropdown">';
	$html .= '<li>';
	$html .= '<span class="current-li"><a>'.__('8', 'yatheme').'</a></span>';
	$html .= '<ul>';
	$html .= '<li class="'.(($pc == $per_page) ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_count', $per_page).'">'.$per_page.'</a></li>';
	$html .= '<li class="'.(($pc == $per_page*2) ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_count', $per_page*2).'">'.($per_page*2).'</a></li>';
	$html .= '<li class="'.(($pc == $per_page*3) ? 'current': '').'"><a href="'.ya_addURLParameter($query_string, 'product_count', $per_page*3).'">'.($per_page*3).'</a></li>';
	$html .= '</ul>';
	$html .= '</li>';
	$html .= '</ul>';
	$html .= '</div>';
	$html .= '</div>';
	
	echo $html;
}


add_action('woocommerce_get_catalog_ordering_args', 'ya_woocommerce_get_catalog_ordering_args', 20);
function ya_woocommerce_get_catalog_ordering_args($args)
{
	global $woocommerce;

	parse_str($_SERVER['QUERY_STRING'], $params);

	$pob = !empty($params['product_orderby']) ? $params['product_orderby'] : 'date';
	$po = !empty($params['product_order'])  ? $params['product_order'] : 'desc';

	switch($pob) {
		case 'date':
			$orderby = 'date';
			$order = 'desc';
			$meta_key = '';
		break;
		case 'price':
			$orderby = 'meta_value_num';
			$order = 'asc';
			$meta_key = '_price';
		break;
		case 'popularity':
			$orderby = 'meta_value_num';
			$order = 'desc';
			$meta_key = 'total_sales';
		break;
		case 'title':
			$orderby = 'title';
			$order = 'asc';
			$meta_key = '';
		break;
		case 'default':
		default:
			$orderby = 'menu_order title';
			$order = 'asc';
			$meta_key = '';
		break;
	}

	switch($po) {
		case 'desc':
			$order = 'desc';
		break;
		case 'asc':
			$order = 'asc';
		break;
		default:
			$order = 'asc';
		break;
	}

	$args['orderby'] = $orderby;
	$args['order'] = $order;
	$args['meta_key'] = $meta_key;

	if( $pob == 'rating' ) {
		$args['orderby']  = 'menu_order title';
		$args['order']    = $po == 'desc' ? 'desc' : 'asc';
		$args['order']	  = strtoupper( $args['order'] );
		$args['meta_key'] = '';

		add_filter( 'posts_clauses', 'ya_order_by_rating_post_clauses' );
	}

	return $args;
}
add_filter('loop_shop_per_page', 'ya_loop_shop_per_page');
function ya_loop_shop_per_page()
{
	global $data;

	parse_str($_SERVER['QUERY_STRING'], $params);

	if($data['woo_items']) {
		$per_page = $data['woo_items'];
	} else {
		$per_page = 8;
	}

	$pc = !empty($params['product_count']) ? $params['product_count'] : $per_page;

	return $pc;
}
/*********QUICK VIEW PRODUCT**********/

add_action("wp_ajax_ya_quickviewproduct", "ya_quickviewproduct");
add_action("wp_ajax_nopriv_ya_quickviewproduct", "ya_quickviewproduct");
function ya_quickviewproduct(){
	
	$productid = (isset($_REQUEST["post_id"]) && $_REQUEST["post_id"]>0) ? $_REQUEST["post_id"] : 0;
	
	$query_args = array(
		'post_type'	=> 'product',
		'p'			=> $productid
	);
	$outputraw = $output = '';
	$r = new WP_Query($query_args);
	if($r->have_posts()){ 

		while ($r->have_posts()){ $r->the_post(); setup_postdata($r->post);
			global $product;
			ob_start();
			woocommerce_get_template_part( 'content', 'quickview-product' );
			$outputraw = ob_get_contents();
			ob_end_clean();
		}
	}
	$output = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $outputraw);
	echo $output;exit();
}

?>