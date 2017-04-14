$(document).ready( () => {
	$('.chosen-select').chosen({
		allow_single_deselect: true
	});
	$('.datepicker').datetimepicker({
		format: 'DD.MM.YYYY',
		locale: 'ru'
	});
});