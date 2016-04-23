<?php

$lib_dir = trailingslashit( str_replace( '\\', '/', dirname(__FILE__) ) );
$lib_abs = trailingslashit( str_replace( '\\', '/', ABSPATH ) );

if( !defined('YA_DIR') ){
	define( 'YA_DIR', $lib_dir );
}

if( !defined('YA_URL') ){
	define( 'YA_URL', site_url( str_replace( $lib_abs, '', $lib_dir ) ) );
}