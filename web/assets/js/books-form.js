$(document).ready( () => {
	$('.chosen-select').chosen();

	const upload = $('.upload');

	let file = upload.attr('data-file') ? upload.attr('data-file').split('/').pop() : '';

	upload.fileinput({
		'showPreview':false,
		'initialCaption': file
	});

} );