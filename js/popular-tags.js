jQuery(document).ready(function(){

	var tag_ids = jQuery('#ec-popular-tags-id').val();
	var source = jQuery('#ec-popular-tags-source').val();
	var source_id = jQuery('#ec-popular-tags-source_id').val();

	if ( tag_ids != undefined ) {

		var data = {
			'action': 'ec_insert_tags_visit',
			'tag_ids': tag_ids,
			'source': source,
			'source_id': source_id
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			console.log(response);
		});

	}

});