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

	$context['title'] = $atts['title'];
	$context['slug']  = $atts['slug'];

	Timber::render( 'views/dashboard-subpage.twig', $context );
}

/**
 * Get an Option name
 *
 * @since 0.0.1
 *
 * @param string $slug prepended
 * @param string $name appended
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
function magic_dashboard_get_options( $atts ) {
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
 */
function magic_dashboard_set_options( $atts ) {
	$options = [];

	foreach ( $atts['settings'] as $setting ) {
		$name        = $setting['name'];
		$option_name = magic_dashboard_get_option_name( $atts['slug'], $name );

		if ( 'header' !== $setting['type'] ) {
			$setting['value'] = ! empty( $_POST[ $name ] )
			? sanitize_text_field( wp_unslash( $_POST[ $name ] ) )
			: $setting['default'];

			magic_set_option( $option_name, $setting['value'] );
		}

		$options[ $name ] = $setting;
	}

	return $options;
}

function magic_dashboard_render_settings_fields( string $slug, array $settings = [] ) {
	foreach ( $settings as $key => $setting ) {
		$default = array(
			'default' => '',
			'name'    => '',
			'type'    => '',
		);

		$setting = array_merge( $default, $setting );

		$setting['type'] = ! empty( $setting['type'] ) ? $setting['type'] : 'text';

		if ( $setting['type'] === 'image' ) {
			$option_name      = magic_dashboard_get_option_name( $slug, $setting['name'] );
			$setting['value'] = magic_get_option( $option_name, $setting['default'] );
		}

		$setting['template'] = 'inputs/input-' . $setting['type'] . '.twig';

		if ( $setting['type'] === 'dropdown-pages' ) {
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
