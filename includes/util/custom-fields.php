<?php
/**
 * Handle Custom Fields
 *
 * @package Magic
 * @since 0.0.1
 */

if ( function_exists( 'register_field_group' ) ) {
	if ( ! function_exists( 'magic_create_field_group' ) ) {
		/**
		 * Create Field Group is a wrapper around magic_register_field_group which wraps register_field_group.
		 *
		 * @since 0.0.1
		 *
		 * @param array $atts array of settings with additional fields, menu_order.
		 *
		 * @return array of Field Groups.
		 */
		function magic_create_field_group( $atts ) {
			$default_fields = array(
				'text'             => array(
					'type'          => 'text',
					'default_value' => '',
					'placeholder'   => '',
					'prepend'       => '',
					'append'        => '',
					'formatting'    => 'html',
					'maxlength'     => '255',
				),
				'wysiwyg'          => array(
					'type'          => 'wysiwyg',
					'default_value' => '',
					'toolbar'       => 'full',
					'media_upload'  => 'yes',
				),
				'tab'              => array(
					'type' => 'tab',
				),
				'email'            => array(
					'type'          => 'email',
					'default_value' => '',
					'placeholder'   => '',
					'prepend'       => '',
					'append'        => '',
				),
				'select'           => array(
					'type'       => 'select',
					'choices'    => array(),
					'allow_null' => 0,
					'multiple'   => 0,
				),
				'date_time_picker' => array(
					'type'              => 'date_time_picker',
					'required'          => 1,
					'show_date'         => 'true',
					'date_format'       => 'm/d/y',
					'time_format'       => 'h:mm',
					'show_week_number'  => 'false',
					'picker'            => 'slider',
					'save_as_timestamp' => 'true',
					'get_as_timestamp'  => 'false',
				),
				'user'             => array(
					'type'       => 'user',
					'required'   => 0,
					'role'       => array(
						0 => 'all',
					),
					'field_type' => 'select',
					'allow_null' => 1,
				),
				'true_false'       => array(
					'type'          => 'true_false',
					'message'       => 'Load user images using gravatar',
					'default_value' => 0,
				),
				'message'          => [
					'type' => 'message',
				],
			);

			$fields = [];
			foreach ( $fields as $key => $field ) {
				if ( empty( $field['type'] ) ) {
					$field['type'] = 'text';
				}

				$default_field = $default_fields[ $field['type'] ];
				$field['name'] = $key;
				$field['key']  = $atts['slug'] . '_' . $key;
				$fields[]      = array_merge( $default_field, $field );
			}

			$menu_order = isset( $atts['menu_order'] ) ? $atts['menu_order'] : 0;

			$field_group = array(
				'id'         => $atts['id'],
				'title'      => $atts['title'],
				'fields'     => $fields,
				'location'   => $atts['location'],
				'options'    => $atts['options'],
				'menu_order' => $menu_order,
			);

			return $field_group;
		}
	}

	if ( ! function_exists( 'magic_register_field_group' ) ) {
		/**
		 * Wrap register_field_group
		 *
		 * @since 0.0.1
		 *
		 * @param array $atts passed to register_field_group.
		 */
		function magic_register_field_group( array $atts ) {
			$field_group = magic_create_field_group( $atts );

			register_field_group( $field_group );
		}
	}
}
