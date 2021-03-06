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
		/**
		 * Do not forget to check your nonce for security!
		 *
		 * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
		 */

		// var_dump($_POST);

		$files = array_filter($_FILES['wiki_file']['name']);
		var_dump($files);
		$total = count($files);
		var_dump($total);

		for ($i=0; $i < $total; $i++) {
			 $post_data = array();
			 $post_id = null;
			 $post_data = array(
				'post_title' => $_POST['fileName'],
				'post_status' => 'draft',
				'post_type' => 'wiki-file'
			);
			$post_id = wp_insert_post($post_data);

			wp_set_object_terms($post_id, $_POST['file_categories_'+$i], 'wiki_file_category');

				
		}

       
		if (
			isset($_POST['my_nonce_field'])
			&& wp_verify_nonce($_POST['my_nonce_field'], 'submit_content')

		) {

			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$attachment_id = media_handle_upload('wiki_file', 0);

			if (is_wp_error($attachment_id)) {
				var_dump($attachment_id);
				echo ('no upload');
				var_dump($_FILES);
				if(isset($_POST['wiki_file'])){
					echo('i am set');
				}
			}
		}
		//TODO: replace with right page to respond
		else {
			wp_send_json_error();
			die();
		}

		update_post_meta($post_id, 'file_attachment', $attachment_id );


		// wp_redirect(site_url() . '/thank-you/');

		die();
	}
}
