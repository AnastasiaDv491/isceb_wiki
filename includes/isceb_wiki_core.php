<?php
/* Source: https://jeroensormani.com/how-to-add-template-files-in-your-plugin/*/


/**
 * Locate template.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme/isceb-wiki/$template_name
 * 2. /themes/theme/$template_name
 * 3. /plugins/isceb-wiki/templates/$template_name.
 *
 * @since 1.0.0
 *
 * @param 	string 	$template_name			Template to load.
 * @param 	string 	$string $template_path	Path to templates.
 * @param 	string	$default_path			Default path to template files.
 * @return 	string 							Path to the template file.
 */
function isceb_wiki_locate_template($template_name, $template_path = '', $default_path = '')
{
    // Set variable to search in isceb-wiki folder of theme.
    if (!$template_path) :
        $template_path = 'isceb-wiki/';
    endif;

    // Set default plugin templates path.
    if (!$default_path) :
        $default_path = plugin_dir_path(dirname(__FILE__)) . 'templates/'; // Path to the template folder
    endif;

    // Search template file in theme folder.
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));

    // Get plugins template file.
    if (!$template) :
        $template = $default_path . $template_name;
    endif;

    return apply_filters('isceb_locate_template', $template, $template_name, $template_path, $default_path);
}

/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @since 1.0.0
 *
 * @see isceb_wiki_locate_template()
 *
 * @param string 	$template_name			Template to load.
 * @param array 	$args					Args passed for the template file.
 * @param string 	$string $template_path	Path to templates.
 * @param string	$default_path			Default path to template files.
 */
function isceb_wiki_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
{
    if (is_array($args) && isset($args)) :
        extract($args);
    endif;

    $template_file = isceb_wiki_locate_template($template_name, $template_path, $default_path);

    if (!file_exists($template_file)) :
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
        return;
    endif;

    include $template_file;
}


/**
 * Template loader.
 *
 * The template loader will check if WP is loading a template
 * for a specific Post Type and will try to load the template
 * from out 'templates' directory.
 *
 * @since 1.0.0
 *
 * @param	string	$template	Template file that is being loaded.
 * @return	string				Template file that should be loaded.
 */
function isceb_wiki_template_loader($template)
{
    $find = array();
    $file = '';


    global $wp;

    if ($wp->request === 'wiki') :
        $file = 'isceb-wiki-home.php';
    elseif (is_singular('course')) :
        $file = 'single-course.php';
    elseif (is_singular('program')) :
        $file = 'single-program.php';
    elseif (is_singular('phase')) :
        $file = 'single-phase.php';
    endif;

    if ($file && file_exists(isceb_wiki_locate_template($file))) :
        $template = isceb_wiki_locate_template($file);
    endif;

    return $template;
}
add_filter('template_include', 'isceb_wiki_template_loader');



function isceb_wiki_get_the_breadcrumb($post)
{
    $breadcrumb = '';
    switch ($post->post_type) {
        case 'phase':
            $programs_of_phase = get_field('program');
            $wiki_phases = get_posts(array(
                'post_type' => 'phase',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'program', // name of custom field
                        'value' => '"' . $programs_of_phase[0]->ID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                )
            ));
            $breadcrumb = '<a href="' . get_permalink($programs_of_phase[0]->ID) . '">' . $programs_of_phase[0]->post_title . '</a> > '
                . '<span  class="isceb-wiki-last-breadcrumb">' . $post->post_title . '</span>';
            break;
        case 'course':
            $phases_of_course = get_field('phases', $post->ID);
            $programs_of_phase = get_field('program', $phases_of_course[0]->ID);

            $wiki_phases = get_posts(array(
                'post_type' => 'phase',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'program', // name of custom field
                        'value' => '"' . $programs_of_phase[0]->ID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                )
            ));
            // var_dump($programs_of_phase[0]);

            $breadcrumb = '<a href="' . get_permalink($programs_of_phase[0]->ID) . '">' . $programs_of_phase[0]->post_title . '</a>  > ' .
                '<a href="' . get_permalink($phases_of_course[0]->ID) . '">' . $phases_of_course[0]->post_title .  '</a> > '
                . '<span  class="isceb-wiki-last-breadcrumb">' . $post->post_title . '</span>';
            break;
    }

    return '<div>' . $breadcrumb . '</div>';
}



function isceb_wiki_content_navigation($pageID)
{
    switch (get_post_type($pageID)) {
        case 'program':
            $isceb_wiki_phases_of_program = get_posts(array(
                'post_type' => 'phase',
                'order'     => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'program', // name of custom field
                        'value' => '"' . $pageID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                )

            ));


            isceb_wiki_get_template('template-parts/isceb-wiki-content-nav.php', array("isceb_wiki_nav_list" => $isceb_wiki_phases_of_program));
            break;
        case 'phase':
            $get_wiki_courses = get_posts(array(
                'post_type' => 'course',
                'meta_query' => array(
                    array(
                        'key' => 'phases', // name of custom field
                        'value' => '"' . get_the_ID() . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                )
            ));
            isceb_wiki_get_template('template-parts/isceb-wiki-content-nav.php', array("isceb_wiki_nav_list" => $get_wiki_courses));
            break;
        case 'course':
            $wiki_file_terms = get_terms('wiki_file_category');

            foreach ($wiki_file_terms as $wiki_file_term) {
                $get_wiki_files = get_posts(array(
                    'post_type' => 'wiki-file',
                    'post_status' => 'publish',
                    'meta_key' => 'academic_year',
                    'orderby' => 'meta_value',
                    'order' => 'DESC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'wiki_file_category',
                            'field' => 'name',
                            'terms' => $wiki_file_term->name,
                            'operator' => 'AND'

                        )
                    ),
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'approved',
                            'value' => 'Yes',
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'course', // name of custom field
                            'value' => '"' . get_the_ID() . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                            'compare' => 'LIKE'
                        )
                    )
                ));
                isceb_wiki_get_template('template-parts/isceb-wiki-course-files.php', array("isceb_wiki_course_files" => $get_wiki_files, "isceb_wiki_file_term" => $wiki_file_term));
            }

            break;
    }
}
add_action('isceb_wiki_after_content', 'isceb_wiki_content_navigation', 10);


function isceb_wiki_navigation_sidebar($pageID)
{
    switch (get_post_type()) {
        case 'program':
            $wiki_phases = get_posts(array(
                'post_type' => 'phase',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'program', // name of custom field
                        'value' => '"' . get_the_ID() . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                )
            ));
            $title_of_page = get_the_title();
            break;
        case 'phase':
            $programs_of_phase = get_field('program');
            $wiki_phases = get_posts(array(
                'post_type' => 'phase',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'program', // name of custom field
                        'value' => '"' . $programs_of_phase[0]->ID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                )
            ));
            $title_of_page = $programs_of_phase[0]->post_title;
            break;
        case 'course':
            $phases_of_course = get_field('phases', get_the_ID());

            $phase_id = $phases_of_course[0]->ID;
            if (get_query_var('phase') !== '') {
                foreach ($phases_of_course as $phase) {
                    if ($phase->ID === intval(get_query_var('phase'))) {
                        $phase_id = $phase->ID;
                        break;
                    }
                }
            }

            $programs_of_phase = get_field('program', $phase_id);

            $wiki_phases = get_posts(array(
                'post_type' => 'phase',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'program', // name of custom field
                        'value' => '"' . $programs_of_phase[0]->ID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                )
            ));
            $title_of_page = $programs_of_phase[0]->post_title;
            break;
    }

    isceb_wiki_get_template('sidebar-templates/sidebar-isceb-wiki.php', array('wiki_phases' => $wiki_phases, 'title_of_page' => $title_of_page));
}
add_action('isceb_wiki_before_main_content', 'isceb_wiki_navigation_sidebar', 10);


/* UsersWP Integration*/
/**
 * Usable hooks
 * ‘uwp_before_validate’ => Triggers before fields validation
 * ‘uwp_validate_result’ => Filter for additional validations
 * ‘uwp_after_validate’ => Triggers after validation
 * ‘uwp_before_extra_fields_save’ => Filter for modifying custom fields before save
 * ‘uwp_after_extra_fields_save’ => Filter for modifying custom fields after save
 * ‘uwp_after_custom_fields_save’ => Triggers after custom fields saved
 * ‘uwp_after_process_register’ => Triggers after registration is complete which
 */


if (in_array('userswp/userswp.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_filter('uwp_account_page_title', 'isceb_account_page_title_cb', 10, 2);
    add_action('uwp_account_form_display', 'isceb_display_user_tab_content', 20, 1);
    add_filter('uwp_account_available_tabs', 'add_extra_tab_to_edit_account_page', 10, 3);
    add_action('uwp_after_process_account', 'isceb_after_account_save', 30, 1);
    add_action('uwp_after_process_register', 'isceb_after_register', 30, 2);
    add_filter('uwp_form_fields_predefined', 'isceb_add_custom_field_userswp', 10, 2);
    add_action('template_redirect', 'isceb_template_redirect_userswp_privacy');
}


/**
 * Add tab to edit account page
 * uwp_account_all_tabs
 */
function add_extra_tab_to_edit_account_page($tabs)
{

    // 	["title" => 'Orders',
    // 	 'icon' => 'fas fa-sign-out-alt'];
    // 'link' => 'http://localhost/www/my-account/orders/'];
    // A direct link to somewhere is also possible

    $new_tab = array(
        'Order' => ["title" => 'Orders',     'icon' => 'fas fa-sign-out-alt'],
        'WikiFiles' => ["title" => 'Your Wiki Files', 'icon' => 'fas fa-file']
    );

    //Remove notifcations tab
    unset($new_tab["Notifications"]);

    $new_tabs = insertInArrayAfterPosition($tabs, $new_tab, 3);
    unset($new_tabs['notifications']);

    return $new_tabs;
}

function insertInArrayAfterPosition($array, $toInsertValue, $position)
{
    return array_slice($array, 0, $position, true) + $toInsertValue +  array_slice($array, $position, count($array) - 3, true);
}

function isceb_account_page_title_cb($title, $type)
{
    switch ($type) {
        case 'Order':
            $title = __('Your orders', 'uwp-messaging');
            break;
        case 'WikiFiles':
            $title = __('Your Wiki Files', 'uwp-messaging');
            break;
    }

    return $title;
}


/**
 * Contains the content of the orders tab
 *
 * @since       1.0.0
 *
 * @param array $type Type of the form
 *
 */
function isceb_display_user_tab_content($type)
{
    switch ($type) {
        case 'Order':
            wc_get_template('myaccount/my-orders.php', array(
                'current_user' => get_user_by('id', get_current_user_id()),
                'order_count'   => -1
            ));
            break;

        case 'WikiFiles':
            isceb_wiki_get_files_of_owner(get_current_user_ID());
            break;
    }
}


/* UsersWP Account Sync */
//This function is being called when a profile in UsersWP is being updated
/* Data format
Array
(
    [first_name] => ddsdf
    [last_name] => ddqsdf
    [display_name] => dddfsq
    [email] => xxxx.xxx@hotmail.comdsfq
    [home_adress] => ffffqsdfsdfddd
    [bio] => dddsdfqsdf
    [uwp_account_nonce] => aa5f6bde30
    [uwp_account_submit] => Update Account
)

*/
function isceb_after_account_save($data)
{
    // error_log(print_r($data, true));
    // error_log($user->ID);

    $user = wp_get_current_user();
    isceb_sync_user_with_woocommerce($data, $user->ID);
}


//This function is being called when a new account is registered
function isceb_after_register($data, $user_id)
{
    // error_log(print_r($data, true));
    // error_log(print_r($user_id, true));

    isceb_sync_user_with_woocommerce($data, $user_id);

    //Default to hiding users
    update_user_meta($user_id, 'uwp_hide_from_listing', 1);
}

function isceb_sync_user_with_woocommerce($data, $user_ID)
{
    $metaFieldsToUpdate = array(
        'billing_first_name' => $data['first_name'],
        'billing_last_name' => $data['last_name'],
    );

    foreach ($metaFieldsToUpdate as $key => $value) {
        update_user_meta($user_ID, $key, $value);
    }
}





/*
Metadata fields used by woocommerce
[first_name] 
[last_name] 
[billing_first_name] 
[billing_last_name] => Array ( [0] => name )
[billing_company] => Array ( [0] => Odisee ) 
[billing_address_1] => Array ( [0] => field ) 
[billing_address_2] => Array ( [0] => 26 ) 
[billing_city] => Array ( [0] => Brussel )
[billing_postcode] => Array ( [0] => 1000 ) 
[billing_country] => Array ( [0] => BE )
[billing_state] => Array ( [0] => dd ) 
[billing_phone] => Array ( [0] => +32123456789 ) 
[billing_email] => Array ( [0] => example@example.com ) 
[shipping_first_name] => Array ( [0] => aname ) 
[shipping_last_name] => Array ( [0] => anothername ) 
[shipping_company] => Array ( [0] => Odisee ) 
[shipping_address_1] => Array ( [0] => Straatnaam ) 
[shipping_address_2] => Array ( [0] => 13 ) 
[shipping_city] => Array ( [0] => Schaarbeek ) 
[shipping_postcode] => Array ( [0] => 1100 ) 
[shipping_country] => Array ( [0] => BE ) 
[shipping_state] => Array ( [0] => dd ) 
	  */

/* UsersWP Custom Block */
//A way to add a custom filed to userswp
function isceb_add_custom_field_userswp($custom_fields, $type)
{
    // WordPress
    $custom_fields['testisceb'] = array(
        'field_type' => 'text',
        'class'      => 'isceb-wc-field-sync',
        'field_icon' => 'fab fa-wordpress-simple',
        'site_title' => __('Isceb test', 'userswp'),
        'help_text'  => __('Let users enter their WordPress profile url.', 'userswp'),
        'defaults'   => array(
            'admin_title'   => 'Test Isceb',
            'site_title'    => 'Test Isceb',
            'form_label'    => __('WordPress url', 'userswp'),
            'htmlvar_name'  => 'wordpress',
            'is_active'     => 1,
            'default_value' => '',
            'is_required'   => 0,
            'required_msg'  => '',
            'field_icon'    => 'fab fa-wordpress-simple',
            'css_class'     => 'btn-wordpress'
        )
    );

    return $custom_fields;
}



/* Hide all profile information 
    This is not perfect because if a shortcode would be used on another page we wouldn't block it
*/
function isceb_template_redirect_userswp_privacy()
{
    global $post;

    if (!is_page()) {
        return false;
    }

    if (uwp_is_page_builder()) {
        return false;
    }

    $current_page_id = isset($post->ID) ? absint($post->ID) : '';
    $uwp_page = uwp_get_page_id('profile_page', false);
    $uwp_users_page = uwp_get_page_id('users_page', false);
    $user_list_item_page = uwp_get_page_id('user_list_item_page', false);

    if (
        $uwp_page && ((int) $uwp_page ==  $current_page_id)
        ||   $uwp_users_page && ((int) $uwp_users_page ==  $current_page_id)
        ||   $user_list_item_page && ((int) $user_list_item_page ==  $current_page_id)
    ) {
        wp_safe_redirect(home_url());
        exit();
    }


    //Works if you want to hide for certain roles
    // if ( $uwp_page && ((int) $uwp_page ==  $current_page_id ) ) {
    // 	$user = uwp_get_user_by_author_slug();
    // 	if( ! $user || ! $user->roles ){
    // 		return false;
    // 	}

    // 	if(isset($user->roles) && $user->ID != get_current_user_id() && (in_array('administrator', (array) $user->roles) || in_array('subscriber', (array) $user->roles))){
    // 		wp_redirect( home_url() );
    // 		exit();
    // 	}
    // }
}


function isceb_wiki_upload_page_url()
{
    $options = get_option('isceb_wiki-test');
    if (array_key_exists('wiki_upload_1', $options['en']) && $options['en']['wiki_upload_1'] !== '') {
        # code...
        return get_permalink($options['en']['wiki_upload_1']);
    } else {
        return '';
    }
}

add_action('pre_get_posts', 'isceb_remove_not_approved_files_from_search');
function isceb_remove_not_approved_files_from_search($query)
{
    if ($query->is_search) {
        $query->set(
            'meta_query',
            array(
                array(
                    'key' => 'approved',
                    'value' => 'No',
                    'compare' => 'NOT EXISTS',
                ),
            )
        );
    }
}



function isceb_wiki_get_files_of_owner($user_id)
{
    $owned_wiki_files = get_posts(array(
        'post_type' => 'wiki-file',
        'post_status' => 'publish',
        'meta_key' => 'academic_year',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'author' => $user_id,
    ));

    $category_name = [];
    $course_name = [];
    $phase_name = [];
    $program_name = [];

    foreach ($owned_wiki_files as $owned_wiki_file) {
        $file_content = get_field('file_attachment', $owned_wiki_file->ID);
        $download_count = get_field('download_count', $owned_wiki_file->ID);
        $download_count = is_null($download_count) ? 0 : $download_count;

        $owned_wiki_files_categories = get_the_terms($owned_wiki_file->ID, 'wiki_file_category');
        //Don't display file if it doesn't have a category (exam, summary....)
        if (!empty($owned_wiki_files_categories)) {

            foreach ($owned_wiki_files_categories as $owned_wiki_files_category) {
                $category_name[] = $owned_wiki_files_category->name;
            }

            $owned_wiki_file_courses = get_field('course', $owned_wiki_file->ID);
            if ($owned_wiki_file_courses) {

                foreach ($owned_wiki_file_courses as $owned_wiki_files_course) {
                    $course_name[] = $owned_wiki_files_course->post_title;

                    $owned_wiki_files_phases = get_field('phases', $owned_wiki_files_course->ID);

                    if (!is_null($owned_wiki_files_phases) && !empty($owned_wiki_files_phases)) {
                        foreach ($owned_wiki_files_phases as $owned_wiki_files_phase) {
                            $phase_name[] = $owned_wiki_files_phase->post_title;

                            $owned_wiki_files_programs = get_field('program', $owned_wiki_files_phase->ID);

                            foreach ($owned_wiki_files_programs as $owned_wiki_files_program) {
                                $program_name[] = $owned_wiki_files_program->post_title;
                            }
                        }
                    }
                }
            }
            isceb_wiki_get_template(
                'template-parts/content-isceb-user-wikifiles.php',
                array(
                    "isceb_wiki_file" => $owned_wiki_file,
                    "isceb_wiki_files_category" => implode(', ',  $category_name),
                    "isceb_wiki_file_course" =>  implode(', ', $course_name),
                    "isceb_wiki_file_phase" => implode(', ', array_unique($phase_name)),
                    "isceb_wiki_file_program" =>    implode(', ', array_unique($program_name)),
                    "file_attachment_url" => $file_content['url'],
                    "isceb_wiki_download_count" => $download_count,
                ),
            );
        }
        $category_name = [];
        $course_name = [];
        $phase_name = [];
        $program_name = [];
    }

    // get the file
    // -- get the category
    // -- get the course
    // --- get phase
    // ---- get the program

}
