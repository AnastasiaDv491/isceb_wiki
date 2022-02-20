<?php

/**
 * Template for displaying nav items
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

<div class="isceb-phases-grid-container">
    <?php foreach ($isceb_wiki_nav_list as $isceb_wiki_nav_item) : ?>
        <a href="<?php echo get_permalink($isceb_wiki_nav_item->ID); ?>" class="isceb-grid-phase-item-url">
            <div class="isceb-phase-grid-item">
                <h6 class="isceb-phase-grid-item-header"> <?php echo $isceb_wiki_nav_item->post_title; ?></h6>
                <p><?php echo $isceb_wiki_nav_item->post_excerpt ?></p>
            </div>
        </a>
    <?php endforeach; ?>
</div>
