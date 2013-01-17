function getSeats() {
	$.ajax({
	  url: '${ROOT}/a/getseats',
	  type: 'GET',
	  dataType: 'json'
	}).done(loadSeats).fail(function(jqSHR, textStatus) {
		debug && alert(textStatus + ': ' + jqSHR.responseText);
	});
}

function loadSeats(data) {
	var src = '';
	for (key in data) {
		alert(key);
		break;
	}
}

getSeats();

