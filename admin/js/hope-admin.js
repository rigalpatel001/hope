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

	/*
	 * Select/Upload image(s) event
	 */
	$(document).on('click', '.upload_image_button', upload_image_button)
		.on('click', '.remove_image_button', remove_image_button);

	function upload_image_button(e) {

		e.preventDefault();
		var $this = $(e.currentTarget);
		var $input_field = $this.prev();
		var $image = $this.parent().find('.uploaded_image');
		var custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Add Image',
			button: {
				text: 'Add Image'
			},
			multiple: false
		});
		custom_uploader.on('select', function () {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$input_field.val(attachment.url);
			$image.html('<img src="' + attachment.url + '" />');
		});
		custom_uploader.open();
	}

	function remove_image_button(e) {
		e.preventDefault();
		var $this = $(e.currentTarget);
		var $input_field = $this.parent().find('.featured_image_upload');
		var $image = $this.parent().find('.uploaded_image');

		$input_field.val('');
		$image.html('');
	}

})(jQuery);