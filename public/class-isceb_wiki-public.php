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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/isceb_wiki-public.js', array('jquery'), $this->version, false);
		wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), $this->version, false);
		
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
	}



	function shortcode_wiki_submit($atts)
	{

		$args = shortcode_atts(
			array(
				'arg1'   => 'arg1',
				'arg2'   => 'arg2',
			),
			$atts
		);

		// code...

		$var = (strtolower($args['arg1']) != "") ? strtolower($args['arg1']) : 'default';

		ob_start();
		//TODO check if text of page where shortcode is included is shown
		include plugin_dir_path(__FILE__) . 'partials/isceb-wiki-public-form.php';
		echo("hello");
		// return $var;


		return ob_get_clean();
	}

	function locate_template($template, $settings, $page_type)
	{

		$theme_files = array(
			$page_type . '-' . $settings['custom_post_type'] . '.php',
			$this->plugin_name . DIRECTORY_SEPARATOR . $page_type . '-' . $settings['custom_post_type'] . '.php',
		);

		$exists_in_theme = locate_template($theme_files, false);

		if ($exists_in_theme != '') {

			// Try to locate in theme first
			return $template;
		} else {

			// Try to locate in plugin base folder,
			// try to locate in plugin $settings['templates'] folder,
			// return $template if non of above exist
			$locations = array(
				join(DIRECTORY_SEPARATOR, array(WP_PLUGIN_DIR, $this->plugin_name, '')),
				join(DIRECTORY_SEPARATOR, array(WP_PLUGIN_DIR, $this->plugin_name, $settings['templates_dir'], '')), //plugin $settings['templates'] folder
			);

			foreach ($locations as $location) {
				if (file_exists($location . $theme_files[0])) {
					return $location . $theme_files[0];
				}
			}

			return $template;
		}
	}

	function get_custom_post_type_templates($template)
	{
		global $post;

		$settings = array(
			'custom_post_type' => 'course',
			'templates_dir' => 'templates',
		);

		//if ( $settings['custom_post_type'] == get_post_type() && ! is_archive() && ! is_search() ) {
		if ($settings['custom_post_type'] == get_post_type() && is_single()) {

			return $this->locate_template($template, $settings, 'single');
		}

		return $template;
	}

	function add_files_to_single_if_course($content)
	{
		if (get_post_type() == 'course') {

			ob_start();
			include plugin_dir_path(__FILE__) . 'partials/isceb_wiki_files.php';
			$my_content = ob_get_contents();
			ob_end_clean();
			return $content . "\n" . $my_content;
			// $content .= '<p>Your new content here</p>';
		}
		return $content;
	}

	function add_course_to_single_if_phase($content)
	{
		if (get_post_type() == 'phase') {

			ob_start();
			include plugin_dir_path(__FILE__) . 'partials/isceb_wiki_phases.php';
			$my_content = ob_get_contents();
			ob_end_clean();
			return $content . "\n" . $my_content;
			// $content .= '<p>Your new content here</p>';
		}
		return $content;
	}


	function add_phase_to_single_if_program($content)
	{
		if (get_post_type() == 'program') {

			ob_start();
			include plugin_dir_path(__FILE__) . 'partials/isceb_wiki_programs.php';
			$my_content = ob_get_contents();
			ob_end_clean();
			return $content . "\n" . $my_content;
			// $content .= '<p>Your new content here</p>';
		}
		return $content;
	}

	public function get_wiki_courses_ajax() {
		 // Query Arguments
		 $args = array(
			'post_type' => "course",
			'post_status' => array('publish'),
			'nopaging' => true,
			'order' => 'DESC',
		);
	
		// The Query
		$ajaxposts = get_posts( $args ); // changed to get_posts from wp_query, because `get_posts` returns an array
		
		// echo json_encode( $ajaxposts );
	
		// exit; // exit ajax call(or it will return useless information to the response)s
		wp_send_json_success($ajaxposts);
	}
}
