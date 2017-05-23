$(document).ready( () => {
	$('.chosen-select').chosen({
		allow_single_deselect: true
	});

	const upload = $('.upload');

	let file = upload.attr('data-file') ? upload.attr('data-file') : '';

	upload.fileinput({
		'showPreview':false,
		'initialCaption': file
	});

	let $collectionHolder;

	let $addFileLink = $('.add_file_link');
	let $newLinkDiv = $('<div></div>');

	$collectionHolder = $('div.files');

	$collectionHolder.find('div').each(function() {
		addFileFormDeleteLink($(this));
	});

	$collectionHolder.append($newLinkDiv);

	$collectionHolder.data('index', $collectionHolder.find(':input').length);

	$addFileLink.on('click', function(e) {
		alert('fff');
		e.preventDefault();
		addFileForm($collectionHolder, $newLinkDiv);
	});

	function addFileForm($collectionHolder, $newLinkDiv) {
		let prototype = $collectionHolder.data('prototype');
		let index = $collectionHolder.data('index');

		let newForm = prototype.replace(/__name__/g, index);

		$collectionHolder.data('index', index + 1);

		let $newFormDiv = $('<div></div>').append(newForm);
		$newLinkDiv.before($newFormDiv);

		addFileFormDeleteLink($newFormDiv);

		$('.upload').fileinput({
			'showPreview':false,
			'initialCaption': file
		});
	}

	function addFileFormDeleteLink($fileFormDiv) {
		let $removeFormA = $('<a href="#" class="remove_file_link pull-right"><i class="glyphicon glyphicon-remove file-control"></i></a><br/>');
		$fileFormDiv.append($removeFormA);

		$removeFormA.on('click', function(e) {
			e.preventDefault();
			$fileFormDiv.remove();
		});
	}

} );