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
  function magic_verify_nonce( string $nonce, string $slug, bool $redirect = true ) {
    if( !wp_verify_nonce( $nonce, $slug ) ) {
      magic_add_query_error( 'nonce' );
      if ( !$redirect ) {
        return true;
      }
    }

    if ( $redirect ) {
      magic_redirect_if_error();
    }
  }
}

if ( !function_exists( 'magic_check_arguments' ) ) {
  function magic_check_arguments( array $args = [] ) {
    foreach ( $args as $key => $msg ) {
      if ( empty( $_POST[$key] ) ) {
        magic_add_query_arg( ['error' => $msg] );
      }
    }

    magic_redirect_if_error();
  }
}

if ( !function_exists( 'magic_parse_arguments' ) ) {
  function magic_parse_arguments( array $args = [], array $ctx = [] ) {
    $defaults = [
      'query' => [],
      'errors' => [],
    ];

    $ctx = array_merge( $defaults, $ctx );

    foreach ( $args as $key => $error ) {
      if ( !empty( $_POST[$key] ) ) {
        if ( $key === 'nonce' && !wp_verify_nonce( $_POST[$key], $error ) ) {
          $ctx['errors'][] = 'nonce';
        } else {
          $ctx['query'][$key] = sanitize_textarea_field( $_POST[$key] );
        }
      } else if ( !empty( $error ) ) {
        if ( $key === 'nonce' ) {
          $ctx['errors'][] = $key;
        } else {
          $ctx['errors'][] = $error;
        }
      } else {
        $ctx['query'][$key] = '';
      }
    }

    if ( defined( 'MAGIC_GDPR_SLUG' ) ) {
      if ( 'on' === $ctx['query']['allow_cookies'] ) {
        magic_gdpr_set_cookies( array( 'settings', 'auth' ) );
      } else if ( !magic_gdpr_check_cookies() ) {
        $ctx['errors'][] = 'cookie';
      }
    }

    return $ctx;
  }
}

if ( !function_exists( 'magic_is_same_user') ) {
  function magic_is_same_user( int $user_id = -1 ) {
    return $user_id === -1 || get_current_user_id() === $user_id;
  }
}

if ( !function_exists( 'magic_is_logged_in' ) ) {
  function magic_is_logged_in() {
    return get_current_user_id() > 0;
  }
}

if ( !function_exists( 'magic_require_login' ) ) {
  function magic_require_login( $redirect = 0, int $user_id = -1 ) {
    $is_logged_in = magic_is_logged_in();
    $is_same_user = magic_is_same_user( $user_id );

    if ( !$is_logged_in || !$is_same_user ) {
      if ( $redirect !== false ) {
        if ( !$redirect ) {
          $redirect = magic_get_option( 'magic_user_admin_login_page', '/login' );
        }

        wp_redirect( $redirect );
        exit;
      }

      return false;
    }

    return true;
  }
}
