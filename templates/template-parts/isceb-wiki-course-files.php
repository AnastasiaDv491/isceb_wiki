<?php

/**
 * Template for displaying wiki files 
 * 
 * Expects: 
 * $isceb_wiki_file_term: the type of the files
 * $isceb_wiki_course_files: a collection of wiki-file posts
 *
 * @package ISCEB_WIKI
 * @version 1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
global $wp;
?>

<h2> <?php echo ($isceb_wiki_file_term->name); ?> </h2>


<?php if (count($isceb_wiki_course_files) == 0) : ?>
    <p>Upload yours</p>
    <button class="isceb-wiki-button-not-gb"> Upload files </button>
<?php endif; ?>

<div class="isceb-wiki-container">
    <?php foreach ($isceb_wiki_course_files as  $isceb_wiki_course_file) :
        $file_content = get_field('file_attachment', $isceb_wiki_course_file->ID);
        $current_academic_year = get_field('academic_year', $isceb_wiki_course_file->ID);
    ?>


        <div class="isceb-wiki-file">
            <div class="isceb-wiki-file-left-part">
                <img src="<?php echo plugin_dir_url(dirname(dirname((__FILE__)))) . 'public/img/pdf-icon.svg' ?>" class="isceb-wiki-pdf">
            </div>
            <div class="isceb-wiki-file-meta">
                <p id="<?php echo $isceb_wiki_course_file->ID ?>" class="isceb-wiki-file-title">
                    <?php echo $isceb_wiki_course_file->post_title; ?>
                </p>
                <p class="isceb-wiki-filesize">File size: <?php echo size_format($file_content["filesize"]) ?></p>

                <p class="isceb-wiki-ac-year"> Academic Year: <?php echo $current_academic_year; ?></p>
            </div>

            <div class="isceb-wiki-file-right-part">
                <?php if (is_user_logged_in()) : ?>

                    <a href=" <?php echo $file_content['url'] ?>" download class="isceb-wiki-download-wrap" id="<?php echo $isceb_wiki_course_file->ID ?>">
                        <!-- <button class="isceb-wiki-file-download isceb-wiki-button-not-gb">Download</button> -->
                        <i class="fas fa-download fa-2x" id="<?php echo $isceb_wiki_course_file->ID ?>"></i>
                    </a>

                <?php else : ?>
                    <a href=" <?php echo (wp_login_url(home_url($wp->request))) ?> " class="isceb-wiki-download-wrap">
                        <button class="isceb-wiki-file-download isceb-wiki-button-not-gb">Login</button>
                    </a>
                <?php endif; ?>
            </div>
        </div>



    <?php endforeach; ?>
</div>