<?php
/**
 * yatheme initial setup and constants
 */
function ya_setup() {
	// Make theme available for translation
	load_theme_textdomain('yatheme', get_template_directory() . '/lang');

	// Register wp_nav_menu() menus (http://codex.wordpress.org/Function_Reference/register_nav_menus)
	register_nav_menus(array(
		//'header_menu' => __('Header Menu', 'yatheme'),
		//'footer_menu' => __('Footer Menu', 'yatheme'),
		'primary_menu' => __('Primary Menu', 'yatheme'),
	));
	
	
	add_theme_support( 'automatic-feed-links' );
	$wp_version = get_bloginfo( 'version' );
	if ( version_compare( $wp_version, '4.1', '>' ) ) {
		add_theme_support( "title-tag" );
	} 
	// Add post thumbnails (http://codex.wordpress.org/Post_Thumbnails)
	add_theme_support('post-thumbnails');
	// set_post_thumbnail_size(150, 150, false);
	// add_image_size('category-thumb', 300, 9999); // 300px wide (and unlimited height)

	// Add post formats (http://codex.wordpress.org/Post_Formats)
	add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'));

	// Tell the TinyMCE editor to use a custom stylesheet
	add_editor_style('/assets/css/editor-style.css');
	
	new YA_Menu();
}
add_action('after_setup_theme', 'ya_setup');

