$(document).ready(function() {

	$('.delete').on('click', function(e) {
		e.preventDefault();
		$('.delete-author').attr('href', $(this).attr('data-path'));
		$('.author-name').html(
			$(this).attr('data-name')
		);
	});

});