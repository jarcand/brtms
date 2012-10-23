function genTournament(t, detailed) {
	var players_src = 'Players: ' + t.players
	  + (t.teamsize > 1 ? ', Teams: <em>Coming Soon</em>' /*+ t.teams*/ : '')
	  + (t.published == '0' ? ', <em>Awaiting Approval</em>' : '');
	if ($('#tour' + t.tid).length) {
		$('#tour' + t.tid + ' .l2').each(function() {$(this).html(players_src);});
		return '';
	}
	
	src1 = '<table cellpadding="0" cellspacing="0"><tr><td>'
	  + '<img class="thumb" src="${ROOT}/imgs/game-'
          + (t.major == '1' && t.shortcode || 'default')
          + '.png" /></td><td><h2><a href="${ROOT}/tournament/'
          + (t.shortcode || t.tid) + '">' + t.name + '</a></h2><p class="l1">'
	  + (t.major == '1' ? 'Major Tournament' : 'Crowdsourced Tournament')
	  + ' by ' + t.organizer + '</p><p class="l2">' + players_src
	  + '</p></td></tr></table>';
	src2 = '<div class="join"><p class="underlim"><a href="#" '
	  + 'onclick="return joinTournament(' + t.tid + ');">JOIN</a></p>'
	  + '<p class="overlim" title="A.K.A. You have reached the maximum '
	  + 'Major Tournaments for your ticket type.">0 CREDITS,<br /> '
	  + 'INSERT<br /> TOKEN</p></div>'
	  + '<p class="joined">JOINED <a href="#" '
	  + 'onclick="return leaveTournament(' + t.tid + ');">LEAVE</a></p>';
	src3 = '<h3>Description:</h3><p>' + t.desc + '</p>'
	  + '<h3>Prizes:</h3><p>' + t.prizes + '</p>';
	src4 = '';
	
	var src = '<table cellspacing="0" class="tour '
	  + (t.major == '1' ? 'major' : 'crowd') + '" id="tour' + t.tid + '">'
	  + '<tr class="r1"><td class="c11"></td><td class="c12"></td>'
	  + '<td class="c13"></td><td class="c14">'
	  + '</td><td class="c15"></td></tr>'
	  + '<tr class="r2"><td class="c21"></td><td class="c22">' + src1
	  + '</td><td class="c23"></td><td class="c24">' + src2
	  + '</td><td class="c25"></td></tr>'
	  + '<tr class="r3"><td class="c31"></td><td class="c32"></td>'
	  + '<td class="c33"></td><td class="c34">'
	  + '</td><td class="c35"></td></tr>';
	if (detailed) {
		src += '<tr class="r4"><td class="c41"></td><td class="c42">'
		  + src3 + '</td><td class="c43"></td><td class="c44">' + src4
		  + '</td><td class="c45"></td></tr>'
		  + '<tr class="r5"><td class="c51"></td><td class="c52"></td>'
		  + '<td class="c53"></td><td class="c54">'
		  + '</td><td class="c55"></td></tr>'
	}
	src += '</table>';
	
	return src;
}

function loadTournaments() {
	var callback = tournament_list ? showTournamentList : showTournament;
	$.ajax({
	  url: '${ROOT}/a/gettournaments',
	  type: 'GET',
	  dataType: 'json'
	}).done(callback
	).fail(function(jqSHR, textStatus) {
		alert(textStatus); //TODO
	});
}

function showTournamentList(data, sts) {
	var src = '';
	for (var i = 0; i < data.tournaments.length; i++) {
		src += genTournament(data.tournaments[i]);
	}
	$('#tournaments .loading').hide();
	$('#tournaments').append(src);
	$('.c11, .c12, .c13, .c21, .c22, .c23, .c31, .c32, .c33')
	  .css('cursor', 'pointer').click(function() {
		document.location = $(this).parent().parent().find('h2 a').attr('href');
	});
	loadMyTeams();
}

function showTournament(data, sts) {
	var src = '';
	for (var i = 0; i < data.tournaments.length; i++) {
		if (data.tournaments[i].tid == tid) {
			src += genTournament(data.tournaments[i], true);
		}
	}
	$('#tournaments .loading').hide();
	$('#tournaments').append(src);
	loadMyTeams();
}

var major_limit = 0;
var registrationOverviewScroll = false;
function loadMyTeams() {
	if (!session) {
		return;
	}
	
	$.ajax({
	  url: '${ROOT}/a/getmyteams',
	  type: 'GET',
	  dataType: 'json'
	}).done(function(data, sts) {
		var major_c = 0, crowd_c = 0;
		if (data.result != 'error') {
			for (var tid in data.myteams) {
				data.myteams[tid].t_major == '1' ? major_c++ : crowd_c++;
				$('#tour' + tid + ' .join').hide();
				$('#tour' + tid + ' .joined').show();
			}
		}
		var src = '<ul><li class="bg"><strong>Joined Tournaments</strong></li>'
		 + '<li><big>' + major_c + ' of ' + data.limit_s
		 + '</big><br /> Major Tournaments</li>'
		 + '<li><big>' + crowd_c + '</big><br /> Crowdsourced Tournaments</li>'
		 + '</ul>';
		if (major_c >= data.limit) {
			$('.tour.major .underlim').hide();
			$('.tour.major .overlim').show();
		} else {
			$('.tour.major .overlim').hide();
			$('.tour.major .underlim').show();
		}
		$('#registration-overview').html(src);
		var tp = parseInt($('#registration-overview').css('top'));
		if (!registrationOverviewScroll) {
			registrationOverviewScroll = true;
			$(window).scroll(function() {
				$('#registration-overview').css('top',
				  Math.max(tp - $(window).scrollTop(), 50));
			});
		}
		major_limit = data.limit;
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus); //TODO
	});
}

var popup;
function showCreate() {
	popup = $('#createForm').bPopup({
		onClose: function() {$('#createForm form').each(function() {this.reset();});}
	});
	$('#createForm input').get(1).focus();
	return false;
}

function createTournament(frm) {
	if (!session) {
		alert('You need to login or purchase a BR6 ticket!');
		return false;
	}
	
	$(frm).find('input, textarea').removeClass('invalid');
	$(frm).find('p.error').remove();
	
	var error = false;
	if (frm.desc.value.trim().length < 3) {
		frm.desc.focus();
		$(frm.desc).addClass('invalid');
		$(frm.desc).after('<p class="error">Please provide a description of the tournament.</p>');
		error = true;
	}
	if (frm.game.value.trim().length < 3) {
		frm.game.focus();
		$(frm.game).addClass('invalid');
		$(frm.game).after('<p class="error">Please specify the game that the tournament is based on.</p>');
		error = true;
	}
	if (frm.tname.value.trim().length < 3) {
		frm.tname.focus();
		$(frm.tname).addClass('invalid');
		$(frm.tname).after('<p class="error">Please specify a longer tournament name.</p>');
		error = true;
	}
	if (error) {
		$(frm.subbtn).after('<p class="error">There were errors in your submission.</p>');
		return false;
	}
	
	$.ajax({
	  url: '${ROOT}/a/createtournament',
	  type: 'POST',
	  data: {
	  	tname: frm.tname.value,
	  	game: frm.game.value,
	  	desc: frm.desc.value,
	  	prizes: frm.prizes.value,
	  	teamsize: frm.teamsize.value,
	  	notes: frm.notes.value
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			src += genTournament(data.tournaments[0]);
		}
		$('#tournaments').append(src);
		if (popup && popup.close) {
			popup.close();
		}
		joinTournament(data.tournaments[0].tid);
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function joinTournament(tid) {
	if (!session) {
		alert('You need to login or purchase a BR6 ticket!');
		return false;
	}
	$.ajax({
	  url: '${ROOT}/a/jointournament',
	  type: 'POST',
	  data: {
	  	tid: tid
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			$('#tour' + tid + ' .join').hide();
			$('#tour' + tid + ' .joined').show();
		} else if (data.result == 'error' && data.errorType == 'overlimit') {
			alert('You have exceeded your limit of ' + major_limit + ' Major Tournaments.');
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
		loadTournaments();
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function leaveTournament(tid) {
	$.ajax({
	  url: '${ROOT}/a/leavetournament',
	  type: 'POST',
	  data: {
	  	tid: tid
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			$('#tour' + tid + ' .joined').hide();
			$('#tour' + tid + ' .overlim').hide();
			$('#tour' + tid + ' .underlim').show();
			$('#tour' + tid + ' .join').show();
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
		loadTournaments();
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus); //TODO
	});
	
	return false;
}

function createAccount(frm) {
	$(frm).find('input').removeClass('invalid');
	$(frm).find('p.error').remove();
	
	var error = false;
	if (frm.pass2.value.trim() != frm.pass1.value.trim()) {
		frm.pass2.focus();
		$(frm.pass2).addClass('invalid');
		$(frm.pass2).after('<p class="error">Your passwords did not match.</p>');
		error = true;
	}
	if (frm.pass1.value.trim().length < 8) {
		frm.pass1.focus();
		$(frm.pass1).addClass('invalid');
		$(frm.pass1).after('<p class="error">Your password is too short.</p>');
		error = true;
	}
	if (!frm.user.value.match(/^[A-Z0-9._+-@]+$/i)) {
		frm.user.focus();
		$(frm.user).addClass('invalid');
		$(frm.user).after('<p class="error">Your username is not valid: only A-Z, 0-9,<br /> <code>. - + @ _</code> are valid.</p>');
		error = true;
	}
	if (!frm.email.value.match(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i)) {
		frm.email.focus();
		$(frm.email).addClass('invalid');
		$(frm.email).after('<p class="error">Your email address does not appear to be valid.</p>');
		error = true;
	}
	if (frm.dname.value.trim().length == 0) {
		frm.dname.focus();
		$(frm.dname).addClass('invalid');
		$(frm.dname).after('<p class="error">Your display name must not be blank.</p>');
		error = true;
	}
	if (error) {
		$(frm.subbtn).after('<p class="error">There were errors in your submission.</p>');
		return false;
	}
	
	$.ajax({
	  url: '${ROOT}/a/createaccount',
	  type: 'POST',
	  data: {
	  	tok: frm.tok.value,
	  	dname: frm.dname.value,
	  	email: frm.email.value,
	  	user: frm.user.value,
	  	pass: frm.pass1.value
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			document.location = data.redirect;
		} else if (data.result == 'invalid') {
			if (data.field == 'dname') {
				frm.dname.focus();
				$(frm.dname).addClass('invalid');
				$(frm.dname).after('<p class="error">That display name is already in use.</p>');
			} else if (data.field == 'user') {
				frm.user.focus();
				$(frm.user).addClass('invalid');
				$(frm.user).after('<p class="error">That username is already in use.</p>');
			} else {
				alert(data.result + ': ' + data.errorType); //TODO
			}
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function updateDname(frm) {
	$(frm).find('input').removeClass('invalid');
	$(frm).find('p.error').remove();
	
	var error = false;
	if (frm.dname.value.trim().length == 0) {
		frm.dname.focus();
		$(frm.dname).addClass('invalid');
		$(frm.dname).after('<p class="error">Your display name must not be blank.</p>');
		error = true;
	}
	if (error) {
		$(frm.subbtn).after('<p class="error">There were errors in your submission.</p>');
		return false;
	}
	
	$.ajax({
	  url: '${ROOT}/a/changedname',
	  type: 'POST',
	  data: {
	  	dname: frm.dname.value.trim()
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			alert('Your display name has been successfully changed.');
			document.location.reload();
		} else if (data.result == 'invalid') {
			if (data.field == 'dname') {
				frm.dname.focus();
				$(frm.dname).addClass('invalid');
				$(frm.dname).after('<p class="error">That display name is already in use.</p>');
			} else {
				alert(data.result + ': ' + data.errorType); //TODO
			}
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function chooseSeat(frm) {
	var seat = '';
	for (var i = 0; i < frm.seat.length; i++) {
		if (frm.seat[i].checked) {
			seat = frm.seat[i].value;
			break;
		}
	}
	
	$.ajax({
	  url: '${ROOT}/a/chooseseat',
	  type: 'POST',
	  data: {
	  	seat: seat
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			frm.reset();
			$(frm).find('input').attr('readonly', 'readonly');
			document.location.reload();
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function releaseSeat() {
	$.ajax({
	  url: '${ROOT}/a/chooseseat',
	  type: 'POST',
	  data: {
	  	seat: 'release'
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			document.location.reload();
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
}

function changePassword(frm) {
	$(frm).find('input').removeClass('invalid');
	$(frm).find('p.error').remove();
	
	var error = false;
	if (frm.pass2.value.trim() != frm.pass1.value.trim()) {
		frm.pass2.focus();
		$(frm.pass2).addClass('invalid');
		$(frm.pass2).after('<p class="error">Your new passwords did not match.</p>');
		error = true;
	}
	if (frm.pass1.value.trim().length < 8) {
		frm.pass1.focus();
		$(frm.pass1).addClass('invalid');
		$(frm.pass1).after('<p class="error">Your password is too short.</p>');
		error = true;
	}
	if (error) {
		$(frm.subbtn).after('<p class="error">There were errors in your submission.</p>');
		return false;
	}
	
	$.ajax({
	  url: '${ROOT}/a/changepwd',
	  type: 'POST',
	  data: {
	  	curpwd: frm.curpwd.value,
	  	newpwd: frm.pass1.value.trim()
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			alert('Your password has been successfully changed.');
			frm.reset();
			document.location.reload();
		} else if (data.result == 'error' && data.errorType == 'invalidPassword') {
			frm.curpwd.focus();
			$(frm.curpwd).addClass('invalid');
			$(frm.curpwd).after('<p class="error">The password you entered does not match our records.</p>');
			$(frm.subbtn).after('<p class="error">There were errors in your submission.</p>');
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

