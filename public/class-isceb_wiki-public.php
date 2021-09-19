<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       isceb.be
 * @since      1.0.0
 *
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/public
 * @author     Anastasia Dvoryanchikova <anadvoryanchikova@gmail.com>
 */
class Isceb_wiki_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/isceb_wiki-public.css', array(), $this->version, 'all');
		wp_enqueue_style('select2style', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/isceb_wiki-public.js', array('jquery'), $this->version, false);
		wp_register_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), $this->version, false);

		wp_register_script('isceb_wiki_files_script', plugin_dir_url(__FILE__) . 'js/isceb_wiki_files.js', array('jquery'), $this->version, false);

		/**
		 *  In backend there is global ajaxurl variable defined by WordPress itself.
		 *
		 * This variable is not created by WP in frontend. It means that if you want to use AJAX calls in frontend, then you have to define such variable by yourself.
		 * Good way to do this is to use wp_localize_script.
		 *
		 * @link http://wordpress.stackexchange.com/a/190299/90212
		 *
		 * You could also pass this datas with the "data" attribute somewhere in your form.
		 */

		wp_localize_script($this->plugin_name, 'wp_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
		));

		wp_localize_script('isceb_wiki_files_script', 'wp_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
		));
	}



	function shortcode_wiki_submit($atts)
	{
		if (is_user_logged_in()) {
			$args = shortcode_atts(
				array(
					'arg1'   => 'arg1',
					'arg2'   => 'arg2',
				),
				$atts
			);

			$var = (strtolower($args['arg1']) != "") ? strtolower($args['arg1']) : 'default';

			ob_start();
			//TODO check if text of page where shortcode is included is shown
			include plugin_dir_path(__FILE__) . 'partials/isceb-wiki-public-form.php';

			return ob_get_clean();
		} else {
			ob_start();
			echo ('You need to be logged in to upload something to the wiki');
			$args = array(
				'redirect' => get_permalink()
			);
			wp_login_form($args);
			return ob_get_clean();
		}
	}

	function shortcode_wiki_programs($atts)
	{
		ob_start();
		include plugin_dir_path(__FILE__) . 'partials/isceb_wiki_programs_shortcode.php';
		return ob_get_clean();
	}

	public function get_wiki_courses_ajax()
	{
		// Query Arguments
		$args = array(
			'post_type' => "course",
			'post_status' => array('publish'),
			'nopaging' => true,
			'order' => 'DESC',
		);

		// The Query
		$ajaxposts = get_posts($args); // changed to get_posts from wp_query, because `get_posts` returns an array

		// echo json_encode( $ajaxposts );

		// exit; // exit ajax call(or it will return useless information to the response)s
		wp_send_json_success($ajaxposts);
	}

	//Only called when initalising
	public function rewrite_wiki_base_url_to_page()
	{
		// delete_option('isceb_wiki-test');


		//get_option returns false by default if option doesn't exist
		$options = get_option('isceb_wiki-test');

		if ($options && ($options['en']['wiki_home_1'] === null || $options['en']['wiki_home_1'] == '')) {
			$page = get_page_by_title('Wiki Homepage');
			if ($page === null) {
				$wiki_homepage = array(
					'ID' => 0,
					'post_type' => 'page',
					'post_name' => 'wiki homepage',
					'post_title' => 'Wiki Homepage',
					'post_status' => 'publish',
				);
				$page_id = wp_insert_post($wiki_homepage);
			} else {
				$page_id = $page->ID;
			}

			add_rewrite_rule(
				'^wiki$',
				"index.php?page_id={$page_id}",
				'top'
			);
		}
		flush_rewrite_rules();
	}

	public function isceb_wiki_set_upload_page()
	{
		// delete_option('isceb_wiki-test');


		//get_option returns false by default if option doesn't exist
		$options = get_option('isceb_wiki-test');

		if ($options && ($options['en']['wiki_upload_1'] === null || $options['en']['wiki_upload_1'] == '')) {
			$page = get_page_by_title('Wiki Upload Page');
			if ($page === null) {
				$wiki_upload_page = array(
					'ID' => 0,
					'post_type' => 'page',
					'post_name' => 'wiki Upload Page',
					'post_title' => 'Wiki Upload Page',
					'post_status' => 'publish',
					'post_content' => '[wiki-form]',
				);
				$page_id = wp_insert_post($wiki_upload_page);
			} else {
				$page_id = $page->ID;
			}

		}
		
	}
}
