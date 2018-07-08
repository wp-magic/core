<?php
/**
 * Magic Dashboard
 *
 * @package   Magic-0
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Magic 0
 * Plugin URI:  https://github.com/wp-magic/plugin-0
 * Description: Admin dashboard and various helpers for plugins. Required by most other magic plugins.
 * Version:     0.0.1
 * Author:      Jascha Ehrenreich
 * Author URI:  https://github.com/wp-magic
 * Text Domain: magic-user-manage
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
 die;
}

define( 'MAGIC_DASHBOARD_SLUG', 'magic_dashboard' );
define( 'MAGIC_DASHBOARD_COOKIE_SEP', '|||');

require_once plugin_dir_path( __FILE__ ) . 'includes/plugin.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/util/dashboard.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/admin/dashboard.php';

if ( is_admin() ) {
  require_once plugin_dir_path( __FILE__ ) . 'includes/admin/plugin-activation.php';
}

register_activation_hook( __FILE__, function () {
  flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
  flush_rewrite_rules();
} );
