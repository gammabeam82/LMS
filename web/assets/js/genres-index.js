$(document).ready(function() {
	$('.delete').on('click', function(e) {
		$('.delete-genre').attr('href', $(this).attr('data-path'));
		$('.genre-name').html(
			$(this).attr('data-name')
		);
	});
});