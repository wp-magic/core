<?php

if ( !function_exists( 'magic_login_or_create_user' ) ) {
  function magic_login_or_create_user( array $ctx ) {
    $ctx['user'] = get_current_user_id();

    if ( empty( $ctx['user'] ) ) {
      if ( empty( $ctx['query']['log'] ) ) {
        $ctx['errors'][] = 'log_missing';
      }

      if ( empty( $ctx['query']['pwd'] ) ) {
        $ctx['errors'][] = 'pwd_missing';
      }

      if ( !empty( $ctx['errors'] ) ) {
        return $ctx;
      }

      if ( !empty( $ctx['query']['register'] ) ) {
        if ( empty( $ctx['query']['pwd2'] ) ) {
          $ctx['errors'][] = 'pwd2_missing';
        }

        if ( $ctx['query']['pwd'] !== $ctx['query']['pwd2'] ) {
          $ctx['errors'][] = 'pwd_mismatch';
        }

        if ( !empty( $ctx['errors'] ) ) {
          return $ctx;
        }

        $ctx['user'] = wp_create_user(
          $ctx['query']['username'],
          $ctx['query']['pwd'],
          $ctx['query']['log']
        );

        if ( is_wp_error( $ctx['user'] ) ) {
          foreach ( $ctx['user']->errors as $key => $val ) {
            $ctx['errors'][] = $key;
          }

          return $ctx;
        }
      }

      $user = wp_signon();
      $ctx['user'] = $user->ID;

      if ( is_wp_error( $user ) ) {
        array_merge( $ctx['errors'], $user->errors );
      } else {
        $ctx['user'] = $user;
      }
    }

    return $ctx;
  }
}
