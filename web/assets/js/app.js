$(document).ready(() => {

	const booksCountContainer = $('.books-count');
	const errorMessage = $('.common-messages-error').text();
	const backToTop = $('.back-to-top');
    const usersCountContainer = $('.users-online-container');
    const usersLink = $('.users-online');

	const hl = $('.highlight-value');
	const hl2 = $('.highlight-value-hidden');

	if(typeof hl.val() !== 'undefined') {
		if(false === hl.closest('div').hasClass('has-error')) {
			$('.highlight').mark(hl.val(), {});
		}
	}

	if(typeof hl2.val() !== 'undefined') {
		$('.highlight-2').mark(hl2.val(), {});
	}

	$('.navbar-fixed-top').autoHidingNavbar({
		'showOnBottom': false
	});

	$(document).ajaxError((event, xhr, options) => {
		$.notify(errorMessage, {type: 'danger'});
		console.log(`status: ${xhr.status} \nmessage: ${xhr.statusText} \nurl: ${options.url}`);
	});

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
				}
			})
            .catch(function (error) {
                btn.removeClass('disabled');
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
				}
			})
            .catch(function (error) {
                btn.removeClass('disabled');
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
		$('body,html').animate({
			scrollTop: 0
		}, 600);
		return false;
	});

	$('.delete, .remove_stored_file').on('click', function (event) {
		event.preventDefault();
		$('.delete-item').attr('href', $(this).attr('data-path'));
		$('.item-name').html($(this).attr('data-name'));
	});

	$('.show-annotation').on('click', function (event) {
		event.preventDefault();
		$('.annotation').text($(this).attr('data-annotation'));
	});

	$('.export').click(function() {
		setTimeout(() => location.reload(), 3500);
	});

	setInterval(function () {
        $.getJSON(usersCountContainer.attr('data-path'))
            .then((data) => {
                if (typeof data['usersCount'] !== 'undefined') {
                    usersCountContainer.html(data['usersCount'])
                }
            });
    }, 60500);

	usersLink.on('click', function (event) {
	    event.preventDefault();

        $.getJSON($(this).attr('data-path'))
            .then((data) => {
               if(data.length > 0) {
               	//TODO users list
               }
            });
    });

});
