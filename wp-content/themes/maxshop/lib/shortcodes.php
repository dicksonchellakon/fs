<?php
	include(get_template_directory().'/lib/shortcodes/slider.php');
	include(get_template_directory().'/lib/shortcodes/portfolio.php');
	include(get_template_directory().'/lib/shortcodes/gallery.php');
	include(get_template_directory().'/lib/shortcodes/skills.php');
	include(get_template_directory().'/lib/shortcodes/woo_slider.php');
	include(get_template_directory().'/lib/shortcodes/woo_countdown.php');
	//include(get_template_directory().'/lib/shortcodes/counterbox.php');
	function ya_shortcode_css() {
		wp_enqueue_style('yashortcode_css', get_template_directory_uri().'/css/shortcode_admin.css');
	}
	add_action('admin_enqueue_scripts', 'ya_shortcode_css');
class YA_Shortcodes{
	protected $supports = array();

	protected $tags = array( 'icon','youtube video', 'button', 'alert', 'bloginfo', 'colorset', 'slideshow', 'googlemaps', 'tabs', 'collapses', 'columns', 'row', 'col', 'code', 'breadcrumb', 'pricing','tooltip','modal','recent_posts','gallery_image');

	public function __construct(){
		add_action('admin_head', array($this, 'mce_inject') );
		$this->add_shortcodes();
	}

	public function mce_inject(){
		global $typenow;
		// check user permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
		return;
		}
			// verify the post type
		if( ! in_array( $typenow, array( 'post', 'page' ) ) )
			return;
		// check if WYSIWYG is enabled
		if ( get_user_option('rich_editing') == 'true') {
			add_filter( 'mce_external_plugins', array($this, 'mce_external_plugins') );
			add_filter( 'mce_buttons', array($this,'mce_buttons') );
		}
	}
	
	public function mce_external_plugins($plugin_array) {
		$wp_version = get_bloginfo( 'version' );
		if ( version_compare( $wp_version, '3.9', '>=' ) ) {
			$plugin_array['ya_shortcodes'] = get_template_directory_uri().'/js/ya_shortcodes_tinymce.js';
		}else{
			$plugin_array['ya_shortcodes'] = get_template_directory_uri().'/js/ya_shortcodes_tinymce_old.js';
		}
		return $plugin_array;
	}
	
	public function mce_buttons($buttons) {
		array_push($buttons, "ya_shortcodes");
		return $buttons;
	}
	
	public function add_shortcodes(){
		if ( is_array($this->tags) && count($this->tags) ){
			foreach ( $this->tags as $tag ){
				add_shortcode($tag, array($this, $tag));
			}
		}
	}
	
	function code($attr, $content) {
		$html = '';
		$html .= '<pre>';
		$html .= $content;
		$html .= '</pre>';
		
		return $html;
	}
	
	function icon( $atts ) {
		
		// Attributes
		extract( shortcode_atts(
			array(
				'tag' => 'span',
				'name' => '*',
				'class' => '',
				'border'=>'',
				'bg'    =>'',
				'color' => ''
			), $atts )
		);
		$attributes = array();
	
		$classes = preg_split('/[\s,]+/', $class, -1, PREG_SPLIT_NO_EMPTY);
		
		if ( !preg_match('/icon-/', $name) ){
			$name = 'icon-'.$name;
		}
		array_unshift($classes, $name);
		
		$classes = array_unique($classes);
		
		$attributes[] = 'class="'.implode(' ', $classes).'"';
		if(!empty($color)&&!empty($bg)&&!empty($border)){
			$attributes[] = 'style="color: '.$color.';background:'.$bg.';border:1px solid '.$border.'"';
		}
		if ( !empty($color) ){
			$attributes[] = 'style="color: '.$color.'"';
		}
		
		// Code
		return "<$tag ".implode(' ', $attributes)."></$tag>";
	}
	
	public function button( $atts, $content = null ){
		// Attributes
		extract( shortcode_atts(
			array(
				'id' => '',
				'tag' => 'span',
				'class' => 'btn',
				'target' => '',
				'type' => 'default',
				'border' =>'',
				'color' =>'',
				'size'	=> '',
				'icon' => '',
				'href' => '#'
			), $atts )
		);
		$attributes = array();
		
		$classes = $class;
		if ( $type != '' ){
			$type = ' btn-'.$type;
		}
		if( $size != '' ){
			$size = 'btn-'.$size;
		}
		$classes .= $type.' '.$size;
		$attributes[] = 'class="'.$classes.'"';
		if ( !empty($id) ){
			$attributes[] = 'id="'.esc_attr($id).'"';
		}
		if ( !empty($target) ){
			if ( 'a' == $tag ){
				$attributes[] = 'target="'.esc_attr($target).'"';
			} else {
				// html5
				$attributes[] = 'data-target="'.esc_attr($target).'"';
			}
		}
		
		if ( 'a' == $tag ){
			$attributes[] = 'href="'.esc_attr($href).'"';
		}
		if( $icon != '' ){
			$icon = '<i class="'.$icon.'"></i>';
		}
		return "<$tag ".implode(' ', $attributes).">".$icon."".do_shortcode($content)."</$tag>";
	}
	
	/**
	 * Alert
	 * */
	public function alert($atts, $content = null ){

		extract(shortcode_atts(array(
				'tag' => 'div',
				'class' => 'block',
				'dismiss' => 'true',
				'icon'  => '',
				'color'	=> '',
				'border' => '',
				'type' => ''
			), $atts)
		);
		
		$attributes = array();
		$attributes[] = $tag;
		$classes = array();
		$classes = preg_split('/[\s,]+/', $class, -1, PREG_SPLIT_NO_EMPTY);
		
		if ( !preg_match('/alert-/', $type) ){
			$type = 'alert-'.$type;
		}
		if( $color != '' || $border != '' ){
			$attributes[] .= 'style="color: '.$color.'; border-color:'.$border.'"';
		}
		array_unshift($classes, 'alert', $type);
		$classes = array_unique($classes);
		$attributes[] = 'class="'.implode(' ', $classes).'"';
		
		$html = '';
		$html .= '<'.implode(' ', $attributes).'>';
		if( $icon != '' ){
			$html .= '<i class="'.$icon.'"></i>';
		}
		if ($dismiss == 'true') {
			$html .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		}
		$html .= do_shortcode($content);
		$html .= '</'.$tag.'>';
		return $html;
	}


	/**
	 * Bloginfo
	 * */
	function bloginfo( $atts){
		extract( shortcode_atts(array(
				'show' => 'wpurl',
				'filter' => 'raw'
			), $atts)
		);
		$html = '';
		$html .= get_bloginfo($show, $filter);

		return $html;
	}
	
	function colorset($atts){
		$value = ya_options()->getCpanelValue('scheme'); 
		return $value;
	}
	
	/**
	 * Google Maps
	 */
	function googlemaps($atts, $content = null) {
		extract(shortcode_atts(array(
		"title" => '',
		"location" => '',
		"width" => '', //leave width blank for responsive designs
		"height" => '300',
		"zoom" => 10,
		"align" => '',
		), $atts));

		// load scripts
		wp_enqueue_script('ya_googlemap',  get_template_directory_uri() . '/js/ya_googlemap.js', array('jquery'), '', true);
		wp_enqueue_script('ya_googlemap_api', 'https://maps.googleapis.com/maps/api/js?sensor=false', array('jquery'), null, true);

		$output = '<div id="map_canvas_'.rand(1, 100).'" class="googlemap" style="height:'.$height.'px;width:'.$width.'">';
		$output .= (!empty($title)) ? '<input class="title" type="hidden" value="'.esc_attr( $title ).'" />' : '';
		$output .= '<input class="location" type="hidden" value="'.esc_attr( $location ).'" />';
		$output .= '<input class="zoom" type="hidden" value="'.esc_attr( $zoom ).'" />';
		$output .= '<div class="map_canvas"></div>';
		$output .= '</div>';

		return $output;
	}


	/**
	 * Tabs
	 * */
	public function tabs( $atts , $content = null ) {
		static $key = 0;
		
		if (is_array($atts) ) {
				
			foreach ($atts as $k => $att){
				$att = trim($att);
				if (empty($att)) unset( $atts[$k] );
			}
		}
		$yaTab_id = 'yaTab-'.$key;
		extract(shortcode_atts(array(
				'tag' => 'div',
				'class' => 'tabbable',
				'position' => 'top'
			), $atts));
		$atts['id'] = $yaTab_id;
		$classes = array();
		$classes = preg_split('/[\s,]+/', $class, -1, PREG_SPLIT_NO_EMPTY);
		array_unshift($classes, 'tabbable');
		
		if ( $position == 'left' ) array_unshift($classes, 'tabs-left');
		elseif ( $position == 'right' ) array_unshift($classes, 'tabs-right');
		elseif ($position == 'bottom') array_unshift($classes, 'tabs-below');
		
		$classes = array_unique($classes);
		$classes = ' class="'.implode(' ', $classes).'"';
		
		$html = '';
		$html .= '<'.$tag.$classes.' id="'.esc_attr( $yaTab_id ).'">';
		$html .= self::get_tab($atts, $content);
		$html .= '</'.$tag.'>';
		$key++;
		
		return $html;
	}
	
	
	protected function get_tab($atts, $content){
		
		if (preg_match_all('/\[(\[?)(tab)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/', $content, $m)) {
			$titles = array();
			$contents = array();
			$icons = array();
			for ($i=0; $i<count($m[0]); $i++) {			
				preg_match_all("/title=&#8221;(.*)&[#8243;,#8221;]/iU", $m[3][$i], $output_title);
				preg_match_all("/icon=&#8221;(.*)&#8221;/iU", $m[3][$i], $output_icon);				
				extract(shortcode_atts( array(
									'title' => (count($output_title[1]) > 0 ) ?$output_title[1][0] : '',
									'icon'	=> (count($output_icon[1]) > 0 ) ? $output_icon[1][0] : ''
								), $atts
							)
						);
				$titles[] = $title;				
				$contents[] = $m[5][$i];
				$icons[] = '<i class="'.$icon.'"></i>';
			}
		
			$header = '<ul class="nav nav-tabs">';
			foreach ($titles as $i => $title){
				$class = '';
				if ($i == 0) $class .= ' class="active"';
				$header .= '<li '.$class.'><a data-toggle="tab" href="#'.$atts['id'].$i.'">'.$icons[$i].esc_html( $title ).'</a></li>';
			}
			$header .= '</ul>';
			
			$body = '<div class="tab-content">';
			foreach ($contents as $i => $content){
				$class = 'tab-pane';
				if ($i == 0) $class .= ' active';
				$class = 'class="'.$class.'"';
				$body .= '<div '.$class.' id="'.$atts['id'].$i.'">'.do_shortcode($content).'</div>';
			}
			$body .= '</div>';
			
			$html = '';
			if ($atts['position'] == 'bottom') {
				$html .= $body.$header;
			} else $html .= $header.$body;
		}
		
		return $html;
	}
	
	
	/**
	 * Collapse
	 * */

	public function collapses( $atts, $content = NULL ){
		static $key = 0;

		if (is_array($atts) ) {
				
			foreach ($atts as $k => $att){
				$att = trim($att);
				if (empty($att)) unset( $atts[$k] );
			}
		}

		$collapse_id = 'yaCollapse-'.$key ;
		extract(shortcode_atts(array(
				'class' => '',
				'type' => 'collapse',
				'tag'   => 'span'
			), $atts));
		$atts['id'] = $collapse_id;
		$classes = array();
		$classes = preg_split('/[\s,]+/', $class, -1, PREG_SPLIT_NO_EMPTY);
		array_unshift($classes, 'panel-group');
		$classes = array_unique($classes);
		$html = '';
		$html .= '<'.$tag.' id="'.esc_attr( $collapse_id ).'" class="'.implode(' ', $classes).'">';
		$html .= self::get_collapse($atts, $content);
		$html .= '</'.$tag.'>';
		$key++;
		
		return $html;
	}

	function get_collapse( $atts, $content = NULL ) {
		$html = '';
		if (preg_match_all('/\[(\[?)(collapse)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/', $content, $m)) {
			$titles = array();
			$contents = array();
			for ($i=0; $i<count($m[0]); $i++) {			
				preg_match_all("/title=&#8221;(.*)&[#8243;,#8221;]/iU", $m[3][$i], $output_title);
				preg_match_all("/active=&#8221;(.*)&#8221;/iU", $m[3][$i], $output_class);
				extract(shortcode_atts( array(
				                
								'title' => (count($output_title[1]) > 0 ) ?$output_title[1][0] : '',
								'active' => (count($output_class[1]) > 0 ) ?$output_class[1][0] : ''
								
							), $atts
						)
					);
				$content = $m[5][$i];
				$attributes = ($active == 'true') ? 'class="panel-collapse collapse in" data-toggle="collapse" id="'.$atts['id'].$i.'"': 'class="panel-collapse collapse" id="'.$atts['id'].$i.'"';
				$types = ($active == 'true') ? 'class="accordion-toggle ya-accordion" data-toggle="collapse"' : 'class="accordion-toggle ya-accordion collapsed" data-toggle="collapse"';
				$types .= ( $atts['type'] == 'collapse' ) ? ' data-parent="#'.$atts['id'].'"' : '';
				$types .= ' href="#'.$atts['id'].$i.'"';
				$html .= '<span class="panel panel-default">';
				$html .= '<a '.$types.'><span class="panel-default-box"><span></span><span class="panel-heading">'.esc_html( $title ).'</span></span></a>';
				$html .= '<span '.$attributes.'>
							<span class="panel-body">
								'.wpb_js_remove_wpautop($content).'
							</span>
						</span>';
				$html .= '</span>';
			}
		}
		
		return $html;
	}

	
	/**
	 * Column
	 * */
	public function row( $atts, $content = null ){
		extract( shortcode_atts( array(
			'class' => '',
			'tag'   => 'div',
			'type'  => ''
		), $atts) );
		$row_class = 'row';
		
		$classes = array();
		$classes = preg_split('/[\s,]+/', $class, -1, PREG_SPLIT_NO_EMPTY);
		
		array_unshift($classes, $row_class);
		$classes = array_unique($classes);
		$classes = ' class="'. implode(' ', $classes).'"';
		return "<$tag ". $classes . ">" . do_shortcode($content) . "</$tag>";
	}
	
	public function col( $atts, $content = null ){
		extract( shortcode_atts( array(
			'class' 	=> '',
			'tag'   	=> 'div',
			'large'  	=> '12',
			'medium'	=> '12',
			'small'		=> '12',
			'xsmall'	=> '12'
		), $atts) );
		$col_class  = !empty($large)  ? "col-lg-$large"   : 'col-lg-12';
		$col_class .= !empty($medium) ? " col-md-$medium" : ' col-md-12';
		$col_class .= !empty($small)  ? " col-sm-$small"  : ' col-sm-12';
		$col_class .= !empty($xsmall) ? " col-xs-$xsmall" : ' col-xs-12';
		$classes = array();
		$classes = preg_split('/[\s,]+/', $class, -1, PREG_SPLIT_NO_EMPTY);
		array_unshift($classes, $col_class);
		$classes = array_unique($classes);
		$classes = ' class="'. implode(' ', $classes).'"';
		return "<$tag ". $classes . ">" . do_shortcode($content) . "</$tag>";
	}
	
	public function breadcrumb ($atts){
		
		extract(shortcode_atts(array(
				'class' => 'breadcumbs',
				'tag'  => 'div'
			), $atts));
			
		$classes = preg_split('/[\s,]+/', $class, -1, PREG_SPLIT_NO_EMPTY);
		$classes = ' class="' . implode(' ', $classes) . '" ';
		
		$before = '<' . $tag . $classes . '>';
		$after  = '</' . $tag . '>';
		
		$ya_breadcrumb = new YA_Breadcrumbs;
		return $ya_breadcrumb->breadcrumb( $before, $after, false );
	}
}
new YA_Shortcodes();


/**
 * This class handles the Breadcrumbs generation and display
 */
class YA_Breadcrumbs {

	/**
	 * Wrapper function for the breadcrumb so it can be output for the supported themes.
	 */
	function breadcrumb_output() {
		$this->breadcrumb( '<div class="breadcumbs">', '</div>' );
	}

	/**
	 * Get a term's parents.
	 *
	 * @param object $term Term to get the parents for
	 * @return array
	 */
	function get_term_parents( $term ) {
		$tax     = $term->taxonomy;
		$parents = array();
		while ( $term->parent != 0 ) {
			$term      = get_term( $term->parent, $tax );
			$parents[] = $term;
		}
		return array_reverse( $parents );
	}

	/**
	 * Display or return the full breadcrumb path.
	 *
	 * @param string $before  The prefix for the breadcrumb, usually something like "You're here".
	 * @param string $after   The suffix for the breadcrumb.
	 * @param bool   $display When true, echo the breadcrumb, if not, return it as a string.
	 * @return string
	 */
	function breadcrumb( $before = '', $after = '', $display = true ) {
		$options = array('breadcrumbs-home' => 'Home', 'breadcrumbs-blog-remove' => false, 'post_types-post-maintax' => '0');
		
		global $wp_query, $post;

		$on_front  = get_option( 'show_on_front' );
		$blog_page = get_option( 'page_for_posts' );

		$links = array(
			array(
				'url'  => get_home_url(),
				'text' => ( isset( $options['breadcrumbs-home'] ) && $options['breadcrumbs-home'] != '' ) ? $options['breadcrumbs-home'] : __( 'Home', 'yatheme' )
			)
		);

		if ( ( $on_front == "page" && is_front_page() ) || ( $on_front == "posts" && is_home() ) ) {

		} else if ( $on_front == "page" && is_home() ) {
			$links[] = array( 'id' => $blog_page );
		} else if ( is_singular() ) {
			if ( get_post_type_archive_link( $post->post_type ) ) {
				$links[] = array( 'ptarchive' => $post->post_type );
			}
			
			if ( 0 == $post->post_parent ) {
				if ( isset( $options['post_types-post-maintax'] ) && $options['post_types-post-maintax'] != '0' ) {
					$main_tax = $options['post_types-post-maintax'];
					$terms    = wp_get_object_terms( $post->ID, $main_tax );
					
					if ( count( $terms ) > 0 ) {
						// Let's find the deepest term in this array, by looping through and then unsetting every term that is used as a parent by another one in the array.
						$terms_by_id = array();
						foreach ( $terms as $term ) {
							$terms_by_id[$term->term_id] = $term;
						}
						foreach ( $terms as $term ) {
							unset( $terms_by_id[$term->parent] );
						}

						// As we could still have two subcategories, from different parent categories, let's pick the first.
						reset( $terms_by_id );
						$deepest_term = current( $terms_by_id );

						if ( is_taxonomy_hierarchical( $main_tax ) && $deepest_term->parent != 0 ) {
							foreach ( $this->get_term_parents( $deepest_term ) as $parent_term ) {
								$links[] = array( 'term' => $parent_term );
							}
						}
						$links[] = array( 'term' => $deepest_term );
					}

				}
			} else {
				if ( isset( $post->ancestors ) ) {
					if ( is_array( $post->ancestors ) )
						$ancestors = array_values( $post->ancestors );
					else
						$ancestors = array( $post->ancestors );
				} else {
					$ancestors = array( $post->post_parent );
				}

				// Reverse the order so it's oldest to newest
				$ancestors = array_reverse( $ancestors );

				foreach ( $ancestors as $ancestor ) {
					$links[] = array( 'id' => $ancestor );
				}
			}
			$links[] = array( 'id' => $post->ID );
		} else {
			if ( is_post_type_archive() ) {
				$links[] = array( 'ptarchive' => get_post_type() );
			} else if ( is_tax() || is_tag() || is_category() ) {
				$term = $wp_query->get_queried_object();

				if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent != 0 ) {
					foreach ( $this->get_term_parents( $term ) as $parent_term ) {
						$links[] = array( 'term' => $parent_term );
					}
				}

				$links[] = array( 'term' => $term );
			} else if ( is_date() ) {
				$bc = __( 'Archives for', 'yatheme' );
				
				if ( is_day() ) {
					global $wp_locale;
					$links[] = array(
						'url'  => get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ),
						'text' => $wp_locale->get_month( get_query_var( 'monthnum' ) ) . ' ' . get_query_var( 'year' )
					);
					$links[] = array( 'text' => $bc . " " . get_the_date() );
				} else if ( is_month() ) {
					$links[] = array( 'text' => $bc . " " . single_month_title( ' ', false ) );
				} else if ( is_year() ) {
					$links[] = array( 'text' => $bc . " " . get_query_var( 'year' ) );
				}
			} elseif ( is_author() ) {
				$bc = __( 'Archives for', 'yatheme' );
				$user    = $wp_query->get_queried_object();
				$links[] = array( 'text' => $bc . " " . esc_html( $user->display_name ) );
			} elseif ( is_search() ) {
				$bc = __( 'You searched for', 'yatheme' );
				$links[] = array( 'text' => $bc . ' "' . esc_html( get_search_query() ) . '"' );
			} elseif ( is_404() ) {
				$crumb404 = __( 'Error 404: Page not found', 'yatheme' );
				$links[] = array( 'text' => $crumb404 );
			}
		}
		
		$output = $this->create_breadcrumbs_string( $links );

		if ( $display ) {
			echo $before . $output . $after;
			return true;
		} else {
			return $before . $output . $after;
		}
	}

	/**
	 * Take the links array and return a full breadcrumb string.
	 *
	 * Each element of the links array can either have one of these keys:
	 *       "id"            for post types;
	 *    "ptarchive"  for a post type archive;
	 *    "term"         for a taxonomy term.
	 * If either of these 3 are set, the url and text are retrieved. If not, url and text have to be set.
	 *
	 * @link http://support.google.com/webmasters/bin/answer.py?hl=en&answer=185417 Google documentation on RDFA
	 *
	 * @param array  $links   The links that should be contained in the breadcrumb.
	 * @param string $wrapper The wrapping element for the entire breadcrumb path.
	 * @param string $element The wrapping element for each individual link.
	 * @return string
	 */
	function create_breadcrumbs_string( $links, $wrapper = 'ul', $element = 'li' ) {
		global $paged;
		
		$output = '';

		foreach ( $links as $i => $link ) {

			if ( isset( $link['id'] ) ) {
				$link['url']  = get_permalink( $link['id'] );
				$link['text'] = strip_tags( get_the_title( $link['id'] ) );
			}

			if ( isset( $link['term'] ) ) {
				$link['url']  = get_term_link( $link['term'] );
				$link['text'] = $link['term']->name;
			}

			if ( isset( $link['ptarchive'] ) ) {
				$post_type_obj = get_post_type_object( $link['ptarchive'] );
				$archive_title = $post_type_obj->labels->menu_name;
				$link['url']  = get_post_type_archive_link( $link['ptarchive'] );
				$link['text'] = $archive_title;
			}
			
			$link_class = '';
			if ( isset( $link['url'] ) && ( $i < ( count( $links ) - 1 ) || $paged ) ) {
				$link_output = '<a href="' . esc_url( $link['url'] ) . '" >' . esc_html( $link['text'] ) . '</a><span class="divider">/</span>';
			} else {
				$link_class = ' class="active" ';
				$link_output = '<span>' . esc_html( $link['text'] ) . '</span>';
			}
			
			$element = esc_attr(  $element );
			$element_output = '<' . $element . $link_class . '>' . $link_output . '</' . $element . '>';
			
			$output .=  $element_output;
			
			$class = ' class="breadcrumb" ';
		}

		return '<' . $wrapper . $class . '>' . $output . '</' . $wrapper . '>';
	}

}

global $yabreadcrumb;
$yabreadcrumb = new YA_Breadcrumbs();

if ( !function_exists( 'ya_breadcrumb' ) ) {
	/**
	 * Template tag for breadcrumbs.
	 *
	 * @param string $before  What to show before the breadcrumb.
	 * @param string $after   What to show after the breadcrumb.
	 * @param bool   $display Whether to display the breadcrumb (true) or return it (false).
	 * @return string
	 */
	function ya_breadcrumb( $before = '', $after = '', $display = true ) {
		global $yabreadcrumb;
		return $yabreadcrumb->breadcrumb( $before, $after, $display );
	}
}

/*
 * Pricing Table
 * @since v1.0
 *
 */
 
/*main*/
if( !function_exists('pricing_table_shortcode') ) {
	function pricing_table_shortcode( $atts, $content = null  ) {
		extract( shortcode_atts( array(
			'style' => 'style1',
		), $atts ) );
		
	   return '<div class="pricing-table clearfix '.$style.'">' . do_shortcode($content) . '</div></div>';
	}
	add_shortcode( 'pricing_table', 'pricing_table_shortcode' );
}

/*section*/
if( !function_exists('pricing_shortcode') ) {
	function pricing_shortcode( $atts, $content = null, $style_table) {
		
		extract( shortcode_atts( array(
			'style' =>'style1',
			'size' => 'one-five',
			'featured' => 'no',
			'description'=>'',
			'plan' => '',
			'cost' => '$20',
			'currency'=>'',
			'per' => 'month',
			'button_url' => '',
			'button_text' => 'Purchase',
			'button_target' => 'self',
			'button_rel' => 'nofollow'
		), $atts ) );
		
		//set variables
		$featured_pricing = ( $featured == 'yes' ) ? 'most-popular' : NULL;
		
		//start content1  
		$pricing_content1 ='';
		$pricing_content1 .= '<div class="pricing pricing-'. $size .' '. $featured_pricing . '">';
				$pricing_content1 .= '<div class="header">'. esc_html( $plan ). '</div>';
				$pricing_content1 .= '<div class="price">'. esc_html( $cost ) .'/'. esc_html( $per ) .'</div>';
			$pricing_content1 .= '<div class="pricing-content">';
				$pricing_content1 .= ''. $content. '';
			$pricing_content1 .= '</div>';
			if( $button_url ) {
				$pricing_content1 .= '<a href="'. esc_url( $button_url ) .'" class="signup" target="_'. esc_attr( $button_target ).'" rel="'. esc_attr( $button_rel ) .'" '.'>'. esc_html( $button_text ) .'</a>';
			}
		$pricing_content1 .= '</div>';
		//start content2  
		$pricing_content2 ='';
		$pricing_content2 .= '<div class="pricing pricing-'. $size .' '. $featured_pricing . '">';
			$pricing_content2 .= '<div class="header"><h3>'. esc_html( $plan ). '</h3><span>'.esc_html( $description ).'</span></div>';
				
			$pricing_content2 .= '<div class="pricing-content">';
				$pricing_content2 .= ''. $content. '';
			$pricing_content2 .= '</div>';
			$pricing_content2 .= '<div class="price"><span class="span-1"><p>'.$currency.'</p>'. esc_html( $cost ) .'</span><span class="span-2">'. esc_html( $per ) .'</span></div>';
			if( $button_url ) {
				$pricing_content2 .= '<div class="plan"><a href="'. esc_url( $button_url ) .'" class="signup" target="_'. esc_attr( $button_target ) .'" rel="'. esc_attr( $button_rel ) .'" '.'>'. esc_html( $button_text ) .'</a></div>';
			}
		$pricing_content2 .= '</div>';
		//start basic
		$pricing_content4 ='';
		$pricing_content4 .= '<div class="pricing pricing-'. $size .' '. $featured_pricing . '">';
			$pricing_content4 .= '<div class="price"><span class="span-1">'. esc_html( $cost ) .'<p>'.$currency.'</p></span><span class="span-2">'. esc_html( $per ) .'</span></div>';
			if( $button_url ) {
				$pricing_content4 .= '<div class="plan"><a href="'. esc_url( $button_url ) .'" class="signup" target="_'. esc_attr( $button_target ) .'" rel="'. esc_attr( $button_rel ) .'" '.'>'. esc_html( $button_text ) .'</a></div>';
			}
		$pricing_content4 .= '</div>';
		if($style == 'style1'||$style == 'style3'){
			return $pricing_content1;
		}
		if($style == 'style2') {
			return $pricing_content2;
		}
		if($style == 'basic'){
			return $pricing_content4;
		}
	}
	
	add_shortcode( 'pricing', 'pricing_shortcode' );
}
/*
 * Tooltip
 * @since v1.0
 *
 */
 function tooltip($atts, $content = null) {
            extract(shortcode_atts(array(
	 'info' =>'',
	 'title'=>'',
	 'style'=>'',
	 'position'=>''
	 ),$atts));
	 if($title !=''){
		$title = '<strong>'.$title.'</strong>';
	 }
		  $html ='<a class="tooltips " href="#">';
		  $html .='<span class="'.$position.' tooltip-'.$style.'">'.$title.$info.'<b></b></span>';
		  $html .=do_shortcode($content);
		  $html .='</a>';
		 return $html;
	
}
add_shortcode('ya_tooltip', 'tooltip');


/*
 * Modal
 * @since v1.0
 *
 */
 
function modal($attr, $content = null) {
            ob_start();
			$tag_id = 'myModal_'.rand().time();
			?>
			<a href="#<?php echo esc_attr( $tag_id ); ?>" role="button" class="btn btn-default" data-toggle="modal"><?php echo trim($attr['label']) ?></a>
 
			<!-- Modal -->
			<div id="<?php echo esc_attr( $tag_id ); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
							<h3 id="myModalLabel"><?php echo esc_html( trim($attr['header']) ) ?></h3>
						</div>
						<div class="modal-body">
							<?php echo $content; ?>
						</div>
						<div class="modal-footer">
							<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo esc_html( trim($attr['close']) ) ?></button>
							<button class="btn btn-primary"><?php echo esc_html( trim($attr['save']) ) ?></button>
						</div>
					</div>
				</div>
			</div>
            
            <?php
            $output_string = ob_get_contents();
            ob_end_clean();
            return $output_string;
        }
add_shortcode('ya_modal', 'modal');

/*
 * Recent posts shortcode
 *
 */
 

function recent_posts( $atts ) {
	extract( shortcode_atts( array( 'limit' => 6, 'title' =>'Recent posts', 'num_column'=> 3, 'id' => 3), $atts ) );
	global $post;
	$numbers = ceil( 12 / $num_column);
	$q = new WP_Query( 'posts_per_page=' . $limit );
	$list  = '<div class="widget-title-sidebar"><h3>'.esc_html( $title ).'</h3>';
	$list .= '<hr></div>';
	$list .= '<div class="sw-latestnews latest-blog">';
	$list .= '<div id="myCarousel-'.$id.'" class="carousel slide ">';
	$list .= '<ol class="carousel-indicators">';
		for ($i=0; $i * $num_column < $limit; $i++){
		$list .= '<li class="';
		if ($i==0) {
			$list .= 'active';
		}
		$list .= '" data-slide-to="'.esc_attr( $i ).'" data-target="#myCarousel-'.$id.'"><span class="icon-circle"></span></li>';
		}
	$list .= '</ol>';
	$list .= '<div class="carousel-inner">';
	$j = 0;
	while ( $q->have_posts() ): $q->the_post();
			
			if($j % $num_column == 0){
				$list .= '<div class="item ';
				if($j == 0)
				{$list .='active ';} 
				$list .='row">';
			}

		$list .= '<div class="sw-widget-item col-lg-'.$numbers.' col-md-'.$numbers.' col-sm-'.$numbers.'"><div class="sw-item-inner">'; 
				$list .= '<div class="sw-thumb">';
				if (has_post_thumbnail()) {
					$list .='<a href="'.get_permalink().'">'.get_the_post_thumbnail($post->ID,'thumbnail').'</a>';
				}else{
					$list .='<a href="'.get_permalink().'"><img src="'.get_template_directory_uri().'/assets/img/medium.png" alt="No Thumb"/></a>';
				}
			$list .='</div>';
			$list .= '<div class="sw-caption"><h4 class="sw-title">';
			$list .= '<a href="'.get_permalink().'">'.get_the_title().'</a></h4>';
			$list .= '<div class="sw-meta"><div class="sw-date">Published: '. get_the_date().'</div>';
			$list .='<div class="sw-comment">';
			$count_comment = get_comments_number($post->comment_count); 
			if ($count_comment > 1) {
				$list .='<i class="icon-comment"></i> ' . esc_html( $count_comment ) . ' comments';
			}else 
			$list .='<i class="icon-comment"></i> ' . esc_attr( $count_comment ) . ' comment';
			$list .='</div></div>';
			$list .='<div class="sw-content">'.get_the_excerpt().'</div></div></div></div>';
			if(($j+1)% $num_column == 0 || $j+1 == $limit){ 
			$list .='</div>';
			}
			$j ++;
	endwhile;
	wp_reset_postdata();

	return $list . '</div></div></div>';
}

add_shortcode( 'ya-recent-posts', 'recent_posts' );
/*
 * Videos shortcode
 *
 */
 
// register the shortcode to wrap html around the content
function yt_vid_sc($atts, $content=null) {
    extract(
        shortcode_atts(array(
            'site' => '',
            'id' => '',
            'w' => '',
            'h' => ''
        ), $atts)
    );
    if ( $site == "youtube" ) { $src = 'http://www.youtube-nocookie.com/embed/'.$id; }
    else if ( $site == "vimeo" ) { $src = 'http://player.vimeo.com/video/'.$id; }
    else if ( $site == "dailymotion" ) { $src = 'http://www.dailymotion.com/embed/video/'.$id; }
    else if ( $site == "yahoo" ) { $src = 'http://d.yimg.com/nl/vyc/site/player.html#vid='.$id; }
    else if ( $site == "bliptv" ) { $src = 'http://a.blip.tv/scripts/shoggplayer.html#file=http://blip.tv/rss/flash/'.$id; }
    else if ( $site == "veoh" ) { $src = 'http://www.veoh.com/static/swf/veoh/SPL.swf?videoAutoPlay=0&permalinkId='.$id; }
    else if ( $site == "viddler" ) { $src = 'http://www.viddler.com/simple/'.$id; }
    if ( $id != '' ) {
        return '<iframe width="'.$w.'" height="'.$h.'" src="'.$src.'" class="vid iframe-'.$site.'"></iframe>';
    }
}
add_shortcode('videos','yt_vid_sc');
/*
 * Audios shortcode
 *
 */
// register the shortcode to wrap html around the content
function yt_audio_shortcode( $atts ) {
    extract( shortcode_atts( array (
        'identifier' => ''
    ), $atts ) );
    return '<div class="yt-audio-container"><iframe width="100%" height="166" frameborder="no" scrolling="no" src="https://w.soundcloud.com/player/?url=' . $identifier . '"></iframe></div>';
}
add_shortcode ('soundcloud-audio', 'yt_audio_shortcode' );
/*
 * Post slide 
 *
 */
function yt_post_slide( $atts ) {
	extract( shortcode_atts( array( 'title'=>'','style_title'=>'title1','limit' => 6,'categories'=>'','length'=> 40,'type'=>'','interval'=>'5000','el_class'=>''), $atts ) );
    $list = get_posts( array('cat' =>$categories,'posts_per_page' =>  $limit) );
	$html = '<div id="yt_post_slide" class="carousel  yt_post_slide_'.$type.' slide content '.$el_class.'" data-ride="carousel" data-interval="'.$interval.'">';
	if($title != ''){
	$html.='<div class="block-title '.$style_title.'">';
if($style_title == 'title3'){
	$wordChunks = explode(" ", $title);
	$firstchunk = $wordChunks[0];
	$secondchunk = $wordChunks[1];
$html.='<h2> <span>'.$firstchunk.'</span> <span class="text-color"> '.$secondchunk.' </span></h2>';
}else{
$html.='<h2>
				<span>'.$title.'</span>
	</h2>' ;
}	
}
	$html.=	'<div class="customNavigation nav-left-product">
				<a title="Previous" class="btn-bs prev-bs icon-angle-left"  href=".yt_post_slide_'.$type.'" role="button" data-slide="prev"></a>
				<a title="Next" class="btn-bs next-bs icon-angle-right" href=".yt_post_slide_'.$type.'" role="button" data-slide="next"></a>
			</div>
		</div>';
    $html .= '<div class="carousel-inner">';
	foreach( $list as $i => $item ){
	  $html .= '<div class="item ';
				if($i == 0)
				{$html .='active ';} 
				$html .='">';
	  $html .='<a href="'.post_permalink($item->ID).'" title="'.$item->post_title.'">'.get_the_post_thumbnail($item->ID).'</a>';
	  $html .='  <div class="carousel-caption-'.$type.' carousel-caption">
      	         <div class="carousel-caption-inner">
				 <a href="'.post_permalink($item->ID).'">'.$item->post_title.'</a>';	
	  $html .='<div class="item-description">'.wp_trim_words($item->post_content,$length).'</div>';
	  $html .='</div></div></div>';  
	}
	  $html .='</div>';
	  $html .='<div class="carousel-cl-'.$type.' carousel-cl" >';
	  $html .=	'<a class="left carousel-control" href=".yt_post_slide_'.$type.'" role="button" data-slide="prev"></a>';
	  $html .='<a class="right carousel-control" href=".yt_post_slide_'.$type.'" role="button" data-slide="next"></a>';
	  $html .='</div>';
	  $html .='</div>';
	return $html;
}
add_shortcode ('postslide', 'yt_post_slide' );
/*
 * Lightbox image
 *
 */
 function yt_lightbox_shortcode($atts){
	  extract( shortcode_atts( array (
        'id' => '',
		'style'=>'',
		'size'=>'thumbnail',
		'class'=>'',
		'title'=>''
    ), $atts ) );
	add_action('wp_footer', 'add_script_lightbox', 50);
	return '<div class="lightbox '.$class.' lightbox-'.$style.'" ><a id="single_image" href="' . wp_get_attachment_url($id) . '">'.wp_get_attachment_image($id,$size).'</a><div class="caption"><h4>'.$title.'</h4></div></div>';
 }
 add_shortcode ('lightbox', 'yt_lightbox_shortcode' );
 function add_script_lightbox(){
	$script = '';
	$script .= '<script type="text/javascript">
	 jQuery(document).ready(function($) {
		  "use strict";
		 $("a#single_image").fancybox();
		 });
	</script>';
	echo $script;
 }
 /*
 * Heading tag
 *
 */
 function yt_heading_shortcode($atts,$content = null){
	 extract( shortcode_atts( array (
        'heading' => '',
		'type'=>'',
		'color'=>'',
		'icon'=>'',
        'class'=>'', 		
		'bg'=>''
    ), $atts ) );
	if( $icon != ''||$color !=''||$bg !=''||$class !=''){
			return '<span class="'.$class.'" style="background:'.$bg.';color:'.$color.'"><i class="'.$icon.'"></i>'.do_shortcode($content);
		}
	if($heading !=''){
	  return '<'.$heading.' style="font-weight:'.$type.'">'.do_shortcode($content).'</'.$heading.'>';
	}
 }
 add_shortcode('headings','yt_heading_shortcode');
  /*
 * Testimonial
 *
 */
 function yt_testimonial($atts,$content = null) {
	 extract(shortcode_atts(array(
	 'iconleft' =>'',
	 'iconright' =>'',
	 'imgsrc'=>'',
	 'auname'=>'',
	 'auinfo'=>'',
	 'title'=>'',
	 'content'=>'',
	 'class'=>'',
	 'style'=>''
	 ),$atts));
	 if($style == 'style1' || $style == 'style2'){
	 if($iconleft !='' ||$iconright !=''){
		 $iconleft = '<i class="'.$iconleft.'"></i>';
		 $iconright ='<i class="'.$iconright.'"></i>';
	 }
	 $html ='<div class="testimonial_'.$style.' '.$class.'">';
	 $html .='<div class="testimonial_content">';
	 $html .= $iconleft.$content.$iconright;
	 $html .='</div>';
	 $html .='<div class="testimonial_meta">';
	 	 if($imgsrc !=''){
	 $html .= '<img src ="'.$imgsrc.'">';
		   }
	 $html .='<div class="testimonial_info"><ul><li>'.$auname.'</li>';
	 if($auinfo !=''){
	 $html .='<li>'.$auinfo.'</li>';
	 }
	 $html .='</ul></div>';
	 $html .='</div></div>';
	 return $html;
	 }
	 /*** testimonial width background ***/
	 if($style == 'bg'){
		 if($iconleft !='' ||$iconright !=''){
		 $iconleft = '<i class="'.$iconleft.'"></i>';
		 $iconright ='<i class="'.$iconright.'"></i>';
	 }
	 $html ='<div class="testimonial_'.$style.' '.$class.'">';
	 if($imgsrc !=''){
	 $html .= '<img src ="'.$imgsrc.'">';
		   }
	 $html .='<div class="testimonial_content">';
	 $html .= $iconleft.$content.$iconright;
	 $html .='</div>';
	 $html .='<div class="testimonial_meta">';
	 $html .='<div class="testimonial_info"><ul><li>'.$auname.'</li>';
	 if($auinfo !=''){
	 $html .='<li>'.$auinfo.'</li>';
	 }
	 $html .='</ul></div>';
	 $html .='</div></div>';
	 return $html; 
	 }
	 /*** Testimonial width border ***/
	 if($style == 'border'){
		 if($iconleft !='' ||$iconright !=''){
		 $iconleft = '<i class="'.$iconleft.'"></i>';
		 $iconright ='<i class="'.$iconright.'"></i>';
	 }
	 $html ='<div class="testimonial_'.$style.' '.$class.'">';
	 $html .='<div class="testimonial_content">';
	 $html .= $iconleft.$content.$iconright;
	 $html .='</div>';
	 $html .='<div class="testimonial_meta">';
	 	 if($imgsrc !=''){
	 $html .= '<img src ="'.$imgsrc.'">';
		   }
	 $html .='<div class="testimonial_info"><ul><li>'.$auname.'</li>';
	 if($auinfo !=''){
	 $html .='<li>'.$auinfo.'</li>';
	 }
	 $html .='</ul></div>';
	 $html .='</div></div>';
	 return $html;
		 
	 }
 }
 add_shortcode('testimonial','yt_testimonial');
 /*
 * Testimonial Slide
 *
 */
 function yt_testimonial_slide($atts){
	 extract(shortcode_atts(array(
	 'post_type' => 'testimonial',
	 'type' =>'',
	 'el_class'=>'',
	 'title'=>'Testimonial',
	 'style_title'=>'title1',
		'orderby' => '',
		'length'=>'',
		'order' => '',
		'post_status' => 'publish',
		'numberposts' => 5
	 ),$atts));
$pf_id = 'testimonial-'.rand().time();
$i='';
$j='';
$k='';
$query_args =array( 'posts_per_page'=> $numberposts,'post_type' => 'testimonial','orderby' => $orderby,'order' => $order); 
$list = new WP_Query($query_args);
//var_dump($list);
//////////////////////    testimonial indicators /////////////////
if($type=='indicators'){
$output='<div id="'.$pf_id.'" class="testimonial-slider carousel slide '.$el_class.'">';
if($title !=''){
$output.='<div class="block-title '.$style_title.'">
			<h2>
				<span>'.$title.'</span>
			</h2>
			</div>';
}
$output.='<div class="carousel-inner">';
				while($list->have_posts()): $list->the_post();
			
				global $post;
				$au_name = get_post_meta( $post->ID, 'au_name', true );
				$au_url  = get_post_meta( $post->ID, 'au_url', true );
				$au_info = get_post_meta( $post->ID, 'au_info', true );
				if( $i % 1 == 0 ){ 
				$active = ($i== 0)? 'active' :'';
	$output.='<div class="item '.$active.'">';
	$output.='<div class="row">';
			   } 
	$output.='<div class="item-inner col-lg-12">';
						if( has_post_thumbnail() ){
	$output.='<div class="client-comment">';
					$text = get_the_content($post->ID);
										$content = wp_trim_words($text, $length);
								$output.= esc_html($content);
	$output.='</div>';
	$output.='<div class="client-say-info">';
	$output.='<div class="image-client">';
	$output.='<a href="#" title="'.esc_attr( $post->post_title ).'">'.get_the_post_thumbnail($post->ID,'thumbnail').'</a>';
	$output.='</div>';
	$output.='<div class="name-client">';
	$output.='<h2><a href="#" title="'.esc_attr( $post->post_title ).'">'.esc_html($au_name).'</a></h2>
						<p>'.esc_html($au_info).'</p>
					</div>
				</div>';
						}
	$output.='</div>';
			if( ( $i+1 )%1==0 || ( $i+1 ) == $numberposts ){

$output.='	</div></div>';
} 
			 $i++; endwhile; wp_reset_postdata(); 
		$output.='</div>';
$output.='<ul class="carousel-indicators">';
			 while ( $list->have_posts() ) : $list->the_post();
				 if( $j % 1 == 0 ) {  $k++;
				 $active = ($j== 0)? 'active' :'';
$output.='<li class="'.$active.'" data-slide-to="'.($k-1).'" data-target="#'.$pf_id.'"> ';
				}  if( ( $j+1 ) % 1 == 0 || ( $j+1 ) == $numberposts ){
$output.='</li>';
			
				}
					
				$j++; 
			 endwhile; 
		wp_reset_postdata(); 
$output.='</ul>';
$output.='</div>';
	return $output;
} 
///////////////////////////   testimonial   slide //////////////////////
if($type=='slide1'){
	$output ='<div class="widget-testimonial">
	<div class="widget-inner">
		<div class="customersay">
			<h3 class="custom-title">'.$title.'</h3>
		<div id="'.$pf_id.'" class="testimonial-slider carousel slide">
			<div class="carousel-inner">';				
					while($list->have_posts()): $list->the_post();
					global $post;
					$au_name = get_post_meta( $post->ID, 'au_name', true );
					$au_url  = get_post_meta( $post->ID, 'au_url', true );
					$au_info = get_post_meta( $post->ID, 'au_info', true );
					if( $i % 1 == 0 ){ 
					$active = ($i== 0)? 'active' :'';
	$output.='<div class="item '.$active.'">
					<div class="">';
				     } 
    $output.='<div class="item-inner col-lg-12 col-md-12">';
							 if( has_post_thumbnail() ){ 
	$output.='<div class="item-content">
									<div class="item-desc">';
											$text = get_the_content($post->ID);
											$content = wp_trim_words($text, $length);
											$output.= esc_html($content);
	$output.='</div></div>';
								
	$output.='<div class="item-info">
										<h4><span class="author">- '.esc_html($au_name).'</span> - <span class="info">'.esc_html($au_info).'</span></h4>
								</div>';
							  }
	$output.='</div>';
				if( ( $i+1 )%1==0 || ( $i+1 ) == $numberposts ){ 
	$output.='</div></div>';  
				} 
				$i++; endwhile; wp_reset_postdata(); 
	$output.='</div>
			<!-- Controls -->
				<div class="carousel-cl">
					<a class="left carousel-control" href="#'.$pf_id.'" role="button" data-slide="prev"></a>
					<a class="right carousel-control" href="#'.$pf_id.'" role="button" data-slide="next"></a>
				</div>
		</div>
		</div>
	</div>
</div>';
return $output;
} 
if($type=='slide2'){
      $output='<div id="'.$pf_id.'" class="client-wrapper-b carousel slide '.$el_class.'">';
	
	$output.='<div class="block-title-bottom">
				<h2>'.$title.'</h2>
				<div class="nav-custom">
					<a title="Previous" class="prev-test icon-angle-left"><span>Previous</span></a>
					<a title="Next" class="next-test icon-angle-right"><span>Next</span></a>
				</div>
			</div>';
	$output.='<div class="carousel-inner">';
				while($list->have_posts()): $list->the_post();
			
				global $post;
				$au_name = get_post_meta( $post->ID, 'au_name', true );
				$au_url  = get_post_meta( $post->ID, 'au_url', true );
				if( $i % 1 == 0 ){ 
				$active = ($i== 0)? 'active' :'';
	       $output.='<div class="item '.$active.'">';
	          
	$output.='<div class="row">';
			   }   
	$output.='<div class="item-inner col-lg-12">';
	if( has_post_thumbnail() ){
	$output.='<div class="image-client">';
	$output.='<a href="#" title="'.esc_attr( $post->post_title ).'">'.get_the_post_thumbnail($post->ID,'thumbnail').'</a>';
	$output.='</div>';
	
	$output.='<div class="client-say-info">';
	$output.='<div class="client-comment">';
					$text = get_the_content($post->ID);
										$content = wp_trim_words($text, $length);
								$output.= esc_html($content);
	$output.='</div>';
	$output.='<div class="name-client">';
	$output.='<h2><a href="'.$au_url.'" title="'.esc_attr( $post->post_title ).'">'.esc_html($au_name).'</a></h2>
					</div>
				</div>';
						}
	$output.='</div>';
			if( ( $i+1 )%1==0 || ( $i+1 ) == $numberposts ){

$output.='	</div></div>';
} 
			 $i++; endwhile; wp_reset_postdata(); 
		$output.='</div></div>';
		return $output;
	
}
}
add_shortcode('testimonial_slide','yt_testimonial_slide');
  /*
 * Personnel
 *
 */
 function yt_personnel_shortcode ($atts){
	 extract(shortcode_atts(array(
	 'fb' =>'',
	 'linkiconfb'=>'',
	 'linkicong'=>'',
	 'linkicontw'=>'',
	 'g' =>'',
	 'tw'=>'',
	 'auname'=>'',
	 'auinfo'=>'',
	 'auoffice'=>'',
	 'class'=>'',
	 'imgsrc'=>'',
	 'style'=>''
	 ),$atts));
	$html= '<div class="personnel_'.$style.' '.$class.'">';
    $html .='<div class="personnel_img">';
    $html .='<a href="#"><img src="'.$imgsrc.'"></a>';
	if($style == 'style1'){
    $html .='<ul>';
	$html .='<li><a href="'.$fb.'" ><img src="'.$linkiconfb.'"></a></li>';
	$html .='<li><a href="'.$g.'"><img src="'.$linkicong.'"></a></li>';
	$html .='<li><a href="'.$tw.'"><img src="'.$linkicontw.'"></a></li>';
	$html .= '</ul>';
	}
    $html .='</div>';
    $html .='<div class="personnel_meta">';
    $html .='<ul>';
	$html .= '<li>'.$auname.'</li>';
    $html .= '<li>'.$auoffice.'</li>';
	$html .= '<li>'.$auinfo.'</li>';
	$html .= '</ul>';
	if($style == 'style2'){
     $html .= '<span class="social"><ul><li><a href="'.$fb.'" ><img src="'.$linkiconfb.'"></a></li>';
	 $html .='<li><a href="'.$g.'"><img src="'.$linkicong.'"></a></li>';
	 $html .='<li><a href="'.$tw.'"><img src="'.$linkicontw.'"></a></li>';
	 $html .='</ul></span>';
	}
    $html .='</div>';
    $html .='</div>';
	 return $html;
 } 
 add_shortcode('personnel','yt_personnel_shortcode');
  /*
 * Divider
 *
 */
 function yt_divider_shortcode ($atts){
	 extract(shortcode_atts(array(
	 'position' =>'top',
	 'title'=>'',
	 'style'=>'',
	 'type'=>'',
	 'width' =>'auto',
	 'widthbd'=>'1px',
	 'color' =>'#d1d1d1'
	 ),$atts));
	 if($position !=''&&$type !='LR'){
		 return '<h4 style="text-align: center;">'.$title.'</h4><hr style ="border-'.$position.':'.$widthbd.' '.$style.' '.$color.';width:'.$width.';margin-top:10px">';
	 }
	 if($type == 'LR'){
		 return'<div class="rpl-title-wrapper"><h4>'.$title.'</h4></div><hr style ="border-'.$position.':'.$widthbd.' '.$style.' '.$color.';width:'.$width.';margin-top:-20px">';
	 }
	
 }
 add_shortcode('divider','yt_divider_shortcode');
 /*
 * Tables
 *
 */
 function yt_simple_table( $atts ) {
    extract( shortcode_atts( array(
        'cols' => 'none',
        'data' => 'none',
		'class'=>'',
		'style'=>''
    ), $atts ) );
    $cols = explode(',',$cols);
    $data = explode(',',$data);
    $total = count($cols);
    $output = '<table class="table-'.$style.' '.$class.'"><tr class="th">';
    foreach($cols as $col):
        $output .= '<td>'.$col.'</td>';
    endforeach;
    $output .= '</tr><tr>';
    $counter = 1;
    foreach($data as $datum):
        $output .= '<td>'.$datum.'</td>';
        if($counter%$total==0):
            $output .= '</tr>';
        endif;
        $counter++;
    endforeach;
        $output .= '</table>';
    return $output;
}
add_shortcode( 'tables', 'yt_simple_table' );
/*
 * Block quotes
 *
 */
 function yt_quote_shortcode( $atts,$content = null ) {
    extract( shortcode_atts( array(
		'style'=>''
    ), $atts ) );
	return '<div class="quote-'.$style.'">'.do_shortcode($content).'</div>';
 }
 add_shortcode ('quotes','yt_quote_shortcode');
 /*
 * Counter box
 *
 */
 function yt_counter_box($atts){
extract( shortcode_atts( array(
		'style'=>'',
		'icon'=>'',
	    'number'=>'',
		'type'=>''
      ), $atts ) );
	  add_action('wp_footer', 'add_script_counterbox', 50);
	  wp_enqueue_script('ya_waypoints_api', 'http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js', array('jquery'), null, true);
	if($icon !=''){
		$icon= '<i class="'.$icon.'"></i>';
	}
	return'<div class="counter-'.$style.'"><ul><li class="counterbox-number">'.$icon.''.$number.'</li><li class="type">'.$type.'</li></ul></div>';
}
add_shortcode('counters','yt_counter_box');
 function add_script_counterbox(){
	$script = '';
	$script .='<script type="text/javascript">';
	$script .= 'jQuery(document).ready(function( $ ) {
        $(".counterbox-number").counterUp({
            delay: 10,
            time: 1000
        });
    });';
	$script .='</script>';
	echo $script;
 }
 /*
 * Social
 *
 */
 function yt_social_shortcode($atts){
	 extract(shortcode_atts(array(
	 'style'=>'',
	 'background'=>'',
	 'icon'=>'',
	 'link'=>'',
	 'title'=>''
	 ),$atts));
	 $bg='';
	 if($background !=''){
		 $bg = 'style="background:'.$background.'"';
	 }
	 return '<div id="socials" class="socials-'.$style.'" '.$bg.'><a href="'.$link.'" title="'.$title.'"><i class="'.$icon.'"></i></a></div>';
 }
 add_shortcode('socials','yt_social_shortcode');
 /*
 * Best Sale product
 *
 */
 function yt_bestsale_shortcode($atts){
	 extract(shortcode_atts(array(
	 'number' => 5,
	 'title'=>'Best Sale',
	 'style_title'=>'title1',
	 'el_class'=>'',
	 'template'=>'',
    		'post_status' 	 => 'publish',
    		'post_type' 	 => 'product',
    		'meta_key' 		 => 'total_sales',
    		'orderby' 		 => 'meta_value_num',
    		'no_found_rows'  => 1
	 ),$atts));
	 global $woocommerce;
	 $i='';
	 $pf_id = 'bestsale-'.rand().time();
	 $query_args =array( 'posts_per_page'=> $number,'post_type' => 'product','meta_key' => 'total_sales','orderby' => 'meta_value_num','no_found_rows' => 1); 
	 $query_args['meta_query'] = $woocommerce->query->get_meta_query();

    		$query_args['meta_query'][] = array(
			    'key'     => '_price',
			    'value'   => 0,
			    'compare' => '>',
			    'type'    => 'DECIMAL',
			);
    

		$r = new WP_Query($query_args);
		if ( $r->have_posts() ) {
if($template== 'default'){
	$output ='<div class="block-title-bottom">
			<h2>'.$title.'</h2>
		</div>';
	$output .='<div id="'.$pf_id.'" class="sw-best-seller-product vc_element">';
		while ( $r -> have_posts() ) : $r -> the_post();
		global $product, $post, $wpdb, $average;
		$count = $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(meta_value) FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
		",$post->ID));
		$rating = $wpdb->get_var($wpdb->prepare("
			SELECT SUM(meta_value) FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
		",$post->ID));
	$output.='<div class="bs-item cf">
			<div class="bs-item-inner">';
	$output.='<div class="item-img">';
	$output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">';
					 if( has_post_thumbnail() ){  
	$output.= (get_the_post_thumbnail( $r->post->ID, 'shop_thumbnail' )) ? get_the_post_thumbnail( $r->post->ID, 'shop_thumbnail' ):'<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>' ;
					}else{ 
	$output.= '<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>' ;
					} 
	$output.='</a>';
	$output.='</div>';
	$output.='<div class="item-content">';
	
					if( $count > 0 ){
						$average = number_format($rating / $count, 1);
				
	$output.='<div class="star"><span style="width:'.($average*14).'px'.'"></span></div>';
					
			 } else { 
				
	$output.='<div class="star"></div>';
					
				}
	$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.esc_html( $post->post_title ).'</a></h4>';
    $output.= '<p>'.$product->get_price_html().'</p>';			 
    $output.='</div></div></div>';
		endwhile;
	wp_reset_postdata();
	$output.='</div>';
	return $output;
}elseif($template == 'slide'){
	$output ='<div id="'.$pf_id.'" class="sw-best-seller-product vc_element '.$el_class.'" data-interval="0">';
	if($title != ''){
	$output.='<div class="block-title '.$style_title.'">';

$output.='<h2>
				<span>'.$title.'</span>
			</h2>';
    
}
	$output.='<div class="customNavigation nav-left-product">
				<a title="Previous" class="btn-bs prev-bs icon-angle-left"  href="#'.$pf_id.'" role="button" data-slide="prev"></a>
				<a title="Next" class="btn-bs next-bs icon-angle-right" href="#'.$pf_id.'" role="button" data-slide="next"></a>
			</div>
		</div>';
    $output.='<div class="carousel-inner">';
		while ( $r -> have_posts() ) : $r -> the_post();
		global $product, $post, $wpdb, $average;
		$count = $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(meta_value) FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
		",$post->ID));
		$rating = $wpdb->get_var($wpdb->prepare("
			SELECT SUM(meta_value) FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
		",$post->ID));
	if( $i % 4 == 0 ){
    $active = ( $i == 0 ) ? 'active' : '';		
	$output.='<div class="item '.$active.'" >';
	}
	$output.='<div class="bs-item cf">';
	$output.='<div class="bs-item-inner">';
	$output.='<div class="item-img">';
	$output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">';
					 if( has_post_thumbnail() ){  
	$output.= (get_the_post_thumbnail( $r->post->ID, 'shop_thumbnail' )) ? get_the_post_thumbnail( $r->post->ID, 'shop_thumbnail' ):'<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>' ;
					}else{ 
	$output.= '<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>' ;
					} 
	$output.='</a>';
	$output.='</div>';
	$output.='<div class="item-content">';
	if( $count > 0 ){
						$average = number_format($rating / $count, 1);
				
	$output.='<div class="star"><span style="width:'.($average*14).'px'.'"></span></div>';
					
			 } else { 
				
	$output.='<div class="star"></div>';
					
				}
	$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.esc_html( $post->post_title ).'</a></h4>';
    $output.= '<p>'.$product->get_price_html().'</p>';
	$output.='</div></div></div>';
		if( ( $i+1 )%4==0 || ( $i+1 ) == $number ){
	$output.='</div>';
		}
		 $i++;endwhile;
	wp_reset_postdata();
	$output.='</div></div>';
		  return $output;
		  }
		}
		
 }
 add_shortcode('BestSale','yt_bestsale_shortcode');
  /*
 * Related product
 *
 */
 function yt_related_product_shortcode($atts){
	 extract(shortcode_atts(array(
	 'number' => 5,
	 'title'=>'Related Product',
	 'el_class'=>'',
	 'template'=>''
	 ),$atts));
	 global $product, $woocommerce_loop,$wp_query;
	$related = $product->get_related( $number );
	if ( sizeof( $related ) == 0 ) return;
	$args = apply_filters( 'woocommerce_related_products_args', array(
		'post_type'            => 'product',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'posts_per_page'       => $number,
		'post__in'             => $related,
		'post__not_in'         => array( $product->id )
	) );
	$num_post  = count($related);
	$relate = new WP_Query( $args );
	
	if ($relate->have_posts()) :
	$i = 0;
	$j = 0;
	$k = 0;
	 $pf_id = 'bestsale-'.rand().time();
	if($template == 'indicators'){
		$output='<div id="'.$pf_id.'" class="carousel slide sw-related-product" data-ride="carousel">';
		$output.='<ul class="list-unstyled carousel-indicators">';
		 while( $relate->have_posts() ) : $relate->the_post(); 
				if( $j % 3 == 0 ){
					  $active = ( $j == 0 ) ? 'active' : '';	
		$output.='<li data-target="#'.$pf_id.'" data-slide-to="'.$k.'" class="'.$active.'"></li>';
				
			 $k++; } $j++;  endwhile; wp_reset_postdata(); 
		$output.='</ul>';
	}
	if($template == 'slide'){
		$output ='<div id="'.$pf_id.'" class="carousel slide sw-related-product '.$el_class.'" data-ride="carousel" data-interval="0">';
	$output.='<div class="block-title title1">
			<h2>
				<span>'.$title.'</span>
			</h2>
			<div class="customNavigation nav-left-product">
				<a title="Previous" class="btn-bs prev-bs icon-angle-left"  href="#'.$pf_id.'" role="button" data-slide="prev"></a>
				<a title="Next" class="btn-bs next-bs icon-angle-right" href="#'.$pf_id.'" role="button" data-slide="next"></a>
			</div>
		</div>';
	}
		$output.='<div class="carousel-inner">';
			while ($relate->have_posts()) : $relate->the_post();
			global $product, $post, $wpdb, $average;
			$count = $wpdb->get_var($wpdb->prepare("
				SELECT COUNT(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
			",$post->ID));

			$rating = $wpdb->get_var($wpdb->prepare("
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
			",$post->ID));
			if( $i % 4 == 0 ){
				$active = ( $i == 0 ) ? 'active' : '';		
				$output.='<div class="item '.$active.'" >';
				}
			$output.='<div class="bs-item cf">';
	        $output.='<div class="bs-item-inner">';
			$output.='<div class="item-img">';
	$output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">';
					 if( has_post_thumbnail() ){  
	$output.= (get_the_post_thumbnail( $relate->post->ID, 'shop_thumbnail' )) ? get_the_post_thumbnail( $relate->post->ID, 'shop_thumbnail' ):'<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>' ;
					}else{ 
	$output.= '<img src="'.get_template_directory_uri().'/assets/img/placeholder/shop_thumbnail.png" alt="No thumb"/>' ;
					} 
	$output.='</a>';
	$output.='</div>';
	$output.='<div class="item-content">';
	if( $count > 0 ){
						$average = number_format($rating / $count, 1);
				
	$output.='<div class="star"><span style="width:'.($average*14).'px'.'"></span></div>';
					
			 } else { 
				
	$output.='<div class="star"></div>';
					
				}
	$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.esc_html( $post->post_title ).'</a></h4>';
    $output.= '<p>'.$product->get_price_html().'</p>';
	$output.='</div></div></div>';
			 if( ( $i+1 )%4==0 || ( $i+1 ) == $num_post  ){ 
			 $output.='</div>';
			 } 
			 $i++; endwhile; 
		$output.='</div>
	</div>';
	endif;
	wp_reset_postdata();
	return $output;
	
 }
  add_shortcode('yt_related_product','yt_related_product_shortcode');
 /**  Nav Title Style **/
 function yt_nav_title_shortcode($atts,$content = null){
	 extract(shortcode_atts(array(
	 'style'=>'',
	 'color'=>'',
	 'tag'=>'h2',
	 'icon'=>'',
	 'font-color'=>''
	 ),$atts));
	if( $icon != '' ){
			$icon = '<i class="'.$icon.'"></i>';
		}
		return '<section class="block-title '.$style.'">
		<'.$tag.'><span>'.$icon.do_shortcode($content).'</span></'.$tag.'>
	</section>';
 }
 add_shortcode('Title','yt_nav_title_shortcode');
 /*
 * OUR BRAND
 *
 */
 function yt_our_brand_shortcode($atts){
	 extract( shortcode_atts( array(
	'title' => '',
	'type'=>'default',
	'style_title'=>'',
	'numberposts' => 5,
	'orderby'=>'',
	'order' => '',
	'post_status' => 'publish',
	'columns'=>'',
	'columns1'=>'',
	'columns2'=>'',
	'columns3'=>'',
	'columns4'=>'',
	'interval'=>'',
	'effect'=>'slide',
	'hover'=>'hover',
	'swipe'=>'yes',
	'el_class' => ''
), $atts ) );
$tag_id ='sw_partner_slider_'.rand().time();
		$default = array(
			'post_type' => 'partner',
			'orderby' => $orderby,
			'order' => $order,
			'post_status' => 'publish',
			'showposts' => $numberposts
		);
	$list = new WP_Query( $default );
if($type == 'default'){
$output='<div class="'.$el_class.' block-brand">';
if($title !=''){
$output.='<div class="block-title '.$style_title.'">';
if($style_title == 'title3'){
	$wordChunks = explode(" ", $title);
	$firstchunk = $wordChunks[0];
	$secondchunk = $wordChunks[1];
$output.='<h2> <span>'.$firstchunk.'</span> <span class="text-color"> '.$secondchunk.' </span></h2>';
}else {
	$output.='<h2>
				<span>'.$title.'</span>
			</h2>';
    }
}
$output.='<a class="view-all-brand" href="#" title="View All">View All</a>
		</div>';
$output.='<div class="block-content">
				<div class="brand-wrapper">
					<ul>';
				while($list->have_posts()): $list->the_post();
						global  $post;
						$output.='<li><a href="'.post_permalink($post->ID).'" title="'.$post->post_title.'">'.get_the_post_thumbnail($post->ID).'</a></li>';
                     endwhile; wp_reset_postdata();
$output.='</ul>
				</div>
			</div>
		</div>';
		return $output;
}
if($type == 'slide'){
$output='<div id="'.$tag_id.'" class="sw-partner-container-slider flex-slider '.$el_class.'">';
$output.='<div class="page-button top">
				<ul class="control-button preload">
					<li class="preview"></li>
					<li class="next"></li>
				</ul>		
			</div>';
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
$output.='<div class="slider not-js cols-6 '.$deviceclass_sfx.'" data-interval="'.$interval.'" data-effect="'.$effect.'" data-hover="'.$hover.'" data-swipe="'.$swipe.'">
			<div class="vpo-wrap">
				<div class="vp">
					<div class="vpi-wrap">';
						while($list->have_posts()): $list->the_post();
						global  $post;
						$link = get_post_meta( $post->ID, 'link', true );
						$target = get_post_meta( $post->ID, 'target', true );
						$description = get_post_meta( $post->ID, 'description', true );
$output.='<div class="item">
							<div class="item-wrap">';							
								if(has_post_thumbnail()){ 
								$output.='	<div class="item-img item-height">
										<div class="item-img-info">
											<a href="'.$link.'" title="'.esc_attr( $post->post_title ).'" target="'.$target.'">
												'.get_the_post_thumbnail($post->ID).'
											</a>
										</div>
									</div>';
								 }else{ 
$output.='      <div class="item-img item-height">
										<div class="item-img-info">
											<a href="'.$link.'" title="'.esc_attr( $post->post_title ).'" target="'.$target.'">
												<img src="'.get_template_directory_uri().'/assets/img/placeholder/thumbnail.png" alt="No thumb">;
											</a>
										</div>
									</div>';
								 }
								 if( $description != '' ){ 
							$output.='<div class="item-desc">';
								$output.= $description.'
									</div>';		
								 }
						$output.='	</div>
						</div>';
					 endwhile; wp_reset_postdata();
					$output.='</div>
				</div>
			</div>
		</div>
	</div>';
	return $output;
}

 }
 add_shortcode('OurBrand','yt_our_brand_shortcode');
 /*
 * Vertical mega menu
 *
 */
 function yt_vertical_megamenu_shortcode($atts){
	 extract( shortcode_atts( array(
	'menu_locate' =>'',
	'title'  =>'',
	'el_class' => ''
), $atts ) );
$output = '<div class="vc_wp_custommenu wpb_content_element ' . $el_class . '">';
if($title != ''){
$output.='<div class="mega-left-title">
				<strong>'.$title.'</strong>
			</div>';
}
$output.='<div class="wrapper_vertical_menu vertical_megamenu">';
ob_start();
$output .= wp_nav_menu( array( 'menu' => $menu_locate, 'menu_class' => 'nav vertical-megamenu' ) );
$output .= ob_get_clean();
$output .= '</div></div>';
return $output;
 }
 add_shortcode('ya_mega_menu','yt_vertical_megamenu_shortcode');
 /***********************
 * Ya Post 
 *
 ***************************/
 function ya_post_shortcode($atts){
	  $output = $title = $number = $el_class = '';
extract( shortcode_atts( array(
	'title' => '',
	'number' => 5,
	'type' =>'the_blog',
	'category_id' =>'',
	'orderby'=>'',
	'order' => '',
	'post_status' => 'publish',
	'length' => 40,
	'el_class' => ''
), $atts ) );
$pf_id = 'posts-'.rand().time();
$list = get_posts(( array('cat' =>$category_id,'posts_per_page' =>  $number,'orderby' => $orderby,'order' => $order ) ));
//var_dump($list);
if (count($list)>0){
	$i = 0;
	$j = 0;
	$k = 0;
// The blog style
if($type =='the_blog'){
$output ='<div class="block-title-bottom">
			<h2>'.$title.'</h2>
	</div>';
$output .='<div class="widget-the-blog">';
		foreach ($list as $key => $post){
		$output .='<div class="widget-post-inner">';
		$output .='<div class="date-blog-left">
					<div class="d-blog">
						'.get_the_modified_date('j').'			
					</div>
					<div class="m-blog">
						'.get_the_modified_date('M').'	
					</div>
				</div>';
		$output .= '<div class="widget-caption">';
		$output .= '<div class="item-title">';
		$output	.= '<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.esc_html( $post->post_title ).'</a></h4>';
	  	$output	.=	'</div><div class="item-content">';
							if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
								$content = explode($matches[0], $post->post_content, 2);
								$content = $content[0];
							} else {
								$content = wp_trim_words($post->post_content, $length, ' ');
							}
		$output	.=	esc_html( $content );
		$output	.=	'</div>
				</div>
			</div>';
		}
	$output .='</div>';
     return $output;
	}
// 2 Column Style
if($type == '2_column'){
	$output='<div class="widget-the-blog">';
	$output .='<ul>';
		foreach ($list as $key => $post){
	    if ( $key == 0 && get_the_post_thumbnail( $post->ID ) ) {
	$output .='<li class="widget-post item-'.$key.'">';
	$output	.='<div class="widget-post-inner">';
	$output	.='<div class="widget-thumb">';
	$output .='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.get_the_post_thumbnail($post->ID, 'medium').'</a>';
	$output	.='</div>';
	$output .='<div class="widget-caption">';
	$output .='<div class="item-title">';
	$output .='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.$post->post_title.'</a></h4>';
	$output .='<div class="entry-meta">';
	$output .='<span class="entry-time">'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'.'</span>';
	$output .='<span class="entry-comment"><i class="icon-comment"></i>'.$post->comment_count .'<span>'. __(' comments', 'yatheme').'</span></span>';
	$output	.='<span class="entry-author"><i class="icon-user"></i>'.get_the_author_link().'</span></div></div>';
	$output.='<div class="item-content">';
							if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
								$content = explode($matches[0], $post->post_content, 2);
								$content = $content[0];
							} else {
								$content = wp_trim_words($post->post_content, $length, ' ');
							}
	$output.= esc_html( $content );
	$output.='</div></div></div>';
	$output.='</li>';
		} else {
	$output.='<li class="widget-post item-'.$key.'">';
	$output.='<div class="widget-post-inner">';
	$output.='<div class="widget-caption">';
	$output.='<div class="item-title">';
	$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.$post->post_title.'</a></h4>';
	$output.='<div class="item-publish">'.human_time_diff(strtotime($post->post_date), current_time('timestamp') ) . ' ago</div>';
	$output.='	</div></div></div>';					
	$output.='</li>';
   } 
 }
	$output.='</ul>';
    $output.='</div>';
	return $output;
}
// Slide Show Style
if($type == 'slide_show'){
	$output = '<div id="'.$pf_id.'" class="carousel slide content" data-ride="carousel">';
    $output.='<div class="carousel-inner">';
     foreach( $list as $i => $item ){
		 if( $i == 0 ){ 
          $output.='<div class="item active">';
		 }else{
			   $output.='<div class="item">';
		 }
    $output.='<a href="'.post_permalink($item->ID).'" title="'.$item->post_title.'">'.get_the_post_thumbnail($item->ID).'</a>';
	$output.='<div class="entry-meta"><span class="entry-comment"><i class="icon-comment"></i>'.$item->comment_count.'</span></div>';						
    $output.= '<div class="carousel-caption">';
    $output.='<div class="carousel-caption-inner">';
    $output.='<a href="'.post_permalink($item->ID).'">'.$item->post_title.'</a>';
    $output.='<div class="item-description">';
				if ( preg_match('/<!--more(.*?)?-->/', $item->post_content, $matches) ) {
					$content = explode($matches[0], $item->post_content, 2);
					$content = $content[0];
				} else {
					$content = wp_trim_words($item->post_content, $length, ' ');
				}
	$output.= esc_html( $content );
	$output.='</div></div></div></div>';
     }
  $output.='</div>';
  //Controls
  	$output.='<div class="carousel-cl">';
    $output.='<a class="left carousel-control" href="#'.$pf_id.'" role="button" data-slide="prev"></a>';
    $output.='<a class="right carousel-control" href="#'.$pf_id.'" role="button" data-slide="next"></a>';
	$output.='</div></div>';
	return $output;
}
// Middle Right
if($type == 'middle_right'){
    $output ='<div class="widget-the-blog news-style">';
	$output.='<ul>';
     foreach ($list as $key => $post){
	if ( $key == 0 ) {
	$output.='<div class="view-all"><a href="'.get_category_link($category_id).'">'. esc_attr__( 'View All', 'yatheme' ).'<i class="icon-caret-right"></i></a></div>';
	$output.='<li class="widget-post item-'.$key.' first-news">';
	$output.='<div class="widget-post-inner">';
	$output.='<div class="widget-thumb">';
	$output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.get_the_post_thumbnail($post->ID, 'medium').'</a>';
	$output.='</div>';
	$output.='<div class="widget-caption">';
	$output.='<div class="item-title">';
	$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.$post->post_title.'</a></h4>';
	$output.='<div class="entry-meta">';
	$output.='<span class="entry-time">'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago</span>';
	$output.='<span class="entry-comment"><i class="icon-comment"></i>'.$post->comment_count.'</span>';
    $output.='<span class="entry-author"><i class="icon-user"></i>'.get_the_author_link().'</span>';		
	$output.='</div></div>';
	$output.='<div class="item-content">';
					
							if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
								$content = explode($matches[0], $post->post_content, 2);
								$content = $content[0];
							} else {
								$content = wp_trim_words($post->post_content, $length, ' ');
							}
	$output.= esc_html( $content );
	$output.='</div></div></div></li>';
		 } else {
	$output.='<li class="widget-post item-'.$key.' other-news">';
	$output.='<div class="widget-post-inner">';
	$output.='<div class="widget-thumb">';
	$output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.get_the_post_thumbnail($post->ID, 'thumbnail').'</a>';
	$output.='</div>';
	$output.='<div class="widget-caption">';
	$output.='<div class="item-title">';
	$output.='<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.$post->post_title.'</a></h4>';
	$output.='<div class="item-publish">'.human_time_diff(strtotime($post->post_date), current_time('timestamp') ) . ' ago</div>';
	$output.='</div></div></div></li>';
	} 
	}
	$output.='</ul>
</div>';
return $output;
}
////////   our member //////
if($type='indicators') {
    $output='<div class="sw-member">';
	$output.='<div id="'.$pf_id.'" class="carousel slide row">';
	$output.='<ol class="carousel-indicators">';
	  		  foreach( $list as $i => $post ){ 
				 if( $j %4 == 0 ) {  
				 $k++;
    $active = ( $i == 0 ) ? 'active' : '';		
	
	$output.='<li class=" '.$active.' " data-slide-to="'.($k-1).'" data-target="#'.$pf_id.'">';
					 }  if( ( $j+1 ) % 3 == 0 || ( $j+1 ) == $number ){
	$output.='</li>';
				}	
				$j++; 
	    	  } 
  	$output.='</ol>';
    $output.='<div class="carousel-inner">';
			foreach ($list as $i => $post) {
				if($i %4 == 0){
		$active = ( $i == 0 ) ? 'active' : '';	
		$selected =($i==0)?'selected' : '';
	$output.='<div class="item '.$active.'">';
			   }
	$output.='<div class="item-member-wrappers col-lg-3 col-md-3 col-sm-6 col-xs-12 clearfix '.$selected.'">
					<div class="item-content">						
						<div class="member-thumb">';
							 if (has_post_thumbnail($post->ID)) {
    $output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.get_the_post_thumbnail($post->ID, 'thumbnail_content').'</a>';
							 }else{ 
	$output.='<a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'"><img src="'.plugins_url( 'img/thumbnail.png' ,dirname(__FILE__)).'" alt="No thumb"/></a>';
							    } 
	$output.=	'					<div class="hover-social">
								<div class="social twitter">
									<a href="https://twitter.com/smartaddons"></a>
								</div>
								<div class="social facebook">
									<a href="https://www.facebook.com/SmartAddons.page"></a>
								</div>
								<div class="social flickr">
									<a href="https://www.flickr.com/smartaddons"></a>
								</div>
							</div>
						</div>
						<div class="member-content">
							<h5><a href="#" title="'.esc_attr( $post->post_title ).'">'.esc_attr( $post->post_title ).'</a></h5>
							<div class="span">
								<p>'.esc_html($content = wp_trim_words($post->post_content, $length, ' ')).'</p>
							</div>
						</div>
					</div>
				</div>	';
				 if(($i+1)%4==0 || $i+1 == count($list)){ 
				$output.='</div>'; }
			  } 
$output.='		</div>
	</div>
</div>';
return $output;
}
}
}
 add_shortcode('ya_post','ya_post_shortcode');
 function get_url_shortcode($atts) {
	 if(is_front_page()){
		 $frontpage_ID = get_option('page_on_front');
		 $link =  get_site_url().'/?page_id='.$frontpage_ID ;
		 return $link;
	 }
	 elseif(is_page()){
		$pageid = get_the_ID();
		 $link = get_site_url().'/?page_id='.$pageid ;
		 return $link;
	 }
	 else{
		 $link = $_SERVER['REQUEST_URI'];
		 return $link;
	 }
	 
	 
 }
 add_shortcode('get_url','get_url_shortcode');