function status() {
	$.get(BASE + '/status', function(data) {
		var lbl = data == 'Running' ? 'label label-success' : 'label label-danger';
		$('#status').html($('<label>').addClass(lbl).text(data));
	});
}

function rescan() {
	$.get(BASE + '/rescan', function(data) {
		
	});
}

function restart() {
	$.get(BASE + '/restart', function(data) {
		
	});
}

$(document).ready(function() {
	status();
	setInterval(status,5000);

	$('#dlna-Rescan').on('click', function() { rescan(); });
	$('#dlna-Restart').on('click', function() { restart(); });
});