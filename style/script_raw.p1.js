function genTournament(t, detailed) {
	var players_src = 'Players: ' + t.players
	  + (t.teamsize > 1 ? ', Teams: <em>Coming Soon</em>' /*+ t.teams*/ : '')
	  + (t.published == '0' ? ', <em>Awaiting Approval</em>' : '');
	if ($('#tour' + t.tid + 'det').length) {
		$('#tour' + t.tid + 'det .l2').html(players_src);
	}
	if ($('#tour' + t.tid).length) {
		$('#tour' + t.tid + ' .l2').html(players_src);
		if (!detailed) {
			return '';
		}
	}
	
	src1 = '<table cellpadding="0" cellspacing="0"><tr><td>'
	  + '<img class="thumb" src="${ROOT}/imgs/game-'
          + (t.major == '1' && t.shortcode || 'default')
          + '.png" /></td><td><h2>'
          + (!detailed ? '<a href="${ROOT}/tournaments#tournament/'
          + (t.shortcode || t.tid) + '">' : '') + t.name + (!detailed ? '</a>' : '')
          + '</h2><p class="l1">'
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
	  + (t.major == '1' ? 'major' : 'crowd') + '" id="tour' + t.tid
	  + (detailed ? 'det' : '') + '">'
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

var tournamentsPageInited = false;
var titlebase = document.title;
function tournamentsPageInit() {
	if (!tournamentsPageInited) {
		tournamentsPageInited = true;
		$(window).hashchange(tournamentsPageInit);
		var tp = parseInt($('#registration-overview').css('top'));
		$(window).scroll(function() {
			$('#registration-overview').css('top',
			  Math.max(tp - $(document).scrollTop(), 50));
		});
	}
	var hash = location.hash;
	if (hash.match(/^#tournament[/]/i)) {
		var tourid = hash.substr(12);
		showTournament(preloadData, tourid);
	} else {
		showTournamentList(preloadData);
	}
	showMyTeams(preloadData);
}

var gtourid = '';
function updateTournaments(data) {
	preloadData = data;
	if (gtourid) {
		showTournament(data, gtourid);
	} else {
		showTournamentList(data);
	}
	showMyTeams(data);
}

function showTournamentList(data) {
	var src = '';
	for (var i = 0; i < data.tournaments.length; i++) {
		src += genTournament(data.tournaments[i]);
	}
	document.title = titlebase + ' - Tournaments List';
	$('#header1').html('Tournaments List');
	$('#tournamentsListContent').show();
	$('#tournamentContent').hide();
	$('#tournamentsListDynContent').append(src);
	$('.c11, .c12, .c13, .c21, .c22, .c23, .c31, .c32, .c33')
	  .css('cursor', 'pointer').click(function() {
		document.location = $(this).parent().parent().find('h2 a').attr('href');
	});
	gtourid = '';
}

function showTournament(data, tourid) {
	var t;
	for (var i = 0; i < data.tournaments.length; i++) {
		if (data.tournaments[i].tid == tourid
		  || data.tournaments[i].shortcode == tourid) {
			t = data.tournaments[i];
		}
	}
	if (!t) {
		return;
	}
	var src = genTournament(t, true);
	src += '<div id="tournamentDetails"></div>';
	src += '<h2 class="dis">Discussion</h2><iframe id="disqusFrame" src="${ROOT}/disqus?tid='
	  + t.tid + '"></iframe>';
	
	$(document).scrollTop(0);
	document.title = titlebase + ' - Tournament: ' + t.name;
	$('#header1').html('Tournament: ' + t.name);
	$('#tournamentContent').show();
	$('#tournamentsListContent').hide();
	$('#tournamentDynContent').html(src);
	getTournamentDetails(t);
	gtourid = tourid;
}

function getTournamentDetails(t) {
	$.ajax({
	  url: '${ROOT}/a/gettournamentdetails',
	  type: 'GET',
	  data: {
	  	tid: t.tid
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		if (t.teamsize > 1) {
			var src = '<h2>Teams</h2><ul>';
			for (var i = 0; i < data.teams.length; i++) {
				src += '<li><strong>' + data.teams[i].name + ':</strong> '
				  + data.teams[i].members.join(', ') + '</li>';
			}
			src += '</ul>';
			src += '<h2>Free Agents</h2><p>' + data.players.join(', ') + '</p>';
		} else {
			var src = '<h2>Players</h2><p>' + data.players.join(', ') + '</p>';
		}
		$('#tournamentDetails').html(src);
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
}

var major_limit = 0;
function showMyTeams(data) {
	var major_c = 0, crowd_c = 0;
	if (data.result != 'error') {
		for (var tid in data.myteams) {
			data.myteams[tid].t_major == '1' ? major_c++ : crowd_c++;
			$('#tour' + tid + ' .join, #tour' + tid + 'det .join').hide();
			$('#tour' + tid + ' .joined, #tour' + tid + 'det .joined').show();
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
	major_limit = data.limit;
}


//-- [ Joining and Leaving Tournaments ] ---------------------------------------

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
			$('#tour' + tid + ' .join, #tour' + tid + 'det .join').hide();
			$('#tour' + tid + ' .joined, #tour' + tid + 'det .joined').show();
		} else if (data.result == 'error' && data.errorType == 'overlimit') {
			alert('You have exceeded your limit of ' + major_limit + ' Major Tournaments.');
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
		updateTournaments(data);
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
			$('#tour' + tid + ' .joined, #tour' + tid + 'det .joined').hide();
			$('#tour' + tid + ' .overlim, #tour' + tid + 'det .overlim').hide();
			$('#tour' + tid + ' .underlim, #tour' + tid + 'det .underlim').show();
			$('#tour' + tid + ' .join, #tour' + tid + 'det .join').show();
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
		updateTournaments(data);
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}


//-- [ Create Tournament ] -----------------------------------------------------

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
