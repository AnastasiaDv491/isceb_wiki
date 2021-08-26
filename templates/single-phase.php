<?php

/**
 * The template for displaying a single phase on the wiki
 *
 * @package ISCEB_WIKI
 * @version 1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();


?>
<div class="wrapper h-100" id="single-wrapper">

    <div class="container-fluid" id="content" tabindex="-1">

        <div class="row">

            <?php do_action('isceb_wiki_before_main_content', get_the_id()) ?>

            <main class="isceb-wiki-site-main col-md-6" id="main">

                <div id="isceb-wiki-breadcrumb"><?php echo isceb_wiki_get_the_breadcrumb($post) ?></div>

                <?php


                while (have_posts()) {
                    the_post();
                    isceb_wiki_get_template('template-parts/content-isceb-wiki.php');
                }

                ?>

            </main>

            <?php do_action('isceb_wiki_after_main_content', get_the_id()) ?>
        </div>
    </div>
</div>

<?php
get_footer();
