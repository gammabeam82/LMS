$(document).ready(function() {
	$('.chosen-select').chosen();

	$('.delete').on('click', function(e) {
		$('.delete-book').attr('href', $(this).attr('data-path'));
	});

});