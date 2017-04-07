$(document).ready( () => {
	$('.chosen-select').chosen();
	if($('upload')) {
		$('.upload').fileinput(
			{'showPreview':false}
		);
	}
} );