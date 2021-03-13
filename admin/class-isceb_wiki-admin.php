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



	function post_first()
	{
		// var_dump($_POST);
		function handle_wiki_form_attachment($file_handler, $post_id)
		{
			// check to make sure its a successful upload
			if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

			// require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			// require_once(ABSPATH . "wp-admin" . '/includes/media.php');

			$attach_id = media_handle_upload($file_handler, $post_id);

			return $attach_id;
		}

		/**
		 * Do not forget to check your nonce for security!
		 *
		 * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
		 */

		// var_dump($_POST);

		// $files = array_filter($_FILES['wiki_file']['name']);
		// var_dump($files);
		// $total = count($files);
		// var_dump($total);
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
								$attachment_id = handle_wiki_form_attachment($file, 0);

								// check if upload was succesfull
								if (is_wp_error($attachment_id)) {
									wp_redirect(site_url() . 'oops-something-went-wrong');
									echo ('no upload');
								} else {
									//upload succesfull
									$post_data = array();
									$post_id = null;
									$post_data = array(
										'post_title' => substr($_POST["fileName_{$i}"], 0, 50),
										'post_status' => 'draft',
										'post_type' => 'wiki-file'
									);

									$post_id = wp_insert_post($post_data);

									var_dump($_POST);
									wp_set_object_terms($post_id, $_POST["file_category_{$i}"], 'wiki_file_category');

									update_field('course', $_POST["file_course_{$i}"], $post_id);
									update_post_meta($post_id, 'file_attachment', $attachment_id);

									// If upload was succesful 
									wp_redirect(site_url() . '/thank-you/');
								}
							}

							// Returns error if nonce is not set or invalid
							else {
								wp_send_json_error();
								die();
							}
						}
						++$i;
					}
				} else {
					//errorS
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
		}
	}

	public function create_menu()
	{

		/*
		* Create a submenu page under Plugins.
		* Framework also add "Settings" to your plugin in plugins list.
		*/
		$config_submenu = array(

			'type'              => 'menu',                          // Required, menu or metabox
			'id'                => $this->plugin_name . '-test',    // Required, meta box id, unique per page, to save: get_option( id )
			'parent'            => 'plugins.php',                   // Required, sub page to your options page
			// 'parent'            => 'edit.php?post_type=your_post_type',
			'submenu'           => true,                            // Required for submenu
			'title'             => esc_html__('ISCEB Wiki Homepage setup', 'plugin-name'),    //The name of this page
			'capability'        => 'manage_options',                // The capability needed to view the page
			'plugin_basename'   => plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php'),
			// 'tabbed'            => false,

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
	public function save_isceb_wiki_settings($valid, $unique) {
		
		$page_id = $valid['en']['wiki_home_1']; 
		$page_data = get_post( $page_id );
	 
		// post not there
		if( ! is_object($page_data) ) { 
			return;
		}
	 
		add_rewrite_rule(
			 '^wiki$',
			"index.php?page_id={$page_id}",
			'top'
		);

		flush_rewrite_rules();
		
		// error_log(print_r($valid, TRUE));
		// error_log(print_r($unique, TRUE));

	}
}
