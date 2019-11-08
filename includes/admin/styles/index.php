<?php
/**
 * Add custom magic css styles to admin pages
 *
 * @package Magic
 * @since 0.0.1
 */

add_action(
	'admin_head',
	function() {
		echo '<style>\n  .magic-input {\n     width: 100%;\n  }\n</style>';
	}
);
