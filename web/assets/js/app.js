$(document).ready(() => {

	const booksCountContainer = $('.books-count');
	const errorMessage = $('.common-messages-error').text();
	const hl = $('.highlight-value');

	if(typeof hl.val() !== 'undefined') {
		if(false === hl.closest('div').hasClass('has-error')) {
			$('.highlight').mark(hl.val(), {});
		}
	}

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
		if (true === btn.hasClass('disabled')) {
			return;
		}
		btn.addClass('disabled');
		$.getJSON(btn.attr('data-path'))
			.then((data) => {
				if (typeof data['booksCount'] !== 'undefined') {
					booksCountContainer.html(data['booksCount']);
					$.notify(data['message'], {type: 'success'});
					if (false === booksCountContainer.hasClass('badge-active')) {
						booksCountContainer.addClass('badge-active');
					}
					booksCountContainer.addClass('animated bounceIn');
					setTimeout(() => booksCountContainer.removeClass('animated bounceIn'), 600);
				} else {
					$.notify(errorMessage, {type: 'danger'});
					btn.removeClass('disabled');
				}
			});
	});

	$('.remove-from-archive').click(function () {
		let btn = $(this);
		if (true === btn.hasClass('disabled')) {
			return;
		}
		btn.addClass('disabled');
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
					btn.removeClass('disabled');
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

	$('.back-to-top').click(() => {
		$('body,html').animate({
			scrollTop: 0
		}, 600);
		return false;
	});

	$('.delete').on('click', function (event) {
		event.preventDefault();
		$('.delete-item').attr('href', $(this).attr('data-path'));
		$('.item-name').html($(this).attr('data-name'));
	});

	$('.show-annotation').on('click', function (event) {
		event.preventDefault();
		$('.annotation').text($(this).attr('data-annotation'));
	});

});