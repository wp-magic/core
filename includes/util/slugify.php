<?php

if ( !function_exists( 'magic_slugify' ) ) {
  function magic_slugify( string $value = '' ) {
    $value = trim( $value );
    $value = strtolower( $value );
    return str_replace( ' ', '_', $value );
  }
}
