<?php
/**
 * URL rewriting
 *
 * Rewrites currently do not happen for child themes (or network installs)
 * @todo https://github.com/retlehs/roots/issues/461
 *
 * Rewrite:
 *   /wp-content/themes/themename/css/ to /css/
 *   /wp-content/themes/themename/js/  to /js/
 *   /wp-content/themes/themename/img/ to /img/
 *   /wp-content/plugins/              to /plugins/
 *
 * If you aren't using Apache, alternate configuration settings can be found in the docs.
 *
 * @link https://github.com/retlehs/roots/blob/master/doc/rewrites.md
 */
function ya_add_rewrites($content) {
	global $ya_rewrite;
	$ya_new_non_wp_rules = array(
			'css/(.*)'  => THEME_PATH . '/css/$1',
			'js/(.*)'   => THEME_PATH . '/js/$1',
			'assets/img/(.*)'  => THEME_PATH . '/assets/img/$1',
			'assets/font/(.*)' => THEME_PATH . '/assets/font/$1',
			'plugins/(.*)'     => RELATIVE_PLUGIN_PATH . '/$1'
	);
	$ya_rewrite->non_wp_rules = array_merge($ya_rewrite->non_wp_rules, $ya_new_non_wp_rules);
	return $content;
}

function ya_clean_urls($content) {
	if (strpos($content, FULL_RELATIVE_PLUGIN_PATH) === 0) {
		return str_replace(FULL_RELATIVE_PLUGIN_PATH, WP_BASE . '/plugins', $content);
	} else {
		return str_replace('/' . THEME_PATH, '', $content);
	}
}

if (!is_multisite() && !is_child_theme() && get_option('permalink_structure')) {
	if (current_theme_supports('rewrites')) {
		add_action('generate_rewrite_rules', 'ya_add_rewrites');
	}

	if ( !is_admin() && current_theme_supports('rewrites') ) {
		$tags = array(
				'plugins_url',
				'bloginfo',
				'stylesheet_directory_uri',
				'template_directory_uri',
				'script_loader_src',
				'style_loader_src'
		);

		add_filters($tags, 'ya_clean_urls');
	}
}
