<?php
if ( empty( $_SERVER['MAGIC_REFERER'] ) ) {
  $_SERVER['MAGIC_REFERER'] = $_SERVER['REDIRECT_URL'];
}

if ( !function_exists( 'magic_start_request' ) ) {
  function magic_request( string $referer = '/' ) {
    $_SERVER['MAGIC_REFERER'] = $referer;
  }
}

if ( !function_exists( 'magic_redirect' ) ) {
  function magic_redirect( array $refs = [] ) {
    if ( !empty( $refs ) ) {
      magic_add_query_arg( $refs );
    }

    wp_redirect( $_SERVER['MAGIC_REFERER'] );
    exit;
  }
}

if ( !function_exists( 'magic_redirect_if_error' ) ) {
  function magic_redirect_if_error() {
    if ( strpos( $_SERVER['MAGIC_REFERER'], 'error' ) !== false ) {
      wp_redirect( $_SERVER['MAGIC_REFERER'] );
      exit;
    }
  }
}

if ( !function_exists( 'magic_add_query_arg' ) ) {
  function magic_add_query_arg( array $refs = [] ) {
    if ( empty( $_SERVER['MAGIC_REFERER'] ) ) {
      $_SERVER['MAGIC_REFERER'] = $_SERVER['REDIRECT_URL'];
    }

    $ref = $_SERVER['MAGIC_REFERER'];

    if ( !empty( $refs ) ) {
      foreach ( $refs as $k => $r ) {
        $k = urlencode($k);
        $r = urlencode($r);

        if ( !empty( $r ) && !empty( $k ) ) {
          if ( !empty( $ref[$k] ) ) {
            $r = urlencode($ref[$k] . ',' . $r);
          }

          $ref = add_query_arg( $k, $r, $ref );
        }
      }
    }

    $_SERVER['MAGIC_REFERER'] = $ref;
    return $_SERVER['MAGIC_REFERER'];
  }

}

if ( !function_exists( 'magic_verify_nonce') ) {
  function magic_verify_nonce( string $nonce, string $slug ) {
    if( !wp_verify_nonce( $nonce, $slug ) ) {
      magic_redirect( [ 'error' => 'nonce' ] );
    }
  }
}
