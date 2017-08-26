$(document).ready(() => {
	$('.chosen-select').chosen({
		"disable_search": true
	});

	const progressContainer = $('.progress');
	const minLength = progressContainer.attr('data-min');
	const maxLength = progressContainer.attr('data-max');
	const progressBar = $('.progress-bar');
	const percent = Math.round(maxLength / 100);

	let checkMessage = () => {
		let ln = $('#comment_message').val().length;
		progressBar.text(ln + "/" + maxLength);
		if (ln >= minLength && ln <= maxLength) {
			if (false === progressBar.hasClass("progress-bar-success")) {
				progressBar.addClass("progress-bar-success");
				progressBar.removeClass("progress-bar-danger");
			}
		} else if (ln < minLength || ln > maxLength) {
			if (false === progressBar.hasClass("progress-bar-danger")) {
				progressBar.addClass("progress-bar-danger");
				progressBar.removeClass("progress-bar-success");
			}
		}

		let progressBarWidth = Math.round(ln / percent);

		if (progressBarWidth < 6) {
			progressBarWidth = 6;
		}

		if (ln < maxLength) {
			progressBar.css("width", progressBarWidth + "%");
		} else {
			progressBar.css("width", "100%");
		}
	}


	checkMessage();
	$('#comment_message').on('input', checkMessage);

	$('.edit-comment').on('click', function (event) {
		event.preventDefault();

		let formContainer = $('.comment-form-container');

		formContainer.html($('.spinner').html());

		$.get($(this).attr('data-path')).then(
			(data) => formContainer.html(data)
		);
	});

	$('.like-button').on('click', function (event) {
		event.preventDefault();

		let icon = $(this).find('i');

		$.get($(this).attr('href')).then(
			(data) => {
				if (typeof data['hasLike'] !== 'undefined') {
					icon.toggleClass('glyphicon-heart');
					icon.toggleClass('glyphicon-heart-empty');
				}
			}
		);
	});

});