<?php
/**
 * Serialize / Deserialize cookies
 *
 * @package Magic
 * @since 0.0.1
 */

/**
 * Deserialize a cookie string into an array.
 *
 * @param string $str input string to derive from.
 * @param string $sep separator string to split by.
 *
 * @return array array of cookie key => value pairs.
 */
function magic_deserialize_cookie( string $str, string $sep = MAGIC_DASHBOARD_COOKIE_SEP ) {
	$string_array = explode( PHP_EOL, $str );
	$cookies      = [];

	foreach ( $string_array as $string ) {
		$arr = explode( $sep, $string );

		if ( empty( $arr ) || empty( $arr[0] ) ) {
			break;
		}

		$name = esc_html( trim( $arr[0] ) );
		$slug = esc_html( trim( $arr[1] ) );
		$desc = esc_html( trim( $arr[2] ) );
		$cook = esc_html( trim( $arr[3] ) );

		$cookies[] = array(
			'name'        => $name,
			'slug'        => $slug,
			'description' => $desc,
			'cookies'     => explode( ',', $cook ),
		);
	}

	return $cookies;
}

/**
 * Serialize a cookie array into a string.
 *
 * @param array  $array input array to concatenate.
 * @param string $sep separator to join by.
 *
 * @return string a serialized cookie.
 */
function magic_serialize_cookie( array $array, string $sep = MAGIC_DASHBOARD_COOKIE_SEP ) {
	$array  = implode( $sep, $array );
	$string = implode( PHP_EOL, $array );
	return $string;
}
