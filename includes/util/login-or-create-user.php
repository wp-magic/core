<?php

if ( !function_exists( 'magic_login_or_create_user' ) ) {
  function magic_login_or_create_user( array $ctx ) {
    $arguments = array (
      'log' => 'missing_log',
      'pwd' => 'missing_pwd',
      'pwd2' => false,
    );

    $user = wp_get_current_user();

    if ( empty( $user->ID ) ) {
      if ( !username_exists( $_POST['log'] ) && !email_exists($_POST['log']) ) {
        $arguments['pwd2'] = 'missing_pwd2';

        $ctx = magic_parse_arguments( $arguments, $ctx );

        if ( !empty( $ctx['errors'] ) ) {
          return $ctx;
        }

        if ( $ctx['query']['pwd'] !== $ctx['query']['pwd2'] ) {
          $ctx['errors'][] = 'mismatch_password';
        }

        if ( !empty( $ctx['errors'] ) ) {
          return $ctx;
        }

      	$created_user_id = wp_create_user( $ctx['query']['log'], $ctx['query']['pwd'], $ctx['query']['log'] );

        if ( is_wp_error( $created_user_id ) || empty( $created_user_id ) ) {
          $ctx['errors'][] = 'create_user';
        }
      }

      $ctx = magic_parse_arguments( $arguments, $ctx );

      if ( !empty( $ctx['errors'] ) ) {
        return $ctx;
      }

      $user = wp_signon();

      if ( is_wp_error( $user ) || empty( $user ) ) {
        $ctx['errors'][] = 'signon';
      } else {
        $ctx['user'] = $user;
      }
    }

    return $ctx;
  }
}
