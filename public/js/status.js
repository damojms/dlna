function status() {
	$.get(BASE + '/status', function(data) {
		var lbl = data == 'Running' ? 'label label-success' : 'label label-danger';
		$('#status').html($('<label>').addClass(lbl).text(data));
	});
}

$(document).ready(function() {
	status();
	setInterval(status,5000);
});