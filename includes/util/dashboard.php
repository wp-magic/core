<?php
/**
 * Magic Dashboard helper utilities
 *
 * @package Magic
 * @since 0.0.1
 */

/**
 * Add a page to the magic admin menu
 *
 * @since 0.0.1
 *
 * @param array $atts passed to add_menu_page.
 */
function magic_dashboard_add_menu_page( array $atts = [] ) {
	$default = array(
		'title'      => 'Magic',
		'slug'       => MAGIC_DASHBOARD_SLUG,
		'capability' => 'manage_options',
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
}

/**
 * Add a submenu page to the magic admin menu
 *
 * @since 0.0.1
 *
 * @param array $atts passed to add_submenu_page.
 */
function magic_dashboard_add_submenu_page( array $atts = [] ) {
	$default = array(
		'capability' => 'edit_posts',
		'link'       => 'Magic Admin Panel',
		'title'      => 'Magic Admin Panel',
		'slug'       => 'magic_admin_panel',
		'settings'   => [],
		'parent'     => MAGIC_DASHBOARD_SLUG,
		'action'     => MAGIC_DASHBOARD_SLUG,
		'is_array'   => false,
	);

	$atts = shortcode_atts( $default, $atts );

	add_submenu_page(
		$atts['parent'],
		$atts['link'],
		$atts['link'],
		$atts['capability'],
		$atts['slug'],
		function() use ( $atts ) {
			magic_dashboard_render_admin_page( $atts );
		}
	);
}


/**
 * Render a submenu page using timber and twig
 *
 * @since 0.0.1
 *
 * @param array $atts passed to add_submenu_page.
 */
function magic_dashboard_render_admin_page( $atts ) {
	$context = Timber::get_context();
	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		$context['settings'] = magic_dashboard_set_options( $atts );
	} else {
		$context['settings'] = magic_dashboard_get_options( $atts );
	}

	$context['settings']['nonce'] = array(
		'name'  => 'nonce',
		'type'  => 'hidden',
		'value' => wp_create_nonce( $atts['action'] ),
	);

	$context['title'] = $atts['title'];
	$context['slug']  = $atts['slug'];

	Timber::render( 'views/dashboard-subpage.twig', $context );
}

/**
 * Get an Option name
 *
 * @since 0.0.1
 *
 * @param string $slug prepended to returned string.
 * @param string $name appended to returned string.
 *
 * @return string $option_name $slug and $name, joined by _.
 */
function magic_dashboard_get_option_name( string $slug, string $name ) {
	$option_name = $slug . '_' . $name;
	return $option_name;
}

/**
 * Get Magic Options prior to rendering the dashboard
 *
 * @since 0.0.1
 *
 * @param array $atts contains settings and slug.
 */
function magic_dashboard_get_options( array $atts ) {
	$options = array();

	foreach ( $atts['settings'] as $setting ) {
		$name        = $setting['name'];
		$option_name = magic_dashboard_get_option_name( $atts['slug'], $name );
		if ( 'header' !== $setting['type'] ) {
			$setting['value'] = magic_get_option( $option_name, ! empty( $setting['default'] ) ? $setting['default'] : 0 );
		}

		$options[ $name ] = $setting;
	}

	return $options;
}

/**
 * Update Magic Options
 *
 * @param array $atts includes settings and slug fields.
 */
function magic_dashboard_set_options( array $atts ) {
	$options = [];

	$post = magic_verify_nonce( $atts['action'], false );

	foreach ( $atts['settings'] as $setting ) {
		$name        = $setting['name'];
		$option_name = magic_dashboard_get_option_name( $atts['slug'], $name );

		if ( 'header' !== $setting['type'] && 'nonce' !== $name ) {
			if ( ! empty( $post[ $name ] ) ) {
				$setting['value'] = sanitize_text_field( wp_unslash( $post[ $name ] ) );

				magic_set_option( $option_name, $setting['value'] );
			}
		}

		$options[ $name ] = $setting;
	}

	return $options;
}

/**
 * Renders magic setting fields in menu pages
 *
 * @since 0.0.1
 *
 * @param string $slug of this setting field group.
 * @param array  $settings fields in this setting field group.
 */
function magic_dashboard_render_settings_fields( string $slug, array $settings = [] ) {
	foreach ( $settings as $key => $setting ) {
		$default = array(
			'default' => '',
			'name'    => '',
			'type'    => '',
		);

		$setting = array_merge( $default, $setting );

		$setting['type'] = ! empty( $setting['type'] ) ? $setting['type'] : 'text';

		if ( 'image' === $setting['type'] ) {
			$option_name      = magic_dashboard_get_option_name( $slug, $setting['name'] );
			$setting['value'] = magic_get_option( $option_name, $setting['default'] );
		}

		$setting['template'] = 'inputs/input-' . $setting['type'] . '.twig';

		if ( 'dropdown-pages' === $setting['type'] ) {
			$settings['dropdown_args'] = array(
				'echo' => 0,
				'name' => $setting['name'],
			);
		}

		$settings[ $key ] = $setting;
	}

	$context['settings'] = $settings;

	Timber::render( 'views/dashboard-fields.twig', $context );
}
