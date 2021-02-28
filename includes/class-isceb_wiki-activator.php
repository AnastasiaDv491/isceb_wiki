<?php

/**
 * Fired during plugin activation
 *
 * @link       isceb.be
 * @since      1.0.0
 *
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/includes
 * @author     Anastasia Dvoryanchikova <anadvoryanchikova@gmail.com>
 */
class Isceb_wiki_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{



		/**
		 * Custom Post Types
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-isceb_wiki-post_types.php';
		$plugin_post_types = new Isceb_Wiki_Post_Types();


		// Isceb_wiki_Activator::define_ACF_FIELDS();
	}

	public static function define_ACF_FIELDS (){
		if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array(
				'key' => 'group_603293235bfdb',
				'title' => 'Add file field',
				'fields' => array(
					array(
						'key' => 'field_603293bf1718f',
						'label' => 'File Attachment',
						'name' => 'file_attachment',
						'type' => 'file',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'array',
						'library' => 'all',
						'min_size' => '',
						'max_size' => '',
						'mime_types' => '.docx, .pdf',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'wiki-file',
						),
					),
				),
				'menu_order' => -500,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => array(
					0 => 'format',
					1 => 'page_attributes',
					2 => 'featured_image',
					3 => 'send-trackbacks',
				),
				'active' => true,
				'description' => '',
			));
			
			acf_add_local_field_group(array(
				'key' => 'group_6037eea0d51a6',
				'title' => 'Add wiki file category',
				'fields' => array(
					array(
						'key' => 'field_6037eeaaacc86',
						'label' => 'wiki_file_category',
						'name' => 'wiki_file_category',
						'type' => 'select',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'Exam' => 'Exam',
							'Summary' => 'Summary',
							'Exercise' => 'Exercise',
						),
						'default_value' => false,
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'wiki-file',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			acf_add_local_field_group(array(
				'key' => 'group_60314a15e04f7',
				'title' => 'Approved',
				'fields' => array(
					array(
						'key' => 'field_60314a218d3db',
						'label' => 'Approved',
						'name' => 'approved',
						'type' => 'checkbox',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'Yes' => 'Yes',
							'No' => 'No',
						),
						'allow_custom' => 0,
						'default_value' => array(
							0 => 'No',
						),
						'layout' => 'vertical',
						'toggle' => 0,
						'return_format' => 'value',
						'save_custom' => 0,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'wiki-file',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			acf_add_local_field_group(array(
				'key' => 'group_60368d2a41756',
				'title' => 'Course selection for Wiki-file',
				'fields' => array(
					array(
						'key' => 'field_60368d3268a6a',
						'label' => 'Course',
						'name' => 'course',
						'type' => 'relationship',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'course',
						),
						'taxonomy' => '',
						'filters' => array(
							0 => 'search',
							1 => 'post_type',
							2 => 'taxonomy',
						),
						'elements' => '',
						'min' => '',
						'max' => '',
						'return_format' => 'object',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'wiki-file',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			acf_add_local_field_group(array(
				'key' => 'group_603138f164da6',
				'title' => 'Phase selection for course',
				'fields' => array(
					array(
						'key' => 'field_603138f46fc85',
						'label' => 'Phases',
						'name' => 'phases',
						'type' => 'relationship',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'phase',
						),
						'taxonomy' => '',
						'filters' => array(
							0 => 'search',
							1 => 'post_type',
							2 => 'taxonomy',
						),
						'elements' => '',
						'min' => '',
						'max' => '',
						'return_format' => 'object',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'course',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			acf_add_local_field_group(array(
				'key' => 'group_60393d8f0fc9f',
				'title' => 'Program select for phase',
				'fields' => array(
					array(
						'key' => 'field_60393d9b990cf',
						'label' => 'Program',
						'name' => 'program',
						'type' => 'relationship',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'program',
						),
						'taxonomy' => '',
						'filters' => array(
							0 => 'search',
							1 => 'post_type',
							2 => 'taxonomy',
						),
						'elements' => '',
						'min' => '',
						'max' => '',
						'return_format' => 'object',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'phase',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			endif;
	}
}
