<?php

/**
 * The template for displaying a single course on the wiki
 *
 * @package UnderStrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();


?>
<p>single program from plugin</p>
<div class="wrapper h-100" id="single-wrapper">

    <div class="container-fluid" id="content" tabindex="-1">

        <div class="row">

            

            <main class="isceb-wiki-site-main col-md-6" id="main">

                <div id="isceb-wiki-breadcrumb"><?php echo isceb_wiki_get_the_breadcrumb($post) ?></div>

                <?php


                while (have_posts()) {
                    the_post();
                    isceb_wiki_get_template('template-parts/content-isceb-wiki.php');
                }

                ?>

            </main><!-- #main -->

            <!-- Do the right sidebar check -->
            <?php get_template_part('global-templates/right-sidebar-check'); ?>

        </div><!-- .row -->

    </div><!-- #content -->

</div><!-- #single-wrapper -->

<?php
get_footer();
