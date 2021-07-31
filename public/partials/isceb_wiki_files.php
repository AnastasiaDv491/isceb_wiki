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
        <div class="isceb-wiki-container">
            <?php $previous_academic_year = ""; ?>

            <?php foreach ($get_wiki_files as $get_wiki_file) : ?>
                <?php

                $current_academic_year = get_field('academic_year', $get_wiki_file->ID);


                $previous_academic_year = $current_academic_year;

                $file_content = get_field('file_attachment', $get_wiki_file->ID);
                ?>

                <div class="isceb-wiki-file">

                    <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'img/pdf-icon.svg' ?>" class="isceb-wiki-pdf">
                    <div class="isceb-wiki-file-meta">
                        <p id="<?php echo $get_wiki_file->ID ?>" class="isceb-wiki-file-title"> <?php echo $get_wiki_file->post_title; ?></p>
                        <p class="isceb-wiki-filesize">File size: <?php echo size_format($file_content["filesize"]) ?></p>
                        <p class="isceb-wiki-ac-year"> Academic Year: <?php echo $current_academic_year; ?></p>
                    </div>

                    <?php if (is_user_logged_in()) : ?>
                        <a href=" <?php echo $file_content['url'] ?>" download class="isceb-wiki-download-wrap">
                            <button class="isceb-wiki-file-download isceb-wiki-button-not-gb">Download</button>
                        </a>
                        <?php else :
                        $isceb_wiki_login_page = get_exopite_sof_option('isceb_wiki-test');
                        if (isset($isceb_wiki_login_page['isceb_wiki_login_page']) && $isceb_wiki_login_page['isceb_wiki_login_page'] != '') {
                        ?>
                            <a href=" <?php echo get_page_link($isceb_wiki_login_page['isceb_wiki_login_page']) ?> " class="isceb-wiki-download-wrap">
                                <button class="isceb-wiki-file-download isceb-wiki-button-not-gb">Login to download file</button>
                            </a>

                        <?php
                        } else {
                            global $wp;
                        ?>

                            <a href=" <?php echo (wp_login_url(home_url($wp->request))) ?> " class="isceb-wiki-download-wrap">
                                <button class="isceb-wiki-file-download isceb-wiki-button-not-gb">Login to download file</button>
                            </a>

                        <?php
                        }
                        ?>
                    <?php endif; ?>




                </div>



            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php
    $get_wiki_files = array();
}

?>