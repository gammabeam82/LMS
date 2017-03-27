$(document).ready(function() {
	$('.chosen-select').chosen();

	if($('upload')) {
		$('.upload').fileinput(
			{'showPreview':false}
		);
	}

});