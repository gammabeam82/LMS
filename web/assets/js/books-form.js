$(document).ready(function() {
	$('.chosen-select').chosen();

	if($('upload')) {
		$('.upload').fileinput(
			{'showPreview':false}
		);
	}

	$('.delete').on('click', function(e) {
		e.preventDefault();
		$('.delete-book').attr('href', $(this).attr('data-path'));
		$('.book-name').html(
			$(this).attr('data-name')
		);
	});
});