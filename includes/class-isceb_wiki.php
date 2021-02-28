<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       isceb.be
 * @since      1.0.0
 *
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/includes
 * @author     Anastasia Dvoryanchikova <anadvoryanchikova@gmail.com>
 */
class Isceb_wiki
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Isceb_wiki_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('ISCEB_WIKI_VERSION')) {
			$this->version = ISCEB_WIKI_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'isceb_wiki';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Isceb_wiki_Loader. Orchestrates the hooks of the plugin.
	 * - Isceb_wiki_i18n. Defines internationalization functionality.
	 * - Isceb_wiki_Admin. Defines all hooks for the admin area.
	 * - Isceb_wiki_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-isceb_wiki-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-isceb_wiki-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-isceb_wiki-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-isceb_wiki-public.php';

		/**
		 * Custom Post Types
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-isceb_wiki-post_types.php';

		/**
		 * Define path and URL to the ACF plugin.
		 * Include the ACF plugin.
		 * Customize the url setting to fix incorrect asset URLs.
		 * (Optional) Hide the ACF admin menu item.
		 */
		define('isceb_wiki_ACF_PATH', plugin_dir_path(dirname(__FILE__)) . '/includes/acf/');
		define('isceb_wiki_ACF_URL', plugin_dir_url(dirname(__FILE__)) . '/includes/acf/');



		include_once(isceb_wiki_ACF_PATH . 'acf.php');

		// add_filter('acf/settings/remove_wp_meta_box', '__return_false');

		add_filter('acf/settings/url', 'isceb_wiki_settings_url');
		function isceb_wiki_settings_url($url)
		{
			return isceb_wiki_ACF_URL;
		}
		//add_filter('acf/settings/show_admin', 'isceb_wiki_settings_show_admin');
		function isceb_wiki_settings_show_admin($show_admin)
		{
			return false;
		}

		add_filter('acf/settings/save_json', 'isceb_wiki_json_save_point');
		function isceb_wiki_json_save_point($path)
		{
			$path = plugin_dir_path(dirname(__FILE__)) . 'acf-json';
			return $path;
		}

		add_filter('acf/settings/load_json', 'isceb_wiki_json_load_point',1);
		function isceb_wiki_json_load_point($paths)
		{
			unset($paths[0]);
			$paths[] = plugin_dir_path(dirname(__FILE__)) . '/acf-json';
			error_log("ddddddddd");
			error_log( plugin_dir_path(dirname(__FILE__)) . '/acf-json');
			return $paths;
		}


		$this->loader = new Isceb_wiki_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Isceb_wiki_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Isceb_wiki_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_post_types = new Isceb_Wiki_Post_Types();
		$this->loader->add_action('init', $plugin_post_types, 'create_custom_post_type', 999);
		$plugin_admin = new Isceb_wiki_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		/*
     * Handle POST
     */
		$this->loader->add_action('admin_post_post_first', $plugin_admin, 'post_first');

		//Turn this on if you also want it for non authenticated users
		// $this->loader->add_action('admin_post_nopriv_post_first', $plugin_admin, 'post_first');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Isceb_wiki_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		/**
		 * Register shortcode via loader
		 *
		 * Use: [short-code-name args]
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/262
		 */

		$this->loader->add_shortcode("wiki-form", $plugin_public, "shortcode_wiki_submit", $priority = 10, $accepted_args = 2);

		// Template page courses
		//Uncomment to register custom post type course
		// $this->loader->add_filter( 'template_include', $plugin_public, 'get_custom_post_type_templates' );

		$this->loader->add_filter('the_content', $plugin_public, 'add_files_to_single_if_course');

		$this->loader->add_filter('the_content', $plugin_public, 'add_course_to_single_if_phase');

		$this->loader->add_filter('the_content', $plugin_public, 'add_phase_to_single_if_program');
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Isceb_wiki_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
