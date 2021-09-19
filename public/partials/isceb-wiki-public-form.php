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
wp_enqueue_script($this->plugin_name);
wp_enqueue_script('select2');
$academicYears = get_field_object('field_605619386bc68');
wp_localize_script($this->plugin_name, 'academic_years', $academicYears);

//Feedback message after upload
if (isset($_GET['message'])) {
	$my_options = get_exopite_sof_option('isceb_wiki-test');
	$message = '';
	switch ($_GET['message']) {
		case 'success':
			if (isset($my_options['isceb_wiki_file_upload_ok_text']) && $my_options['isceb_wiki_file_upload_ok_text'] != '') {
				$message = $my_options['isceb_wiki_file_upload_ok_text'];
			} else {
				//Success message doesn't exist

				$message = 'Your upload was succesfull, thank you for helping your fellow students';
			}
			echo ("<h3 id='isceb-wiki-error-message' style='color:green'>$message</h3>");
			break;
		case 'failed':
			//Check if null or empty
			if (isset($my_options['isceb_wiki_file_upload_went_wrong_text']) && $my_options['isceb_wiki_file_upload_went_wrong_text'] != '') {
				$message = $my_options['isceb_wiki_file_upload_went_wrong_text'];
			} else {
				//Error message doesn't exist
				$message = 'Something went wrong while uploading the file, please try again or contact someone from ISCEB';
			}
			echo ("<h3 id='isceb-wiki-error-message' style='color:red'>$message</h3>");

		default:
			# code...
			break;
	}
}



?>

<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>?action=post_first" method="post" enctype="multipart/form-data" id="file_form">

	<?php wp_nonce_field('submit_content', 'my_nonce_field'); ?>
	<h2> Here you can upload your files to WikiISCEB ! </h2>
	<br>
	<p> Only <b>PDF</b> files are allowed!</p>
	<ul>
		<li> You can change the name of the file </li>
		<li> Upload multiple files at once </li>
		<li> Select the corresponding course </li>
		<li> Select summary/exercises/exam types </li>
	</ul>
	<br>
	
	<label for="filesInput" class="custom-file-upload">
	<i class="fa fa-upload"  monospace="true"></i> Upload files
	</label>
	<input id="filesInput" multiple onchange='updateList()' type='file' name='wiki_file[]' accept='.pdf'  class="isceb-wiki-upload-input" />

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
	</script>

	<p>
		<input type='hidden' name='action' value='post_first'>
		<input type='hidden' name='userID' value='<?php echo get_current_user_id(); ?>'>
		<input id="button_wiki_file_submit" type='submit' value='Submit Content' disabled="disabled" class="isceb-wiki-upload-submit">
	</p>

</form>