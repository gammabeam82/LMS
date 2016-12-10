$(document).ready(function() {
	$('.chosen-select').chosen();

	$('.delete').on('click', function(e) {
		$('.delete-book').attr('href', $(this).attr('data-path'));
		$('.book-name').html(
			$(this).attr('data-name')
		);
	});

	$('.datepicker').datetimepicker({
		format: 'DD.MM.YYYY',
		locale: 'ru'
	});
});