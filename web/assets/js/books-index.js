$(document).ready( () => {
	$('.chosen-select').chosen();
	$('.datepicker').datetimepicker({
		format: 'DD.MM.YYYY',
		locale: 'ru'
	});
});