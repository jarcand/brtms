/**
 * Make an AJAX request to get the status of the seats.
 */
function getSeats() {
	$.ajax({
	  url: '${ROOT}/a/getseats',
	  type: 'GET',
	  dataType: 'json'
	}).done(loadSeats).fail(function(jqSHR, textStatus) {
		debug && alert(textStatus + ': ' + jqSHR.responseText);
	});
}

/**
 * Process the seat status results.
 * @param data - The AJAX response data.
 */
function loadSeats(data) {
	var i = 0;
	for (key in data) {
		var scope = function(seat, dname) {
			var func = function() {loadSeat(seat, dname);};
			setTimeout(func, 1000 + i * 100);
		};
		scope(key, data[key]);
		i++;
	}
}

/**
 * Show the specified seat as occupied.
 * @param seat - The seat number.
 * @param dname - The display name of the player.
 */
function loadSeat(seat, dname) {
	var s = $('#seat-' + seat);
	s.addClass('occ').removeClass('vac2');
	if (/Vacant$/.exec(s.attr('title'))) {
		s.attr('title', 'Taken by ' + dname);
	}
}

// Get the seats
getSeats();

