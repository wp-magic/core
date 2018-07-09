<?php
if ( empty( $_SERVER['MAGIC_REFERER'] ) ) {
  $_SERVER['MAGIC_REFERER'] = explode( '?', $_SERVER['REDIRECT_URL'])[0];
}

if ( !function_exists( 'magic_request' ) ) {
  function magic_request( string $referer = null ) {
    if ( empty( $referer ) ) {
      $referer = explode( '?', $_SERVER['REDIRECT_URL'] )[0];
    }

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
          $params = [];
          $query = parse_url( $ref, PHP_URL_QUERY );
          parse_str($query, $params);
          if ( !empty( $params[$k] ) ) {
            $ref = add_query_arg( $k, $r . ',' . $params[$k] );
          } else {
            $ref = add_query_arg( $k, $r, $ref );
          }
        }
      }
    }

    $_SERVER['MAGIC_REFERER'] = $ref;
    return $_SERVER['MAGIC_REFERER'];
  }
}

if ( !function_exists( 'magic_add_query_error' ) ) {
  function magic_add_query_error( string $err = 'unknown') {
    magic_add_query_arg( ['error' => $err ] );
  }
}

if ( !function_exists( 'magic_verify_nonce') ) {
  function magic_verify_nonce( string $nonce, string $slug ) {
    if( !wp_verify_nonce( $nonce, $slug ) ) {
      magic_add_query_error( 'nonce' );
    }

    magic_redirect_if_error();
  }
}

if ( !function_exists( 'magic_check_arguments') ) {
  function magic_check_arguments( array $args = [] ) {
    foreach ( $args as $key => $msg ) {
      if ( empty( $_POST[$key] ) ) {
        magic_add_query_arg( ['error' => $msg]);
      }
    }

    magic_redirect_if_error();
  }
}

if ( !function_exists( 'magic_require_login' ) ) {
  function magic_require_login() {
    $current_user = wp_get_current_user();
    if ( $current_user->ID === 0) {
      wp_redirect( magic_get_option( 'magic_user_admin_login_page', '/login' ) );
      exit;
    }
  }
}
