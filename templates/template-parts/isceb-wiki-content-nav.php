<?php
/**
 * Available variables:
 * Array $isceb_wiki_nav_list contains either course or a phase
 */
?>
<ul>
<?php foreach( $isceb_wiki_nav_list as $isceb_wiki_nav_item ): ?>
<li>
    <a href=" <?php echo get_permalink($isceb_wiki_nav_item->ID); ?>"> <?php echo $isceb_wiki_nav_item->post_title; ?> </a>
</li>
<?php endforeach; ?>
<ul>