<?php
/**
 * Template for 
 * 
 * Expects: 
 * $isceb_wiki_nav_list: a collection of posts
 *
 * @package ISCEB_WIKI
 * @version 1.0.0
 */
// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<header class="entry-header">
		<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
	</header>

	<div class="entry-content">
        hello
           
        <div class="isceb-phases-grid-container">
		<?php

		// the_content();
		// do_action('isceb_wiki_after_content', get_the_id())

        
        $wiki_phase_args = array(
            'post_type' => 'phase',
            'order' => 'ASC',
            'orderby' => 'title',
            'posts_per_page' => -1
        );
        
        $query = new WP_Query($wiki_phase_args);

        if ($query->have_posts()) :
            remove_filter('get_the_excerpt', 'wp_trim_excerpt');
            while ($query->have_posts()) : $query->the_post();
                echo '<a href="'.get_permalink().'" class="isceb-grid-phase-item-url"> ';
                echo '<div class="isceb-phase-grid-item">';
                echo get_the_post_thumbnail($post->ID, array(75, 75),  array('class' => 'isceb-phase-grid-item-logo'));;
                echo '<h6 class="isceb-phase-grid-item-header">' . get_the_title() . '</h6>';
                echo '<p>' . get_the_excerpt() . '<p>';
                echo '</div>';
                echo '</a>';
            endwhile;
            add_filter('get_the_excerpt', 'wp_trim_excerpt');
            wp_reset_postdata();
        endif;

        
		?>

    </div>
	</div>

</article>