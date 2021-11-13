<?php

/**
 * Template for ISCEB User Wiki files
 * 
 * Expects: 
 * 
 *
 * @package ISCEB_WIKI
 * @version 1.0.0
 */
// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <i>
        <b>
            <h2>Here are the wiki files you uploaded!</h2>
        </b>
    </i>
    <a href=" <?php echo esc_url($file_attachment_url) ?>" download>
        <h1><i class="fas fa-file-pdf"></i> <?php echo  $isceb_wiki_file->post_title ?> </h1>
    </a>
    <p>About the file:</p>
    <p>Category: <?php echo esc_html($isceb_wiki_files_category) ; ?> </p>
    <p>Course: <?php echo esc_html( $isceb_wiki_file_course) ?></p>
    <p>Phase: <?php echo  esc_html($isceb_wiki_file_phase) ?> </p>
    <p>Program: <?php echo  esc_html($isceb_wiki_file_program) ?> </p>

</article>