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
		$plugin_post_types->create_custom_post_type();

		Isceb_wiki_Activator::rewrite_wiki_base_url_to_page();

		flush_rewrite_rules();
		
		$lines = array(
			'RewriteCond %{REQUEST_URI} ^.*wp-content/uploads/isceb_wiki/.*',
			'RewriteRule ^(.*)$ ../../plugins/isceb_wiki/public/dl-file.php?file=$1 [QSA,L]',
			//Disable indexing
			'Options -Indexes',
			//Kill PHP Execution
			'<Files *.php>',
			'deny from all',
			'</Files>'
		);
		//Add lines into .htaccess for file downloads reroute
		insert_with_markers(get_home_path() . '/wp-content/uploads/isceb_wiki/' . '.htaccess', 'isceb_wiki', $lines);
	}

	//Only called when initalising
	private static function rewrite_wiki_base_url_to_page()
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
	}
}
