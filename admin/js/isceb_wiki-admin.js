(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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



})(jQuery);


function isceb_event_download_attendess(event, page_id) {
	event.preventDefault();

	jQuery.ajax({
		type: 'POST',
		url: wp_ajax.ajax_url,
		dataType: "json", // add data type
		data: {
			action: 'isceb_event_attendee_download',
			isceb_wiki_file: event.target.id
		},
		success: function (response) {
			console.log(response);
			console.log(event.target.id);
		}
	});
}