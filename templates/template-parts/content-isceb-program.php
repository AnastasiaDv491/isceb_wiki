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
        <?php
        the_content();
        do_action('isceb_wiki_after_content', get_the_id());
        ?>
    </div>
</article>