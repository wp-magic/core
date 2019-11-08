<?php
/**
 * Main Magic Entry Point
 *
 * @package Magic
 * @since 0.0.1
 */

/**
 * Require admin files
 */
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/index.php';
}

/**
 * General utility functionality
 */
require_once plugin_dir_path( __FILE__ ) . 'util/options.php';
require_once plugin_dir_path( __FILE__ ) . 'util/cookies.php';
require_once plugin_dir_path( __FILE__ ) . 'util/slugify.php';
require_once plugin_dir_path( __FILE__ ) . 'util/styles.php';

require_once plugin_dir_path( __FILE__ ) . 'util/request-handler.php';
require_once plugin_dir_path( __FILE__ ) . 'util/custom-fields.php';
require_once plugin_dir_path( __FILE__ ) . 'util/magic-date-object.php';
require_once plugin_dir_path( __FILE__ ) . 'util/login-or-create-user.php';
require_once plugin_dir_path( __FILE__ ) . 'util/page-templates.php';

require_once plugin_dir_path( __FILE__ ) . 'util/dashboard.php';

/**
 * Main action hook for Magic.
 * Does nothing if Timber is not installed.
 *
 * @since 0.0.1
 */
function magic_initialize() {
	if ( ! class_exists( 'Timber' ) ) {
		return;
	}

	/**
	 * Find partial locations throughout plugin dir
	 */

	$results = scandir( WP_PLUGIN_DIR );

	$locations = [];
	foreach ( $results as $result ) {
		if ( '.' === $result || '..' === $result ) {
			continue;
		}

		if ( strpos( $result, 'magic' ) === 0 ) {
			$views = WP_PLUGIN_DIR . '/' . $result . '/includes/partials';
			if ( is_dir( $views ) ) {
				$locations[] = $views;
			}
		}
	}

	Timber::$locations = $locations;
}

add_action( 'init', 'magic_initialize', PHP_INT_MAX );
