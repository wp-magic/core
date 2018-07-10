<?php

require_once 'util/options.php';
require_once 'util/cookies.php';
require_once 'util/slugify.php';
require_once 'util/styles.php';

require_once 'util/request-handler.php';
require_once 'util/custom-fields.php';
require_once 'util/login-or-create-user.php';
require_once 'util/page-templates.php';

require_once plugin_dir_path( __FILE__ ) . 'util/dashboard.php';

if ( is_admin() ) {
  require_once plugin_dir_path( __FILE__ ) . 'admin/dashboard.php';
  require_once plugin_dir_path( __FILE__ ) . 'admin/plugin-activation.php';
}


add_action( 'init', function() {
  $results = scandir(WP_PLUGIN_DIR);

  $locations = [];
  foreach ($results as $result) {
    if ($result === '.' or $result === '..') continue;
  	// print( $result . ' <br> ' . strpos( $result, 'magic' ) === 0 );
  	if (strpos( $result, 'magic' ) === 0 ) {
  		$views = WP_PLUGIN_DIR . '/' . $result . '/includes/partials';
      if ( is_dir( $views ) ) {
  			$locations[] = $views;
  		}
  	}
  }

  Timber::$locations = $locations;
}, PHP_INT_MAX );
