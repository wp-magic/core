<?php

function magic_dashboard_add_menu_page( array $atts = [] ) {
  $default = array(
    'title' => 'Magic',
    'slug' => MAGIC_DASHBOARD_SLUG,
    'capability' => 'manage_options'
  );

  $atts = shortcode_atts( $default, $atts );

  add_menu_page(
      $atts['title'],
      $atts['title'],
      $atts['capability'],
      $atts['slug'],
      function () {
        $context = Timber::get_context();

        Timber::render( plugin_dir_path( __FILE__ ) . 'views/dashboard.twig', $context );
      },
      'dashicons-carrot',
      25
  );


  // add_submenu_page(
  //   MAGIC_DASHBOARD_SLUG,
  //   'Magic',
  //   'Magic',
  //   'manage_options',
  //   MAGIC_DASHBOARD_SLUG,
  //   function () {
  //     $context = Timber::get_context();
  //
  //     Timber::render( plugin_dir_path( __FILE__ ) . 'views/dashboard.twig', $context );
  //   }
  // );
}

function magic_dashboard_add_submenu_page( array $atts = [] ) {
  $default = array (
    'capability' => 'edit_posts',
    'link' => 'Magic Admin Panel',
    'title' => 'Magic Admin Panel',
    'slug' => 'magic_admin_panel',
    'settings' => [],
    'parent' => MAGIC_DASHBOARD_SLUG,
  );

  $atts = shortcode_atts( $default, $atts );

  add_submenu_page(
    $atts['parent'],
    $atts['link'],
    $atts['link'],
    $atts['capability'],
    $atts['slug'],
    function() use ( $atts ) {
      magic_dashboard_render_admin_page( $atts['title'], $atts['settings'] );
    }
  );
}

function magic_dashboard_render_admin_page( string $title, array $settings ) {
  $context = Timber::get_context();

  if ( !empty( $_POST ) ) {
    $context['settings'] = magic_dashboard_set_options( $settings );
  } else {
    $context['settings'] = magic_dashboard_get_options( $settings );
  }

  $context['title'] = $title;

  Timber::render( 'views/dashboard-subpage.twig', $context );
}

function magic_dashboard_get_options( $settings ) {
  $options = array();
  foreach ( $settings as $setting ) {
    $name = $setting['name'];
    if ($setting['type'] !== 'header') {
      $setting['value'] = magic_get_option( $name );
    }

    $options[$name] = $setting;
  }

  return $options;
}

function magic_dashboard_set_options( array $settings = [] ) {
  if ( is_array( $_POST[MAGIC_DASHBOARD_SLUG] ) ) {
    magic_set_option( MAGIC_DASHBOARD_SLUG, $_POST[MAGIC_DASHBOARD_SLUG] );
  } else {
    foreach ( $settings as $setting ) {
      $name = $setting['name'];
      if ( isset( $_POST[$name] ) ) {
        $value = $_POST[$name];

        magic_set_option( $name, $value );
      }
    }
  }

  return magic_dashboard_get_options( $settings );
}

function magic_dashboard_render_settings_fields( array $settings = [] ) {
  foreach ( $settings as $key => $setting ) {
    $default = array (
      'default' => '',
      'name' => '',
      'type' => '',
    );

    $setting = array_merge( $default, $setting );

    $setting['type'] = !empty( $setting['type'] ) ? $setting['type'] : 'text';

    if ( $setting['type'] !== 'header' ) {
      $data = get_option( $setting['name'], $setting['default'] );
      $setting['value']  = esc_attr( $data );
    }

    if ($setting['type'] === 'image') {
      $upload_field = $setting['name'] . '_upload';
      $name = $setting['name'];

      $setting['value'] = magic_get_option( $setting['name'] );
    }

    $setting['template'] = 'inputs/input-' . $setting['type'] . '.twig';

    if ($setting['type'] === 'dropdown-pages' ) {
      $settings['dropdown_args'] = array(
        'echo' => 0,
        'name' => $setting['name'],
      );
    }

    $settings[$key] = $setting;
  }

  $context['settings'] = $settings;

  Timber::render( 'views/dashboard-fields.twig', $context );
}
