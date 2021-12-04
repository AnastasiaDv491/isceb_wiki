<?php

/**
 * The sidebar for the wiki
 *
 * @package ISCEB_WIKI
 * @version 1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<div class="col-md-2 widget-area" id="isceb-wiki-sidebar" role="complementary">
    <div class="isceb-nav-container-sticky">
        <div class="isceb-nav-header">

            <a href="<?php echo (get_site_url() . '/wiki') ?>" id="isceb-wiki-nav-header-text">
                <h4 style="margin-bottom: 0px;">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    ISCEB Wiki
                </h4>
            </a>

            <button id="isceb-wiki-nav-button-toggle" class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#isceb-wiki-navbar-content" aria-controls="isceb-wiki-navbar-content" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

        </div>

        <?php
        if (is_active_sidebar('isceb_wiki_sidebar_nav')) :
            dynamic_sidebar('ISCEB Wiki sidebar');
        else :
        ?>

            <div class="collapse navbar-collapse isceb-wiki-nav-container-supra dont-collapse-sm" id="isceb-wiki-navbar-content">
                <h4 class="isceb-nav-program-name"> <?php echo $title_of_page ?></h4>

                <div id="isceb-wiki-nav-container">
                    <?php foreach ($wiki_phases  as $phase) :
                        //Get all the courses for a certain phase
                        $wiki_courses = get_posts(array(
                            'post_type' => 'course',
                            'numberposts'       => -1,
                            'meta_query' => array(
                                array(
                                    'key' => 'phases', // name of custom field
                                    'value' => '"' . $phase->ID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                                    'compare' => 'LIKE'
                                )
                            )
                        ));
                    ?>

                        <div>
                            <i class="fa fa-sort-desc" aria-hidden="true" id="isceb-wiki-toggle<?php echo $phase->post_title ?>" onclick="isceb_wiki_sidebar_toggle_function('<?php echo ('isceb-wiki-courses-per-phase-' . $phase->post_title) ?>',this)"></i>
                            <a href="<?php echo get_permalink($phase->ID); ?>" class="isceb-wiki-nav-phase"> <?php echo $phase->post_title; ?> </a>
                        </div>
                        <hr class="isceb-wiki-nav-phase-line">

                        <ul class="isceb-wiki-courses-per-phase" id="<?php echo ('isceb-wiki-courses-per-phase-' . $phase->post_title) ?>" style="display: block;">
                            <?php foreach ($wiki_courses  as $course) :
                            ?>
                                <li>
                                    <a href="<?php echo add_query_arg('phase', $phase->ID, get_permalink($course->ID)); ?>" class="isceb-wiki-nav-course"> <?php echo $course->post_title; ?> </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>
            <?php
        endif;
            ?>
            <?php if (isceb_wiki_upload_page_url() !== '') : ?>
                <a href="<?php echo esc_url(isceb_wiki_upload_page_url()) ?>">
                    <button class="isceb-wiki-button-not-gb" id="isceb-wiki-nav-upload-btn"> Upload files </button>
                </a>
            <?php endif; ?>
            </div>
    </div>
</div>

<script>
    function isceb_wiki_sidebar_toggle_function(elementId, dropdownId) {
        console.log(dropdownId);

        console.log(elementId);
        var x = document.getElementById(elementId);
        if (x.style.display === "block") {
            x.style.display = "none";
            dropdownId.classList.add("fa-rotate-270");
        } else {
            x.style.display = "block";
            dropdownId.classList.remove("fa-rotate-270");
        }
    }
</script>