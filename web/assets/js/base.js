$(document).ready( () => {

	const booksCountContainer = $('.books-count');
	const errorMessage = $('.common-messages-error').text();

	$('[data-toggle="tooltip"]').tooltip();

	$('.to-archive').click(function() {
		let btn = $(this);
		if(!btn.hasClass('disabled')) {
			$.get(btn.attr('data-path'), (data) => {
				if(typeof data['booksCount'] !== 'undefined') {
					booksCountContainer.html(data['booksCount']);
					btn.addClass('disabled');
					$.notify(data['message'], { type: 'success' });
					if(!booksCountContainer.hasClass('badge-active')) {
						booksCountContainer.addClass('badge-active');
					}
				} else {
					$.notify(errorMessage, { type: 'danger' });
				}
			});
		}
	});

	$('.remove-from-archive').click(function() {
		let btn = $(this);
		$.get(btn.attr('data-path'), (data) => {
			if(typeof data['booksCount'] !== 'undefined') {
				let item = btn.closest('tr');
				item.addClass('animated fadeOutLeft');
				$.notify(data['message'], { type: 'success' });
				setTimeout(() => item.remove(), 800);
				booksCountContainer.html(data['booksCount']);
				if(data['booksCount'] === 0) {
					$('.archive-download').addClass('disabled');
					booksCountContainer.removeClass('badge-active');
				}
			} else {
				$.notify(errorMessage, { type: 'danger' });
			}
		});
	});

});