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

<ul>
<?php foreach( $isceb_wiki_nav_list as $isceb_wiki_nav_item ): ?>
<li>
    <a href=" <?php echo get_permalink($isceb_wiki_nav_item->ID); ?>"> <?php echo $isceb_wiki_nav_item->post_title; ?> </a>
</li>
<?php endforeach; ?>
<ul>