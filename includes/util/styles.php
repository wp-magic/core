<?php
/**
 * Register Magic css styles using wp_less
 *
 * @package Magic
 * @since 0.0.1
 */

/**
 * Register and enqeue less styles
 *
 * @since 0.0.1
 *
 * @param string $slug of the stylesheet.
 * @param string $base_path of the stylesheet.
 */
function magic_register_style( $slug, $base_path ) {
	$content_url = content_url();
	$plugins_url = plugins_url();

	$plugin_path = str_replace( $content_url, '', $plugins_url );

	$local_path = $plugin_path . '/' . $base_path . '/' . $slug . '.less';

	wp_register_style( $slug, $local_path, [], MAGIC_STYLESHEET_VERSION );
	wp_enqueue_style( $slug );
}
