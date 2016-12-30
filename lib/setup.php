<?php

namespace MWD\ACF\ComplexTitles;

class Setup {


	public static function create_titles() {
		if( function_exists('acf_add_local_field_group') ):

		/**
		 * Variable holding the ACF fields definition to be automatically created
		 */
		$args = array (
				'key' => 'group_567332edc8e48',
				'title' => 'Complex Titles',
				'fields' => array (
						array (
								'key' => 'field_5673328771366',
								'label' => 'Preview Placeholder',
								'name' => '',
								'type' => 'message',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array (
										'width' => '',
										'class' => '',
										'id' => '',
								),
								'message' => '',
								'esc_html' => 0,
								'new_lines' => 'wpautop',
						),
						array (
								'key' => 'field_5673330824dd5',
								'label' => 'Build Title',
								'name' => 'build_title',
								'type' => 'repeater',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array (
										'width' => '',
										'class' => '',
										'id' => '',
								),
								'collapsed' => '',
								'min' => '',
								'max' => '',
								'layout' => 'block',
								'button_label' => 'Add Group',
								'sub_fields' => array (
										array (
												'key' => 'field_56f07d9857697',
												'label' => 'Title',
												'name' => '',
												'type' => 'tab',
												'instructions' => '',
												'required' => 0,
												'conditional_logic' => 0,
												'wrapper' => array (
														'width' => '',
														'class' => '',
														'id' => '',
												),
												'placement' => 'top',
												'endpoint' => 0,
										),
										array (
												'key' => 'field_56f05bf5f9da8',
												'label' => 'Title Group',
												'name' => 'title',
												'type' => 'repeater',
												'instructions' => '',
												'required' => 0,
												'conditional_logic' => 0,
												'wrapper' => array (
														'width' => '',
														'class' => '',
														'id' => '',
												),
												'collapsed' => '',
												'min' => '',
												'max' => '',
												'layout' => 'table',
												'button_label' => 'Add Word',
												'sub_fields' => array (),
										),
										array (
												'key' => 'field_56f07daf57698',
												'label' => 'Layout',
												'name' => '',
												'type' => 'tab',
												'instructions' => '',
												'required' => 0,
												'conditional_logic' => 0,
												'wrapper' => array (
														'width' => '',
														'class' => '',
														'id' => '',
												),
												'placement' => 'top',
												'endpoint' => 0,
										),
								),
						),
				),
			'location' => array (),
				'menu_order' => 0,
				'position' => 'acf_after_title',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
		);


		/**
		* Check for declared post types to attach fields to
		*
		* @author Michael W. Delaney
		* @since 1.0
		*
		* Default: post_type == page
		*
		* Declare theme support for specific post types:
		* $landing_page_templates = array(
		* 	array (
		* 		array (
		* 			'param' => 'post_type',
		* 			'operator' => '==',
		* 			'value' => 'page',
		* 		),
		* 		array (
		* 			'param' => 'page_template',
		* 			'operator' => '!=',
		* 			'value' => 'template-no-header-image.php',
		* 		),
		*	),
		* );
		* add_theme_support( 'complex-titles-location', $landing_page_templates );
		*/

		//Check if theme support is explicitly defined. If so, enable all attachments declared in theme support.
		if( current_theme_supports( 'complex-titles-location' ) ) {
				$locations_supported    = get_theme_support( 'complex-titles-location' );
				$locations_enabled      = $locations_supported[0];

		} else {
				// If theme support is not explicitly defined, enable default attachments.
				$locations_enabled = array(
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'page',
						)
					)
				);
		}
		// Insert each location into the $args array
		$args['location'] = $locations_enabled;


		/**
		* Include all enabled layout fields
		*
		* @author Michael W. Delaney
		* @since 1.0
		*
		* Declare theme support for specific layout fields. Default is to include all layout fields:
		*   add_theme_support( 'complex-titles-layout', array( 'alignment' ) );
		*
		* Pass an empty array to disable alignment fields altogether:
		*   add_theme_support( 'complex-titles-layout', array() );
		*/

		//Check if theme support is explicitly defined. If so, only enable layouts declared in theme support.
		if( current_theme_supports( 'complex-titles-layout' ) ) {
				$layout_fields_supported = get_theme_support( 'complex-titles-layout' );
				$layout_fields_enabled = $layout_fields_supported[0];
		} else {
				// If theme support is not explicitly defined, enable all fields as a fallback.
				$layout_fields_enabled = array();
				foreach(get_class_methods('\MWD\ACF\ComplexTitles\Layout') as $layout_field) {
						if($layout_field != '__construct') {
								$layout_fields_enabled[] = $layout_field;
						}
				}
		}
		// Enable each layout field
		// If no layout fields are enabled, remove the tabs to avoid clutter
		if( count( $layout_fields_enabled ) <= 0 ) {
				unset($args['fields'][1]['sub_fields'][2]);
				unset($args['fields'][1]['sub_fields'][0]);
		} else {

				// Enable each layout field
				$CTLayouts      = new \MWD\ACF\ComplexTitles\Layout();
				$layout_fields_array  = array();
				foreach ($layout_fields_enabled as $layout_field) {
						$layout_fields_array[]    = $CTLayouts->$layout_field();
				}
				// Sort layout fields by the 'order' element
				usort($layout_fields_array, function ($a, $b) {
						if ($a['order'] == $b['order']) return 0;
						return $a['order'] < $b['order'] ? -1 : 1;
				});
				// Insert each layout field into the $args array
				foreach ( $layout_fields_array as $layout_field) {
						$args['fields'][1]['sub_fields'][] = $layout_field['field'];
				}
		}



		/**
		*
		* Include all enabled fields
		*
		* @author Michael W. Delaney
		* @since 1.0
		*
		* Declare theme support for specific fields. Default is to include all fields:
		*   add_theme_support( 'complex-titles-fields', array( 'emphasize', 'alignment' ) );
		*
		*/

		//Check if theme support is explicitly defined. If so, only enable fields declared in theme support.
		if( current_theme_supports( 'complex-titles-fields' ) ) {
				$fields_supported = get_theme_support( 'complex-titles-fields' );
				$fields_enabled = $fields_supported[0];
		} else {
				// If theme support is not explicitly defined, enable all fields as a fallback.
				$fields_enabled = array();
				foreach(get_class_methods('\MWD\ACF\ComplexTitles\Fields') as $field) {
						if($field != '__construct') {
								$fields_enabled[] = $field;
						}
				}
		}

		// Enable each layout field
		$CTFields      = new Fields();
		$fields_array  = array();
		foreach ($fields_enabled as $field) {
				$fields_array[]    = $CTFields->$field();
		}
		// Sort layout fields by the 'order' element
		usort($fields_array, function ($a, $b) {
				if ($a['order'] == $b['order']) return 0;
				return $a['order'] < $b['order'] ? -1 : 1;
		});
		// Insert each layout field into the $args array
		foreach ( $fields_array as $field) {
				$args['fields'][1]['sub_fields'][1]['sub_fields'][] = $field['field'];
		}



		/**
		* Add the local field group, with its layouts, to ACF
		*
		* @author Michael W. Delaney
		* @since 1.0
		*
		*/

		acf_add_local_field_group($args);

		endif;
	}

}