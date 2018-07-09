<?php

require_once 'util/options.php';
require_once 'util/cookies.php';
require_once 'util/slugify.php';
require_once 'util/styles.php';

require_once 'util/request-handler.php';
require_once 'util/custom-fields.php';
require_once 'util/login-or-create-user.php';

require_once plugin_dir_path( __FILE__ ) . 'util/dashboard.php';

if ( is_admin() ) {
  require_once plugin_dir_path( __FILE__ ) . 'admin/dashboard.php';
  require_once plugin_dir_path( __FILE__ ) . 'admin/plugin-activation.php';
}
