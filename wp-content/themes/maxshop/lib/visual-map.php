<?php 
add_action( 'vc_before_init', 'my_shortcodeVC' );
function my_shortcodeVC(){
$target_arr = array(
	__( 'Same window', 'js_composer' ) => '_self',
	__( 'New window', 'js_composer' ) => "_blank"
);
$link_category = array( __( 'All Links', 'js_composer' ) => '' );
$link_cats     = get_categories();
if ( is_array( $link_cats ) ) {
	foreach ( $link_cats as $link_cat ) {
		$link_category[ $link_cat->name ] = $link_cat->term_id;
	}
}		
$args = array(
			'type' => 'post',
			'child_of' => 0,
			'parent' => 0,
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'number' => '',
			'taxonomy' => 'product_cat',
			'pad_counts' => false,

		);
		$product_categories_dropdown = array( __( 'All Category Product', 'js_composer' ) => '' );;
		$categories = get_categories( $args );
		foreach($categories as $category){
			$product_categories_dropdown[$category->name] = $category -> term_id;
		}
$menu_locations_array = array( __( 'All Links', 'js_composer' ) => '' );
$menu_locations = wp_get_nav_menus();	
foreach ($menu_locations as $menu_location){
	$menu_locations_array[$menu_location->name] = $menu_location -> term_id;
}

/* YTC VC */
//YTC post
vc_map( array(
	'name' => 'YTC_' . __( 'POSTS', 'yatheme' ),
	'base' => 'ya_post',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'Display posts-seclect category', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Style title', 'js_composer' ),
			'param_name' => 'style_title',
			'description' =>__( 'What text use as a style title. Leave blank to use default style title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Type post', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				'Select type',
				__( 'The_blog', 'js_composer' ) => 'the_blog',
				__( '2_column', 'js_composer' ) => '2_column',
				__( 'slide_show', 'js_composer' ) => 'slide_show',
				__( 'middle_right', 'js_composer' ) => 'middle_right',
				__( 'Our Member', 'js_composer' ) => 'indicators'
			),
			'description' => sprintf( __( 'Select different style posts.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
		array(
			'param_name'    => 'category_id',
			'type'          => 'dropdown',
			'value'         => $link_category, // here I'm stuck
			'heading'       => __('Category filter:', 'overmax'),
			'description'   => '',
			'holder'        => 'div',
			'class'         => ''
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of posts to show', 'js_composer' ),
			'param_name' => 'number',
			'admin_label' => true
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Excerpt length (in words)', 'js_composer' ),
			'param_name' => 'length',
			'description' => __( 'Excerpt length (in words).', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		),
			

		array(
			'type' => 'dropdown',
			'heading' => __( 'Order way', 'js_composer' ),
			'param_name' => 'order',
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC'
			),
			'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
				
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				'Select orderby',
				__( 'Date', 'js_composer' ) => 'date',
				__( 'ID', 'js_composer' ) => 'ID',
				__( 'Author', 'js_composer' ) => 'author',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Modified', 'js_composer' ) => 'modified',
				__( 'Random', 'js_composer' ) => 'rand',
				__( 'Comment count', 'js_composer' ) => 'comment_count',
				__( 'Menu order', 'js_composer' ) => 'menu_order'
			),
			'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
			
	)
) );

// ytc tesminial

vc_map( array(
	'name' => 'YTC_ ' . __( 'Testimonial Slide', 'yatheme' ),
	'base' => 'testimonial_slide',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'The tesminial on your site', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Style title', 'js_composer' ),
			'param_name' => 'style_title',
			'value' => array(
				'Select type',
				__( 'Style title 1', 'js_composer' ) => 'title1',
				__( 'Style title 2', 'js_composer' ) => 'title2',
				__( 'Style title 3', 'js_composer' ) => 'title3',
				__( 'Style title 4', 'js_composer' ) => 'title4'
			),
			'description' =>__( 'What text use as a style title. Leave blank to use default style title.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of posts to show', 'js_composer' ),
			'param_name' => 'numberposts',
			'admin_label' => true
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Excerpt length (in words)', 'js_composer' ),
			'param_name' => 'length',
			'description' => __( 'Excerpt length (in words).', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Template', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				__( 'indicators', 'js_composer' ) => 'indicators',
				__( 'Slide Style 1', 'js_composer' ) => 'slide1',
				__('Slide Style 2','js_composer') => 'slide2'
			),
			'description' => sprintf( __( 'Chose template for testimonial', 'js_composer' ) )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order way', 'js_composer' ),
			'param_name' => 'order',
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC'
			),
			'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
				
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				'Select orderby',
				__( 'Date', 'js_composer' ) => 'date',
				__( 'ID', 'js_composer' ) => 'ID',
				__( 'Author', 'js_composer' ) => 'author',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Modified', 'js_composer' ) => 'modified',
				__( 'Random', 'js_composer' ) => 'rand',
				__( 'Comment count', 'js_composer' ) => 'comment_count',
				__( 'Menu order', 'js_composer' ) => 'menu_order'
			),
			'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
			
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		)
	)
) );
//ytc our brand
vc_map( array(
	'name' => 'YTC_ ' . __( 'Brand', 'yatheme' ),
	'base' => 'OurBrand',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'The best sale  product on your site', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Style title', 'js_composer' ),
			'param_name' => 'style_title',
			'value' => array(
				'Select type',
				__( 'Style title 1', 'js_composer' ) => 'title1',
				__( 'Style title 2', 'js_composer' ) => 'title2',
				__( 'Style title 3', 'js_composer' ) => 'title3',
				__( 'Style title 4', 'js_composer' ) => 'title4'
			),
			'description' =>__( 'What text use as a style title. Leave blank to use default style title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Type display', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				'Select type',
				__( 'Type default', 'js_composer' ) => 'default',
				__( 'Type slide', 'js_composer' ) => 'slide',
			),
			'description' =>__( 'type you want display.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of posts to show', 'js_composer' ),
			'param_name' => 'numberposts',
			'admin_label' => true
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order way', 'js_composer' ),
			'param_name' => 'order',
			'value' => array(
				__( 'Descending', 'js_composer' ) => 'DESC',
				__( 'Ascending', 'js_composer' ) => 'ASC'
			),
			'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
				
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				'Select orderby',
				__( 'Date', 'js_composer' ) => 'date',
				__( 'ID', 'js_composer' ) => 'ID',
				__( 'Author', 'js_composer' ) => 'author',
				__( 'Title', 'js_composer' ) => 'title',
				__( 'Modified', 'js_composer' ) => 'modified',
				__( 'Random', 'js_composer' ) => 'rand',
				__( 'Comment count', 'js_composer' ) => 'comment_count',
				__( 'Menu order', 'js_composer' ) => 'menu_order'
			),
			'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Speed slide', 'js_composer' ),
			'param_name' => 'interval',
			'description' => __( 'Speed for slide', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
		    'heading' => __( 'Effect slide', 'js_composer' ),
			'param_name' => 'effect',
			'value' => array(
				__( 'Slide', 'js_composer' ) => 'slide',
				__( 'Fade', 'js_composer' ) => 'fade',
			),
				'description' => __( 'Effect for slide', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
		    'heading' => __( 'Hover slide', 'js_composer' ),
			'param_name' => 'hover',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'hover',
				__( 'No', 'js_composer' ) => '',
			),
				'description' => __( 'Hover for slide', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
		    'heading' => __( 'Swipe slide', 'js_composer' ),
			'param_name' => 'swipe',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'yes',
				__( 'No', 'js_composer' ) => 'no',
			),
				'description' => __( 'Swipe for slide', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns >1200px:', 'js_composer' ),
			'param_name' => 'columns',
			'description' => __( 'Number colums you want display  > 1200px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 768px to 1199px:', 'js_composer' ),
			'param_name' => 'columns1',
			'description' => __( 'Number colums you want display  on 768px to 1199px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 480px to 767px:', 'js_composer' ),
			'param_name' => 'columns2',
			'description' => __( 'Number colums you want display  on 480px to 767px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 321px to 479px:', 'js_composer' ),
			'param_name' => 'columns3',
			'description' => __( 'Number colums you want display  on 321px to 479px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns in 320px or less than:', 'js_composer' ),
			'param_name' => 'columns4',
			'description' => __( 'Number colums you want display  in 320px or less than.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		)
	)
) );
// ytc post slide
vc_map( array(
	'name' => 'YTC_' . __( 'SLIDE_POSTS', 'yatheme' ),
	'base' => 'postslide',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'Display posts-seclect category', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Style title', 'js_composer' ),
			'param_name' => 'style_title',
			'value' => array(
				'Select type',
				__( 'Style title 1', 'js_composer' ) => 'title1',
				__( 'Style title 2', 'js_composer' ) => 'title2',
				__( 'Style title 3', 'js_composer' ) => 'title3',
				__( 'Style title 4', 'js_composer' ) => 'title4'
			),
			'description' =>__( 'What text use as a style title. Leave blank to use default style title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Type post', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				'Select type',
				__( 'bottom', 'js_composer' ) => 'bottom',
				__( 'right', 'js_composer' ) => 'right',
				__( 'left', 'js_composer' ) => 'left',
				__( 'out', 'js_composer' ) => 'out'
			),
			'description' => sprintf( __( 'Select different style posts.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
		),
		array(
			'param_name'    => 'categories',
			'type'          => 'dropdown',
			'value'         => $link_category, // here I'm stuck
			'heading'       => __('Category filter:', 'overmax'),
			'description'   => '',
			'holder'        => 'div',
			'class'         => ''
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of posts to show', 'js_composer' ),
			'param_name' => 'limit',
			'admin_label' => true
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Excerpt length (in words)', 'js_composer' ),
			'param_name' => 'length',
			'description' => __( 'Excerpt length (in words).', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Speed slide', 'js_composer' ),
			'param_name' => 'interval',
			'description' => __( 'Speed slide', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		),
			

			
	)
) );
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_postslide extends WPBakeryShortCodesContainer {
    }
}
////YTC Woo Slide Shortcode
vc_map( array(
	'name' => 'YTC_' . __( 'WOO SLIDE', 'yatheme' ),
	'base' => 'ya_woo_slide',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'Display woo slide - seclect category', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Style title', 'js_composer' ),
			'param_name' => 'style_title',
			'value' => array(
				'Select type',
				__( 'Style title 1', 'js_composer' ) => 'title1',
				__( 'Style title 2', 'js_composer' ) => 'title2',
				__( 'Style title 3', 'js_composer' ) => 'title3',
				__( 'Style title 4', 'js_composer' ) => 'title4'
			),
			'description' =>__( 'What text use as a style title. Leave blank to use default style title.', 'js_composer' )
		),
		array(
			'type' => 'attach_image',
			'heading' => __( 'Image', 'js_composer' ),
			'param_name' => 'image',
			'value' => '',
			'description' => __( 'Select image from media library.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Theme shortcode want display', 'js_composer' ),
			'param_name' => 'template',
			'value' => array(
				'Select type',
				__( 'Upsell Products', 'js_composer' ) => 'default',
				__( 'Recommend Products', 'js_composer' ) => 'theme1',
				__( 'Child categories product', 'js_composer' ) => 'theme3',
				__('Left Child Cat','js_composer' )             => 'theme4',
			),
			'description' => sprintf( __( 'Select different style posts.', 'js_composer' ) )
		),
		array(
			'param_name'    => 'category_id',
			'type'          => 'dropdown',
			'value'         => $product_categories_dropdown, // here I'm stuck
			'heading'       => __('Category filter:', 'overmax'),
			'description'   => '',
			'holder'        => 'div',
			'class'         => ''
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of product to show', 'js_composer' ),
			'param_name' => 'numberposts',
			'admin_label' => true
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Excerpt length (in words)', 'js_composer' ),
			'param_name' => 'length',
			'description' => __( 'Excerpt length (in words).', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Speed slide', 'js_composer' ),
			'param_name' => 'interval',
			'description' => __( 'Speed for slide', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
		    'heading' => __( 'Effect slide', 'js_composer' ),
			'param_name' => 'effect',
			'value' => array(
				__( 'Slide', 'js_composer' ) => 'slide',
				__( 'Fade', 'js_composer' ) => 'fade',
			),
				'description' => __( 'Effect for slide', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
		    'heading' => __( 'Hover slide', 'js_composer' ),
			'param_name' => 'hover',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'hover',
				__( 'No', 'js_composer' ) => '',
			),
				'description' => __( 'Hover for slide', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
		    'heading' => __( 'Swipe slide', 'js_composer' ),
			'param_name' => 'swipe',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'yes',
				__( 'No', 'js_composer' ) => 'no',
			),
				'description' => __( 'Swipe for slide', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns >1200px:', 'js_composer' ),
			'param_name' => 'columns',
			'description' => __( 'Number colums you want display  > 1200px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 768px to 1199px:', 'js_composer' ),
			'param_name' => 'columns1',
			'description' => __( 'Number colums you want display  on 768px to 1199px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 480px to 767px:', 'js_composer' ),
			'param_name' => 'columns2',
			'description' => __( 'Number colums you want display  on 480px to 767px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 321px to 479px:', 'js_composer' ),
			'param_name' => 'columns3',
			'description' => __( 'Number colums you want display  on 321px to 479px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns in 320px or less than:', 'js_composer' ),
			'param_name' => 'columns4',
			'description' => __( 'Number colums you want display  in 320px or less than.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		),			
	)
) );
////YTC Woo CountDown Shortcode
vc_map( array(
	'name' => 'YTC_' . __( 'WOO Count Down', 'yatheme' ),
	'base' => 'ya_woo_countdown',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'Display woo slide - seclect category', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Style title', 'js_composer' ),
			'param_name' => 'style_title',
			'value' => array(
				'Select type',
				__( 'Style title 1', 'js_composer' ) => 'title1',
				__( 'Style title 2', 'js_composer' ) => 'title2',
				__( 'Style title 3', 'js_composer' ) => 'title3',
				__( 'Style title 4', 'js_composer' ) => 'title4'
			),
			'description' =>__( 'What text use as a style title. Leave blank to use default style title.', 'js_composer' )
		),
		array(
			'param_name'    => 'category_id',
			'type'          => 'dropdown',
			'value'         => $product_categories_dropdown, // here I'm stuck
			'heading'       => __('Category filter:', 'overmax'),
			'description'   => '',
			'holder'        => 'div',
			'class'         => ''
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of product to show', 'js_composer' ),
			'param_name' => 'numberposts',
			'admin_label' => true
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Excerpt length (in words)', 'js_composer' ),
			'param_name' => 'length',
			'description' => __( 'Excerpt length (in words).', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Speed slide', 'js_composer' ),
			'param_name' => 'interval',
			'description' => __( 'Speed for slide', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
		    'heading' => __( 'Effect slide', 'js_composer' ),
			'param_name' => 'effect',
			'value' => array(
				__( 'Slide', 'js_composer' ) => 'slide',
				__( 'Fade In', 'js_composer' ) => 'fadeIn',
				__( 'Slide Left', 'js_composer' ) => 'slideLeft',
				__( 'Slide Right', 'js_composer' ) => 'slideRight',
				__( 'Zoom Out', 'js_composer' ) => 'zoomOut',
				__( 'Zoom In', 'js_composer' ) => 'zoomIn',
				__( 'Flip', 'js_composer' ) => 'flip',
				__( 'Flip in Horizontal', 'js_composer' ) => 'flipInX',
				__( 'Flip in Vertical', 'js_composer' ) => 'flipInY',
				__( 'Star war', 'js_composer' ) => 'starwars',
				__( 'Bounce In', 'js_composer' ) => 'bounceIn',
				__( 'Page Top', 'js_composer' ) => 'pageTop',
			),
				'description' => __( 'Effect for slide', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns >1200px:', 'js_composer' ),
			'param_name' => 'columns',
			'description' => __( 'Number colums you want display  > 1200px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 768px to 1199px:', 'js_composer' ),
			'param_name' => 'columns1',
			'description' => __( 'Number colums you want display  on 768px to 1199px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 480px to 767px:', 'js_composer' ),
			'param_name' => 'columns2',
			'description' => __( 'Number colums you want display  on 480px to 767px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns on 321px to 479px:', 'js_composer' ),
			'param_name' => 'columns3',
			'description' => __( 'Number colums you want display  on 321px to 479px.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of Columns in 320px or less than:', 'js_composer' ),
			'param_name' => 'columns4',
			'description' => __( 'Number colums you want display  in 320px or less than.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		),			
	)
) );
//// vertical mega menu
vc_map( array(
	'name' => 'YTC ' . __( 'vertical mega menu', 'yatheme' ),
	'base' => 'ya_mega_menu',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'Display vertical mega menu', 'js_composer' ),
	'params' => array(
	    array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
	    array(
			'param_name'    => 'menu_locate',
			'type'          => 'dropdown',
			'value'         => $menu_locations_array, // here I'm stuck
			'heading'       => __('Category menu:', 'overmax'),
			'description'   => '',
			'holder'        => 'div',
			'class'         => ''
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Theme shortcode want display', 'js_composer' ),
			'param_name' => 'widget_template',
			'value' => array(
				__( 'default', 'js_composer' ) => 'default',
			),
			'description' => sprintf( __( 'Select different style menu.', 'js_composer' ) )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		),			
	)
));
///// Gallery 
vc_map( array(
	'name' => __( 'YTC_Gallery', 'js_composer' ),
	'base' => 'gallerys',
	'icon' => 'icon-wpb-images-carousel',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'description' => __( 'Animated carousel with images', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'js_composer' )
		),
		array(
			'type' => 'attach_images',
			'heading' => __( 'Images', 'js_composer' ),
			'param_name' => 'ids',
			'value' => '',
			'description' => __( 'Select images from media library.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'gallery size', 'js_composer' ),
			'param_name' => 'size',
			'description' => __( 'Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size. If used slides per view, this will be used to define carousel wrapper size.', 'js_composer' )
		),
		
		array(
			'type' => 'dropdown',
			'heading' => __( 'Gallery caption', 'js_composer' ),
			'param_name' => 'caption',
			'value' => array(
				__( 'true', 'js_composer' ) => 'true',
				__( 'false', 'js_composer' ) => 'false'
			),
			'description' => __( 'Images display caption true or false', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Gallery type', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				__( 'column', 'js_composer' ) => 'column',
				__( 'slide', 'js_composer' ) => 'slide',
				__( 'flex', 'js_composer' ) => 'flex'
			),
			'description' => __( 'Images display type', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'gallery columns', 'js_composer' ),
			'param_name' => 'columns',
			'description' => __( 'Enter gallery columns. Example: 1,2,3,4 ... Only use gallery type="column".', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Slider speed', 'js_composer' ),
			'param_name' => 'interval',
			'value' => '5000',
			'description' => __( 'Duration of animation between slides (in ms)', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Gallery event', 'js_composer' ),
			'param_name' => 'event',
			'value' => array(
				__( 'slide', 'js_composer' ) => 'slide',
				__( 'fade', 'js_composer' ) => 'fade'
			),
			'description' => __( 'event slide images', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		)
		)
) );
/////////////////// best sale /////////////////////
vc_map( array(
	'name' => 'YTC_' . __( 'Best Sale', 'yatheme' ),
	'base' => 'BestSale',
	'icon' => 'icon-wpb-ytc',
	'category' => __( 'My shortcodes', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => __( 'Display bestseller', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' )
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Style title', 'js_composer' ),
			'param_name' => 'style_title',
			'description' =>__( 'What text use as a style title. Leave blank to use default style title.', 'js_composer' )
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Template', 'js_composer' ),
			'param_name' => 'template',
			'value' => array(
				'Select type',
				__( 'Default', 'js_composer' ) => 'default',
				__( 'Slide', 'js_composer' ) => 'slide',
			),
			'description' => sprintf( __( 'Select different style best sale.', 'js_composer' ) )
		),
		
		array(
			'type' => 'textfield',
			'heading' => __( 'Number of posts to show', 'js_composer' ),
			'param_name' => 'number',
			'admin_label' => true
		),
		
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
		),	
	)
) );
}
?>