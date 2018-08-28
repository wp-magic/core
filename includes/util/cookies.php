<?php

function magic_deserialize_cookie( string $str, string $sep = MAGIC_GDPR_COOKIE_SEP ) {
  $string_array = explode( PHP_EOL, $str);
  $cookies = [];
  foreach ( $string_array as $string ) {
    $arr = explode( $sep, $string );

    if ( empty( $arr ) || empty( $arr[0] ) ) {
      break;
    }

    $name = esc_html( trim( $arr[0] ) );
    $slug = esc_html( trim( $arr[1] ) );
    $desc = esc_html( trim( $arr[2] ) );
    $cook = esc_html( trim( $arr[3] ) );
    $on   = !empty($_POST[$slug]) || !empty( $_POST['accept_all'] );

    $cookies[] = array (
      'name' => $name,
      'slug' => $slug,
      'description' => $desc,
      'cookies' => explode( ',', $cook ),
      'on' => $on,
    );
  }

  return $cookies;
}

function magic_serialize_cookie( array $array, string $sep = MAGIC_GDPR_COOKIE_SEP ) {
  $array = implode( $sep, $array );
  $string = implode( PHP_EOL, $array );
  return $string;
}
