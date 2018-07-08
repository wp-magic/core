<?php

add_action( 'admin_menu', function () {
  magic_dashboard_add_menu_page( array (
    'title' => 'Magic',
  ) );

  $title = 'Magic Dashboard Settings';

  $settings = array(
    array (
      'type' => 'header',
      'name' => MAGIC_DASHBOARD_SLUG . '_header_single_page',
      'label' => 'Error Messsages',
    ),

    array (
      'name' => MAGIC_DASHBOARD_SLUG . '[nonce]',
      'type' => 'text',
      'default' => 'Security Error. Please retry later.',
      'label' => 'Nonce Error',
    ),
    array (
      'name' => MAGIC_DASHBOARD_SLUG . '[signon]',
      'type' => 'text',
      'default' => 'Login Error. Please try again later.',
      'label' => 'Login Error',
    ),
    array (
      'name' => MAGIC_DASHBOARD_SLUG . '[password_mismatch]',
      'type' => 'text',
      'default' => 'The passwords do not match.',
      'label' => 'Password Mismatch Error',
    ),
    array (
      'name' => MAGIC_DASHBOARD_SLUG . '[password_mismatch]',
      'type' => 'text',
      'default' => 'The passwords do not match.',
      'label' => 'Password Mismatch Error',
    ),
    array (
      'name' => MAGIC_DASHBOARD_SLUG . '[insert]',
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
  );

  magic_dashboard_add_submenu_page( $submenu_config );
}, 1 );
