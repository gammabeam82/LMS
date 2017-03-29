$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();

	const booksCountContainer = $('.books-count');
	const bookUrl = booksCountContainer.attr('data-path');

	const archiveModal = $('#archive-modal');

	function getBooksCount()
	{
		$.get(bookUrl, function(data) {
			if(data['booksCount']) {
				booksCountContainer.html(data['booksCount']);
			}
		});
	}

	$('.to-archive').click(function(e) {
		e.preventDefault();
		let btn = $(this);

		if(!btn.hasClass('disabled')) {
			$.get($(this).attr('data-path'), function(data) {
				if(data['booksCount']) {
					booksCountContainer.html(data['booksCount']);
					btn.addClass('disabled');

					archiveModal.find('.modal-body').html(data['message']);
					archiveModal.modal('show');

					setTimeout(function() {
						archiveModal.modal('hide');
					}, 1000);
				}
			});
		}
	});

	$('.remove-from-archive').click(function(e) {
		let btn = $(this);
		$.get($(this).attr('data-path'), function(data) {
			if(data['message']) {
				btn.closest('tr').remove();
				booksCountContainer.html(data['booksCount']);

				//archiveModal.find('.modal-body').html(data['message']);
				//archiveModal.modal('show');

				if(data['booksCount'] == 0) {
					$('.archive-download').addClass('disabled');
				}

				/*setTimeout(function() {
					archiveModal.modal('hide');
				}, 1000);*/
			}
		});
	});

	getBooksCount();

});