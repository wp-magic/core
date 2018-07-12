<?php

add_action( 'admin_menu', function () {
  magic_dashboard_add_menu_page( array (
    'title' => 'Magic',
  ) );

  $title = 'Magic Dashboard Settings';

  $settings = array(
    array (
      'type' => 'header',
      'name' => 'header_single_page',
      'label' => 'Error Messages',
    ),

    array (
      'name' => 'nonce',
      'type' => 'text',
      'default' => 'Security Error. Please retry later.',
      'label' => 'Nonce Error',
    ),
    array (
      'name' => 'signon',
      'type' => 'text',
      'default' => 'Login Error. Please try again later.',
      'label' => 'Login Error',
    ),
    array (
      'name' => 'password_mismatch',
      'type' => 'text',
      'default' => 'The passwords do not match.',
      'label' => 'Password Mismatch Error',
    ),
    array (
      'name' => 'insert',
      'type' => 'text',
      'default' => 'Item could not be added.',
      'label' => 'Creation Error',
    ),
  );

  $submenu_config = array (
    'link' => 'Error Messages',
    'title' => $title,
    'slug' => MAGIC_DASHBOARD_SLUG . '_error_messages',
    'settings' => $settings,
    'is_array' => true,
  );

  magic_dashboard_add_submenu_page( $submenu_config );
}, 1 );
