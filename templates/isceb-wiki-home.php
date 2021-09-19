<?php
 /**
 * Main template for ISCEB Wiki landing page
 * 
 *
 * @package ISCEB_WIKI
 * @version 1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

?>

<div class="wrapper" id="full-width-page-wrapper">

    <div class="container-fluid">


        <header class="isceb-wiki-home-header">
            <h1 id="isceb-wiki-home-search-headerText">Welcome to ISCEB WIKI! </h1>

            <form id="isceb-wiki-home-search-wrap" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="text" id="isceb-wiki-home-search-field" name="s" placeholder="What are you looking for?" value="<?php echo get_search_query(); ?>">
                <button type="submit" id="isceb-wiki-home-search-button" class="btn btn-secondary"><i class="fa fa-search"></i></button>
                
                <input type="hidden" name="post_type[]" value="phase" />
                <input type="hidden" name="post_type[]" value="course" />
                <input type="hidden" name="post_type[]" value="wiki-file" />
            </form>
        </header>

        <main class="site-main" id="main" role="main">

            <div class="isceb-grid-container">
                <?php

                $wiki_programs_args = array(
                    'post_type' => 'program',
                    'order' => 'ASC',
                    'orderby' => 'title',
                    'posts_per_page' => -1
                );
                
                $query = new WP_Query($wiki_programs_args);
              
                if ($query->have_posts()) :
                    remove_filter('get_the_excerpt', 'wp_trim_excerpt');
                    while ($query->have_posts()) : $query->the_post();
                        echo '<a href="'.get_permalink().'" class="isceb-grid-item-url"> ';
                        echo '<div class="isceb-grid-item">';
                        echo get_the_post_thumbnail($post->ID, array(75, 75),  array('class' => 'isceb-grid-item-logo'));;
                        echo '<h4 class="isceb-grid-item-header">' . get_the_title() . '</h4>';
                        echo '<p>' . get_the_excerpt() . '<p>';
                        echo '</div>';
                        echo '</a>';
                    endwhile;
                    add_filter('get_the_excerpt', 'wp_trim_excerpt');
                    wp_reset_postdata();
                endif;

                ?>

                <div class="isceb-grid-item" id="isceb-grid-item-upload">
                    <h4>Upload your files</h4>
                    <p>Some text here that is long</p>
                    <button class="isceb-wiki-button-not-gb"> Upload files </button>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
get_footer();
