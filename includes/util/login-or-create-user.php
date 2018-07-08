<?php

if ( !function_exists( 'magic_login_or_create_user' ) ) {
  function magic_login_or_create_user() {
    $user = wp_get_current_user();

    if ( empty( $user->ID ) ) {
      if ( !username_exists( $_POST['email'] ) && !email_exists($_POST['email']) ) {
        if ( $_POST['password'] !== $_POST['password2'] ) {
          magic_redirect( [ 'error' => 'password_mismatch' ] );
        }
      	$user->ID = wp_create_user( $_POST['email'], $_POST['password'], $_POST['email'] );
      }

      $credentials = array(
        'user_login' => $_POST['email'],
        'user_password' => $_POST['password'],
        'remember' => isset($_POST['remember']) ? $_POST['remember'] : false,
      );

      $user = wp_signon( $credentials );
    }

    if ( is_wp_error( $user ) || empty( $user->ID) ) {
      magic_redirect( [ 'error' => 'signon' ] );
    }

    return $user;
  }
}
