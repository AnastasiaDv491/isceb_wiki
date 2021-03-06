<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       isceb.be
 * @since      1.0.0
 *
 * @package    Isceb_wiki
 * @subpackage Isceb_wiki/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
// If this file is called directly, abort.
if (!defined('WPINC')) die;
?>

<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>?action=post_first" method="post" enctype="multipart/form-data" id="file_form">

	<?php wp_nonce_field('submit_content', 'my_nonce_field'); ?>

	<p>
		<label><input type="text" name="post_title" placeholder="Enter a Title" value="Title"></label>
	</p>
	<p>
		<label><textarea rows='5' name="post_content" placeholder="Enter a description"></textarea></label>
	</p>

	<p>
		<input id='filesInput' multiple onchange='updateList()' type='file' name='wiki_file[]' accept='.pdf'>
	</p>
	<div id="fileList"></div>

	<?php
	$taxonomy = 'wiki_file_category';
	$terms = get_terms(array(
		'taxonomy' => $taxonomy,
		'hide_empty' => false,
	));

	?>

	<script type="text/javascript">
		//Assign php generated json to JavaScript variable
		var tempArray = <?php echo json_encode($terms); ?>;
		console.log(tempArray);
	</script>





	<p>
		<input type='hidden' name='action' value='post_first'>
		<input type='submit' value='Submit Content'>
	</p>






</form>