$(document).ready(() => {

	const booksCountContainer = $('.books-count');
	const errorMessage = $('.common-messages-error').text();
	const backToTop = $('.back-to-top');

	$('.navbar-fixed-top').autoHidingNavbar({
		'showOnBottom': false
	});

	$(document).ajaxError(() => $.notify(errorMessage, {type: 'danger'}));

	$('[data-toggle="tooltip"]').tooltip({
		delay: {
			"show": 500,
			"hide": 100
		}
	});

	$('.to-archive').click(function () {
		let btn = $(this);
		if (!btn.hasClass('disabled')) {
			$.getJSON(btn.attr('data-path'))
				.then((data) => {
					if (typeof data['booksCount'] !== 'undefined') {
						booksCountContainer.html(data['booksCount']);
						btn.addClass('disabled');
						$.notify(data['message'], {type: 'success'});
						if (!booksCountContainer.hasClass('badge-active')) {
							booksCountContainer.addClass('badge-active');
						}
						booksCountContainer.addClass('animated bounceIn');
						setTimeout(() => booksCountContainer.removeClass('animated bounceIn'), 600);
					} else {
						$.notify(errorMessage, {type: 'danger'});
					}
				});
		}
	});

	$('.remove-from-archive').click(function () {
		let btn = $(this);
		$.getJSON(btn.attr('data-path'))
			.then((data) => {
				if (typeof data['booksCount'] !== 'undefined') {
					let item = btn.closest('tr');
					item.addClass('animated fadeOut');
					$.notify(data['message'], {type: 'info'});
					setTimeout(() => item.remove(), 800);
					booksCountContainer.html(data['booksCount']);
					if (data['booksCount'] === 0) {
						$('.archive-download').addClass('disabled');
						booksCountContainer.removeClass('badge-active');
						setTimeout(() => $('table').remove(), 800);
					}
				} else {
					$.notify(errorMessage, {type: 'danger'});
				}
			});
	});

	$(window).scroll(function () {
		if ($(this).scrollTop() > 50) {
			backToTop.fadeIn();
		} else {
			backToTop.fadeOut();
		}
	});

	backToTop.click(() => {
		backToTop.tooltip('hide');
		$('body,html').animate({
			scrollTop: 0
		}, 600);
		return false;
	});

});