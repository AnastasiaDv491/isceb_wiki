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
global $wp;


$category_name = [];
foreach ($isceb_wiki_files_category as $category) {
    $category_name[] = $category->name;
}

$course_name = [];
foreach ($isceb_wiki_file_course as $course) {
    $course_name[] = $course->post_title;
}
$phase_name = [];
foreach ($isceb_wiki_file_phase as $phase) {
    $phase_name[] = $phase->post_title;
}
// isceb_wiki_get_template('template-parts/content-isceb-user-wikifiles.php', array("isceb_wiki_files" => $owned_wiki_file, "isceb_wiki_files_category" =>  $owned_wiki_files_categories, "isceb_wiki_file_course" => $owned_wiki_file_courses, "isceb_wiki_file_phase" => $owned_wiki_files_phases, "isceb_wiki_file_program" => $owned_wiki_files_programs));
$program_name = [];
foreach ($isceb_wiki_file_program as $program) {
    $program_name[] = $program->post_title;
}
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <i><b>
            <h2>Here are the wiki files you uploaded!</h2>
        </b></i>
    <?php foreach ($isceb_wiki_files as $isceb_wiki_file) : ?>
        <a href=" <?php echo $file_attachment['url'] ?>" download>
            <h1><i class="fas fa-file-pdf"></i> <?php echo  $isceb_wiki_file->post_title ?> </h1>
        </a>
        <p>About the file:</p>
        <p>Category: <?php echo  implode(', ', $category_name) ?> </p>
        <p>Course: <?php echo  implode(', ', $course_name) ?> </p>
        <p>Phase: <?php echo   implode(', ', $phase_name) ?> </p>
        <p>Program: <?php echo   implode(', ', $program_name) ?> </p>
    <?php endforeach; ?>
</article>