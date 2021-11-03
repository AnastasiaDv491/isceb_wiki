var courses = [];

(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$.ajax({
		type: 'POST',
		url: wp_ajax.ajax_url,
		dataType: "json", // add data type
		data: { action: 'get_wiki_courses_ajax' },
		success: function (response) {
			$.each(response["data"], function (key, value) {
				courses.push([value["post_title"], value["ID"]]); // that's the posts data.
			});
			// console.log(response["data"][0]["post_title"]);
		}
	});

})(jQuery);

function updateList() {
	var input = document.getElementById('filesInput');
	var output = document.getElementById('fileList');
	var maxFileSize = 10; // MB
	var error_message = "";
	if (input.files.length > 0) {

		// Check the file size
		for (var x in input.files) {

			var filesize = ((input.files[x].size / 1024) / 1024).toFixed(4); // MB

			if (input.files[x].name != "item"
				&& typeof input.files[x].name != "undefined"
				&& filesize > maxFileSize) {
				error_message += `<li><b>${input.files[x].name} </b> is too big. Max size is ${maxFileSize} MB </li>`;
				console.log(error_message);
			}
		}

		if (error_message === "") {
			var fileCategoryOptions = "";
			var fileCourseOptions = "";
			var fileAcademicYearOptions = "";
			var currentAcademicYear = getCurrentAcademicYear();
			for (var j = 0; j < tempArray.length; ++j) {

				fileCategoryOptions += '<option>' + tempArray[j].name + '</option>';
			}

			for (let j = 0; j < courses.length; j++) {
				fileCourseOptions += '<option value="' + courses[j][1] + '">' + courses[j][0] + '</option>';

			}

			for (const [key, value] of Object.entries(academic_years["choices"])) {
				if (value == currentAcademicYear) {
					fileAcademicYearOptions += '<option value="' + key + '" selected>' + value + '</option>';
				}
				else {
					fileAcademicYearOptions += '<option value="' + key + '">' + value + '</option>';
				}

			}


			var children = "";
			for (var i = 0; i < input.files.length; ++i) {
				children += '<li>' + '<input class="inputFileName"  name="fileName_' + i + '" type="text" value="'
					+ input.files[i].name + '">' + '<ul><li style="display:flex;"><p>Type:</p> <select style="width:100%;" name="file_category_' + i
					+ '" class="js-example-basic-single">' + fileCategoryOptions
					+ '</select></li><li style="display:flex;"><p>Course:</p> <select style="width:100%;" class="js-example-basic-single" name=file_course_' + i + '>'
					+ fileCourseOptions + '</select></li>'
					+ '<li style="display:flex;"><p>Year:</p> <select style="width:100%;" class="js-example-basic-single" name=file_academic_year_' + i + '>'
					+ fileAcademicYearOptions + '</select></li></ul></li>';

			}
			output.innerHTML = '<ul>' + children + '</ul>';

			(function ($) {
				$('.js-example-basic-single').select2({ theme: "classic"});
				$("#button_wiki_file_submit").removeAttr("disabled");

			})(jQuery);
		}
		else {
			output.innerHTML = '<ul style="color: red">' + error_message + '</ul>';
		}
	}
	else {
		output.innerHTML = "";

		(function ($) {
			$('#button_wiki_file_submit').prop("disabled", true);
		})(jQuery);
	}

}

function getCurrentAcademicYear() {
	var now = new Date();
	if (now.getMonth() >= 8) {
		return `${now.getFullYear()} - ${now.getFullYear() + 1}`;
	}
	else {
		return `${now.getFullYear() - 1} - ${now.getFullYear()}`;
	}

}

