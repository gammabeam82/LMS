$(document).ready( () => {
	$('[data-toggle="tooltip"]').tooltip();

	const booksCountContainer = $('.books-count');
	const bookUrl = booksCountContainer.attr('data-path');


	$('.to-archive').click(function() {
		let btn = $(this);
		if(!btn.hasClass('disabled')) {
			$.get(btn.attr('data-path'), (data) => {
				if(data['booksCount']) {
					booksCountContainer.html(data['booksCount']);
					btn.addClass('disabled');
					$.notify(data['message'], { type: 'success' });
				}
			});
		}
	});

	$('.remove-from-archive').click(function() {
		let btn = $(this);
		$.get(btn.attr('data-path'), (data) => {
			if(data['message']) {
				btn.closest('tr').remove();
				$.notify(data['message'], { type: 'success' });
				booksCountContainer.html(data['booksCount']);
				if(data['booksCount'] === 0) {
					$('.archive-download').addClass('disabled');
				}
			}
		});
	});

	$.get(bookUrl, (data) => data['booksCount'] ? booksCountContainer.html(data['booksCount']) : false);

});