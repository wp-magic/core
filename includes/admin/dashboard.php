<?php
/**
 * Add admin Dashboard index Page
 * All other Magic Plugins will use this as their parent.
 *
 * @package Magic
 * @since 0.0.1
 */

add_action(
	'admin_menu',
	function () {
		magic_dashboard_add_menu_page(
			[
				'title' => 'Magic',
			]
		);

		$title = 'Magic Dashboard Settings';

		$settings = array(
			[
				'type'  => 'header',
				'name'  => 'header_single_page',
				'label' => 'Error Messages',
			],
			[
				'name'    => 'nonce',
				'type'    => 'text',
				'default' => 'Security Error. Please retry later.',
				'label'   => 'Nonce Error',
			],
			[
				'name'    => 'signon',
				'type'    => 'text',
				'default' => 'Login Error. Please try again later.',
				'label'   => 'Login Error',
			],
			[
				'name'    => 'password_mismatch',
				'type'    => 'text',
				'default' => 'The passwords do not match.',
				'label'   => 'Password Mismatch Error',
			],
			[
				'name'    => 'insert',
				'type'    => 'text',
				'default' => 'Item could not be added.',
				'label'   => 'Creation Error',
			],
		);

		$submenu_config = [
			'link'     => 'Error Messages',
			'title'    => $title,
			'slug'     => MAGIC_DASHBOARD_SLUG . '_error_messages',
			'settings' => $settings,
			'is_array' => true,
		];

		magic_dashboard_add_submenu_page( $submenu_config );
	},
	1
);
