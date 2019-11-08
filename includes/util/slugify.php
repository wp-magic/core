<?php
/**
 * Slugify returns a slug safe string
 *
 * @package Magic
 * @since 0.0.1
 */

if ( ! function_exists( 'magic_slugify' ) ) {
	/**
	 * Slugify a value to be safe as a slug.
	 *
	 * @since 0.0.1
	 *
	 * @param string $value to be slugified.
	 *
	 * @return string slugified value.
	 */
	function magic_slugify( string $value = '' ) {
		$value = trim( $value );
		$value = strtolower( $value );

		return str_replace( ' ', '_', $value );
	}
}
