$(function () {
	$('#primary_navigation select').change(function () {
		var value = $(this).val();
		if (value) {
			location.href = value;
		}
	});
});