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
		data: { action : 'get_wiki_courses_ajax' },
		success: function( response ) {
			$.each( response["data"], function( key, value ) {
				console.log(value);
				courses.push( [value["post_title"], value["ID"]] ); // that's the posts data.
			} );
			// console.log(response["data"][0]["post_title"]);
		}
	});
	
		
	
	


})(jQuery);

function updateList() {
	var input = document.getElementById('filesInput');

	if (input.files.length > 0) {

		var fileCategoryOptions = "";
		var fileCourseOptions = "";
	
		for (var j = 0; j < tempArray.length; ++j) {
	
			fileCategoryOptions += '<option>' + tempArray[j].name + '</option>';
		}
		
		console.log(courses);
		for (let j = 0; j < courses.length; j++) {
			fileCourseOptions += '<option value="'+courses[j][1] + '">' + courses[j][0] + '</option>';
	
		}
	
		var output = document.getElementById('fileList');
		var children = "";
		for (var i = 0; i < input.files.length; ++i) {
			children += '<li>' + '<input class="inputFileName"  name="fileName_'+i+'" type="text" value="'
					+input.files[i].name +'">' + '<select name="file_category_' + i 
					+ '" class="js-example-basic-single">' + fileCategoryOptions 
					+ '</select><select class="js-example-basic-single" name=file_course_'+i+'>'
					+fileCourseOptions+'</select> </li>';
	
		}
		output.innerHTML = '<ul>' + children + '</ul>';
	
		(function ($) {
			$('.js-example-basic-single').select2({theme: "classic",width: 'resolve'});
			$('#button_wiki_file_submit').prop("disabled", false);
		
		})(jQuery);
		
	} 
	else {
		(function ($) {
			$('#button_wiki_file_submit').prop("disabled", true);
		})(jQuery);
	}
	
}


