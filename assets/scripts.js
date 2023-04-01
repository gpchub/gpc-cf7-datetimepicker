;(function ($) {
	$(document).ready(function () {
		let lang = $('html').attr('lang');
		$.datetimepicker.setLocale(lang);

		$('.gpc-cf7-datetimepicker').each(function(index, element) {
			$(this).datetimepicker({
				dayOfWeekStart : 1,
				yearStart: '1900',
				format:'d-m-Y H:i',
				formatDate:'d-m-Y',
				formatTime:'H:i',
				defaultTime:'08:00',
				validateOnBlur: false,
				onGenerate: function( ct, $input ){
					$input.prop('readonly', true);
				}
			});
		});

		$('.gpc-cf7-datepicker').each(function(index, element) {
			$(this).datetimepicker({
				dayOfWeekStart : 1,
				yearStart: '1900',
				timepicker:false,
				format:'d-m-Y',
				formatDate:'d-m-Y',
				validateOnBlur: false,
				onGenerate: function( ct, $input ){
					$input.prop('readonly', true);
				}
			});
		});
	});
}(jQuery));

