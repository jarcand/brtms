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
	var i = 0;
	for (key in data) {
		var func = 'loadSeat("' + key + '","' + data[key] + '")';
		setTimeout(func, i * 200);
		i++;
		if (i > 10)
			break;
	}
}

function loadSeat(seat, dname) {
	$('#seat-' + seat).addClass('occ').removeClass('vac2');
	alert('#seat-' + seat + ': ' + $('#seat-' + seat).html());
}

getSeats();

