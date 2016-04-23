<?php
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
$el_class = $this->getExtraClass( $el_class );
if (count($list)>0){
// The blog style
if($type =='the_blog'){
$output ='<div class="widget-the-blog">';
$output .='<ul>';
		foreach ($list as $key => $post){
		$output .='<li class="widget-post item-'.$key.'">';
		$output .='<div class="widget-post-inner">';
				if ( $key == 0 && get_the_post_thumbnail( $post->ID ) ) {
		$output .= '<div class="widget-thumb">';
		$output .='<a href="'. post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.get_the_post_thumbnail($post->ID, 'thumbnail').'</a>';
		$output	.='</div>';
				 } 
		$output .= '<div class="widget-caption">';
		$output .= '<div class="item-title">';
		$output	.= '<h4><a href="'.post_permalink($post->ID).'" title="'.esc_attr( $post->post_title ).'">'.esc_html( $post->post_title ).'</a></h4>';
		$output	.=	'<div class="item-publish">'.human_time_diff(strtotime($post->post_date), current_time('timestamp') ) . ' ago'.'</div>';
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
			</div>
		 </li>';
		}
	$output .='</ul></div>';
     echo $output;
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
	echo $output;
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
	echo $output;
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
echo $output;
}
}
