<?php

/**
 * Template for ISCEB User Wiki files
 * 
 * Expects: 
 * 
 *
 * @package ISCEB_WIKI
 * @version 1.0.0
 */
// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <div class="isceb-wiki-container">
        <!-- <a href=" <?php echo esc_url($file_attachment_url) ?>" download> -->



            <div class="isceb-wiki-file">
                <div class="isceb-wiki-file-left-part">
                    <img src="<?php echo plugin_dir_url(dirname(dirname((__FILE__)))) . 'public/img/pdf-icon.svg' ?>" class="isceb-wiki-pdf">
                </div>

                <div class="isceb-wiki-file-meta">
                    <p class="isceb-wiki-file-title">
                        <?php echo  $isceb_wiki_file->post_title ?>
                    </p>
                    <!-- TODO: change the classes to the right names -->
                    <p class="isceb-wiki-filesize">Category: <?php echo esc_html($isceb_wiki_files_category); ?> </p>

                    <p class="isceb-wiki-ac-year">Course: <?php echo esc_html($isceb_wiki_file_course) ?> </p>
                    <p class="isceb-wiki-ac-year">Phase: <?php echo  esc_html($isceb_wiki_file_phase) ?></p>
                    <p class="isceb-wiki-ac-year">Program: <?php echo  esc_html($isceb_wiki_file_program) ?> </p>
                </div>
                <div class="isceb-wiki-file-right-part">


                    <a href=" <?php echo $file_content['url'] ?>" download class="isceb-wiki-download-wrap" id="<?php echo $isceb_wiki_course_file->ID ?>">
                        <!-- <button class="isceb-wiki-file-download isceb-wiki-button-not-gb">Download</button> -->
                        <i class="fas fa-download fa-2x" id="<?php echo $isceb_wiki_course_file->ID ?>"></i>
                    </a>

                </div>

                <!-- <p>About the file:</p> -->


</article>