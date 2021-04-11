<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       isceb.be
 * @since      1.0.0
 *
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/admin
 * @author     Anastasia Dvoryanchikova <anadvoryanchikova@gmail.com>
 */
class Isceb_wiki_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Isceb_wiki_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Isceb_wiki_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/isceb_wiki-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Isceb_wiki_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Isceb_wiki_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/isceb_wiki-admin.js', array('jquery'), $this->version, false);
	}

	//Custom directory for the wiki files upload
	static function isceb_wiki_custom_upload_dir($dir_data)
	{
		// $dir_data already you might want to use
		$custom_dir = 'wiki';
		return [
			'path' => $dir_data['basedir'] . '/' . $custom_dir,
			'url' => $dir_data['url'] . '/' . $custom_dir,
			'subdir' => '/' . $custom_dir,
			'basedir' => $dir_data['error'],
			'error' => $dir_data['error'],
		];
	}

	//Needed becaue media_handle_upload can only process one file at a time
	function handle_wiki_form_attachment($file_handler, $post_id)
	{
		// check to make sure its a successful upload
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

		// require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		// require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		// changing the directory
		add_filter('upload_dir', array($this, 'isceb_wiki_custom_upload_dir'));

		//Get term to put on wiki file, is a workaround to filter the media library
		$term = get_term_by('name', 'wiki_file_attachement_tag', 'wiki_file_tags');
		error_log(print_r($term, true));
		$post_data = array(
			// 'post_author' => 1,
			'tax_input'     => array(
				'wiki_file_tags' => array($term->term_id)
			)
		);

		$attach_id = media_handle_upload($file_handler, $post_id, $post_data);
		remove_filter('upload_dir', array($this, 'isceb_wiki_custom_upload_dir'));


		return $attach_id;
	}

	function post_first()
	{
		$i = 0;
		if ($_FILES) {
			$files = $_FILES["wiki_file"];
			foreach ($files['name'] as $key => $value) {
				//Check filetype
				$allowed = array('pdf');
				$filename = $files['name'][$key];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				if (in_array($ext, $allowed)) {
					if ($files['name'][$key]) {
						$file = array(
							'name' => $files['name'][$key],
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'error' => $files['error'][$key],
							'size' => $files['size'][$key]
						);
						$_FILES = array("wiki_file" => $file);


						foreach ($_FILES as $file => $array) {

							if (
								isset($_POST['my_nonce_field'])
								&& wp_verify_nonce($_POST['my_nonce_field'], 'submit_content')

							) {

								$attachment_id = $this->handle_wiki_form_attachment($file, 0);

								// check if upload was succesfull
								if (is_wp_error($attachment_id)) {
									wp_redirect(site_url() . remove_query_arg('message', $_POST['_wp_http_referer']) . '?message=failed');
								} else {
									//upload succesfull
									$post_data = array();
									$post_id = null;
									$post_data = array(
										'post_title' => substr($_POST["fileName_{$i}"], 0, 50),
										'post_status' => 'publish',
										'post_type' => 'wiki-file',
										'post_author' => $_POST["userID"]
									);

									$post_id = wp_insert_post($post_data);
									if ($post_id != 0) {
										error_log(print_r($_POST));
										$terms_return = wp_set_object_terms($post_id, $_POST["file_category_{$i}"], 'wiki_file_category');
										if (!is_wp_error($terms_return)) {
											update_field('course', $_POST["file_course_{$i}"], $post_id);
											update_field('academic_year', $_POST["file_academic_year_{$i}"], $post_id);
											$meta_return = update_post_meta($post_id, 'file_attachment', $attachment_id);
											if ($meta_return) {
												// If upload was succesful 
												wp_redirect(site_url() . remove_query_arg('message', $_POST['_wp_http_referer']) . '?message=success');
											} else {
												wp_redirect(site_url() . remove_query_arg('message', $_POST['_wp_http_referer']) . '?message=failed');
											}
										} else {
											wp_redirect(site_url() . remove_query_arg('message', $_POST['_wp_http_referer']) . '?message=failed');
										}
									} else {
										wp_redirect(site_url() . remove_query_arg('message', $_POST['_wp_http_referer']) . '?message=failed');
									}
								}
							}

							// Returns error if nonce is not set or invalid
							else {
								wp_redirect(site_url() . remove_query_arg('message', $_POST['_wp_http_referer']) . '?message=failed');
								die();
							}
						}
						++$i;
					}
				} else {
					wp_redirect(site_url() . remove_query_arg('message', $_POST['_wp_http_referer']) . '?message=failed');
				}
			}
		}
		//There are no files
		die();
	}

	function custom_wiki_file_column($column, $post_id)
	{
		switch ($column) {
			case 'course':
				$terms = get_field('course', $post_id);
				$output = "";

				if ($terms) {
					$count = count($terms);
					$i = 1;

					foreach ($terms as $value) {
						$output = $output . $value->post_title;

						if ($i < $count) {
							$output = $output . ", ";
						}
						$i++;
					}

					echo esc_html($output);
				} else {
					_e('Unable to get course', 'isceb_wiki');
				}
			break;

			case 'approved':
				$approved = get_field('approved');
				
				if ($approved != 'Yes') {
					echo ('<p style="background-color: #8B0000; color: white; position: center,"> Not approved </p>');
				} else {
					echo ('<p> Approved </p>');
				}
			break;
		}
	}

	public function isceb_wiki_create_options_menu()
	{

		/*
		* Create a submenu page under Plugins.
		* Framework also add "Settings" to your plugin in plugins list.
		*/
		$config_submenu = array(

			'type'              => 'menu',                          // Required, menu or metabox
			'id'                => $this->plugin_name . '-test',    // Required, meta box id, unique per page, to save: get_option( id )
			// 'parent'            => 'plugins.php',                   // Required, sub page to your options page
			'parent'            => 'isceb_wiki_admin_menu',
			'submenu'           => true,                            // Required for submenu
			'title'             => esc_html__('Settings', 'plugin-name'),    //The name of this page
			'capability'        => 'manage_options',                // The capability needed to view the page
			'plugin_basename'   => plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php'),
			// 'tabbed'            => false,
			'menu_title'		=> 'ISCEB WIKI',
			// 'menu_slug'			=> 'isceb_wiki_menu',
			'position'			=> 	25
		);

		$fields[] = array(
			'name'   => 'first',
			'title'  => 'First',
			'icon'   => 'dashicons-admin-generic',
			'fields' => array(

				array(
					'id' => 'wiki_home_1',
					'type' => 'select',
					'title' => 'Wiki Homepage',
					'query'          => array(
						'type'           => 'pages',
						'args'           => array(
							'orderby'      => 'post_date',
							'order'        => 'DESC',
						),
					),
					'default_option' => '',
					'class'       => 'chosen',
				),
				array(
					'id' => 'isceb_wiki_login_page',
					'type' => 'select',
					'title' => 'ISCEB wiki Login',
					'query'          => array(
						'type'           => 'pages',
						'args'           => array(
							'orderby'      => 'post_date',
							'order'        => 'DESC',
						),
					),
					'default_option' => '',
					'class'       => 'chosen',
				),
				array(
					'id' => 'isceb_wiki_file_upload_went_wrong_text',
					'type' => 'text',
					'title' => 'Wiki file upload went wrong message',
					'attributes' => array(
						'placeholder' => 'Error message for upload file',

					),
				),
				array(
					'id' => 'isceb_wiki_file_upload_ok_text',
					'type' => 'text',
					'title' => 'Wiki file upload success message',
					'attributes' => array(
						'placeholder' => 'Success message for upload file',

					),
				),
			),
		);

		$options_panel = new Exopite_Simple_Options_Framework($config_submenu, $fields);
	}


	// Called when Wiki settings are changed
	// $valid - 
	// (
	// [en] => Array
	// (
	// 	[wiki_home_1] => 2
	// )

	// )
	// Unique - ID of config submenu
	public function save_isceb_wiki_settings($valid, $unique)
	{

		$page_id = $valid['en']['wiki_home_1'];
		$page_data = get_post($page_id);

		// post not there
		if (!is_object($page_data)) {
			return;
		}

		add_rewrite_rule(
			'^wiki$',
			"index.php?page_id={$page_id}",
			'top'
		);

		flush_rewrite_rules();
	}

	public function isceb_wiki_add_admin_menu()
	{
		add_menu_page("ISCEB WIKI", "ISCEB WIKI", 'manage_options', $this->plugin_name . '_admin_menu', array($this, 'page_signups'));
	}



	public function page_signups()
	{
		include(plugin_dir_path(__FILE__) . 'partials/isceb_wiki-main-menu.php');
	}

	public function isceb_wiki_exclude_admin_uploads_media_library($wp_query_obj = array())
	{
		$wp_query_obj['tax_query'] =
			array(
				array(
					'taxonomy' => 'wiki_file_tags',
					'operator' => 'NOT EXISTS'
				)
			);

		return $wp_query_obj;
	}

	//This is a workaround to add a tag to attachements so that we can filter them in the media library
	function isceb_wiki_add_categories_to_attachments()
	{

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name'                       => _x('Wike_file_tags', 'taxonomy general name', 'textdomain'),
			'singular_name'              => _x('Wike_file_tag', 'taxonomy singular name', 'textdomain'),
			'search_items'               => __('Search Wike_file_tags', 'textdomain'),
			'popular_items'              => __('Popular Wike_file_tags', 'textdomain'),
			'all_items'                  => __('All Wike_file_tags', 'textdomain'),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __('Edit Wike_file_tag', 'textdomain'),
			'update_item'                => __('Update Wike_file_tag', 'textdomain'),
			'add_new_item'               => __('Add New Wike_file_tag', 'textdomain'),
			'new_item_name'              => __('New Wike_file_tag Name', 'textdomain'),
			'separate_items_with_commas' => __('Separate Wike_file_tags with commas', 'textdomain'),
			'add_or_remove_items'        => __('Add or remove Wike_file_tags', 'textdomain'),
			'choose_from_most_used'      => __('Choose from the most used Wike_file_tags', 'textdomain'),
			'not_found'                  => __('No Wike_file_tags found.', 'textdomain'),
			'menu_name'                  => __('Wike_file_tags', 'textdomain'),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			//Turn on to show in media menu and on attachemnt post
			'show_ui'               => false,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
		);


		register_taxonomy(
			'wiki_file_tags',
			'attachement',
			$args
		);

		wp_insert_term('wiki_file_attachement_tag', 'wiki_file_tags');

		register_taxonomy_for_object_type('wiki_file_tags', 'attachment');
	}

	function isceb_wiki_download_count()
	{

		$user = get_current_user_id();
		$downloads = get_field('isceb_wiki_user_files', 'user_' . $user);

		$current_count = get_field('download_count', $_REQUEST['isceb_wiki_file']);
		$current_count = is_null($current_count) ? 0 : $current_count;

		if (!in_array($_REQUEST['isceb_wiki_file'], $downloads)) {
			update_field('download_count', $current_count + 1, $_REQUEST['isceb_wiki_file']);
		}

		array_push($downloads, $_REQUEST['isceb_wiki_file']);
		update_field('isceb_wiki_user_files', $downloads, 'user_' . $user);

		$return = array(
			'message'   => 'Saved',
			'ID'        => $_REQUEST['isceb_wiki_file'],
			'current_count' => $current_count,
			'user' => $user,
			'userDownloads' => $downloads,
			'current_post' => $current_post
		);

		wp_send_json_success($return);
	}

	function isceb_wiki_delete_attachment($post_id) {
		if('wiki-file' == get_post_type( $post_id )) {

			$attachment =  get_field('file_attachment', $post_id);

			//TODO: Weird acf behaviour, returns ID instead of array
			//Probably a problem with the test environment
			if (is_string($attachment)) {
				wp_delete_attachment($attachment);
			}
			else{
				wp_delete_attachment($attachment['ID']);
			}
			return;
		}
	}
}
