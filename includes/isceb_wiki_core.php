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
            $title_of_page = $programs_of_phase[0]->post_title;
            break;
    }

    isceb_wiki_get_template('sidebar-templates/sidebar-isceb-wiki.php', array('wiki_phases' => $wiki_phases, 'title_of_page' => $title_of_page));
}
add_action('isceb_wiki_before_main_content', 'isceb_wiki_navigation_sidebar', 10);
