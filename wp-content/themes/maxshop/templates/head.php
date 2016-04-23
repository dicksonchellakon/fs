<!DOCTYPE html>
<?php
	$direction = ya_options()->getCpanelValue('direction');
?>
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>         <html class="no-js ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?> <?php if( $direction == 'rtl' ){echo 'dir="rtl"';}; ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<?php
	$wp_version = get_bloginfo( 'version' );
	if ( version_compare( $wp_version, '4.1', '<' ) ) { ?>
		<title><?php wp_title('|', true, 'right'); ?></title>
	<?php } ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="google-site-verification" content="iCk8RoEeGFH9zc0TAeCCIylUTXWLVbzTdE64zUTBVN0" /> 
	<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url( home_url()); ?>/feed/">
	<?php wp_head(); ?>
</head>
