<?php
/**
 * Util functionality to set and get option fields
 *
 * @package Magic
 * @since 0.0.1
 */

if ( ! function_exists( 'magic_set_option' ) ) {
	/**
	 * Update an option field in the db
	 *
	 * @since 0.0.1
	 *
	 * @param string $name of the field.
	 * @param any    $value to write.
	 */
	function magic_set_option( string $name, $value ) {
		if ( function_exists( 'update_blog_option' ) ) {
			update_blog_option( null, $name, $value );
		} else {
			update_option( $name, $value );
		}
	}
}

if ( ! function_exists( 'magic_get_option' ) ) {
	/**
	 * Get an option field from the db
	 *
	 * @since 0.0.1
	 *
	 * @param string $name of the field.
	 * @param any    $default to return if value is not set.
	 */
	function magic_get_option( string $name, $default = '' ) {
		if ( function_exists( 'get_blog_option' ) ) {
			$val = get_blog_option( null, $name );
		} else {
			$val = get_option( $name );
		}

		if ( empty( $val ) ) {
			return $default;
		}

		return $val;
	}
}
