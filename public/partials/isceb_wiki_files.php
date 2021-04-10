<?php

/*
*  Query posts for a relationship value.
*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
*/
wp_enqueue_script('isceb_wiki_files_script');

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

    if ($get_wiki_files) : ?>
        <h2> <?php echo ($wiki_file_term->name); ?> </h2>
        <ul>
            <?php $previous_academic_year = ""; ?>

            <?php foreach ($get_wiki_files as $get_wiki_file) : ?>
                <?php

                $current_academic_year = get_field('academic_year', $get_wiki_file->ID);

                if ($current_academic_year != $previous_academic_year) {
                    echo ("<h3>$current_academic_year</h3>");
                }
                $previous_academic_year = $current_academic_year;

                if (is_user_logged_in()) :
                    $file_content = get_field('file_attachment', $get_wiki_file->ID);

                ?>
                    <li>
                        <a id="<?php echo $get_wiki_file->ID ?>" class="isceb_wiki_file" href=" <?php echo $file_content['url'] ?>" > <?php echo $get_wiki_file->post_title; ?></a>
                    </li>

                <?php else :
                    $isceb_wiki_login_page = get_exopite_sof_option('isceb_wiki-test');
                    if (isset($isceb_wiki_login_page['isceb_wiki_login_page'])) {
                        echo ("<li>$get_wiki_file->post_title<a href=" . get_page_link($isceb_wiki_login_page['isceb_wiki_login_page']) . " > Login to download file </a></li>");
                    } else {
                        global $wp;
                        echo ("<li>$get_wiki_file->post_title<a href=" . wp_login_url(home_url($wp->request)) . "> Login to download file</a></li>");
                    }
                ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php
    $get_wiki_files = array();
}

?>