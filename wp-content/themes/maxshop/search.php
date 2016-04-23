<?php get_header(); ?>
<?php
	$post_type = isset( $_GET['search_posttype'] ) ? $_GET['search_posttype'] : '';
	if( ( $post_type != '' ) &&  locate_template( 'templates/search-' . $post_type . '.php' ) ){
		get_template_part( 'templates/search', $post_type );
	}else{
?>
<div class="container">
	<?php 
		get_template_part('templates/content');
	?>
</div>
<?php } ?>
<?php get_footer(); ?>