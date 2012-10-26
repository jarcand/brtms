function genTournament(t, detailed) {
	var players_src = 'Players: ' + t.players
	  + (t.teamsize > 1 ? ', Teams: ' + t.teams + ', Free Agents: ' + t.freeagents : '')
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
	
	var href = '${ROOT}/tournaments#tournament/' + (t.shortcode || t.tid);
	var src1 = '<table cellpadding="0" cellspacing="0"><tr><td>'
	  + '<img class="thumb" src="${ROOT}/imgs/game-'
          + (t.major == '1' && t.shortcode || 'default')
          + '.png" /></td><td><h2>'
          + (!detailed ? '<a href="' + href + '">' : '') + t.name + (!detailed ? '</a>' : '')
          + '</h2><p class="l1">'
	  + (t.major == '1' ? 'Major Tournament' : 'Crowdsourced Tournament')
	  + ' by ' + t.organizer + '</p><p class="l2">' + players_src
	  + '</p></td></tr></table>';
	var src2 = '<div class="join"><p class="underlim"><a href="#" '
	  + 'onclick="return joinTournament(' + t.tid + ');">JOIN</a></p>'
	  + '<p class="overlim" title="A.K.A. You have reached the maximum '
	  + 'Major Tournaments for your ticket type.">0 CREDITS,<br /> '
	  + 'INSERT<br /> TOKEN</p></div>'
	  + '<div class="joined"><p>JOINED <a href="#" '
	  + 'onclick="return leaveTournament(' + t.tid + ');">LEAVE</a></p>'
	  + (t.teamsize <= 1 ? '' : '<p class="teamaction"><span>Team Selected</span><a href="' + href
	    + '/jointeam">Join a Team</a></p>')
	  + '</div>';
	var src3 = '<h3>Description:</h3><p>' + t.desc + '</p>'
	  + '<h3>Prizes:</h3><p>' + t.prizes + '</p>';
	var src4 = '';
	
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
	var jointeamHighlight = false;
	if (hash.match(/^#tournament[/]/i)) {
		var m = new RegExp('^#tournament[/]([^/]+)([/](.*))?$', 'i').exec(hash);
		if (m[3] == 'jointeam') {
			jointeamHighlight = true;
		}
		showTournament(preloadData, m[1]);
	} else {
		showTournamentList(preloadData);
	}
	if (jointeamHighlight) {
		$('#tournamentDetails').addClass('highlight');
	} else {
		$('#tournamentDetails').removeClass('highlight');
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
	if (session) {
		src += '<div id="tournamentDetails"><h2>Loading Players List&hellip;</h2></div>';
		src += '<h2 class="dis">Discussion</h2><iframe id="disqusFrame" src="${ROOT}/disqus?tid='
		  + t.tid + '"></iframe>';
	} else {
		src += '<h2>Login to view players lists and discussions</h2>'
		  + '<ul><li><a href="${ROOT}/login">Login to Players Portal</a></li></ul>';
	}
	
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
	if (!session) {
		return;
	}
	$.ajax({
	  url: '${ROOT}/a/gettournamentdetails',
	  type: 'GET',
	  data: {
	  	tid: t.tid
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (t.teamsize > 1) {
			src += '<h2>Teams</h2><dl class="teams">';
			for (var i = 0; i < data.teams.length; i++) {
				src += genTeam(data.teams[i], t.joined, data.inteam);
			}
			if (!data.inteam && t.joined == '1') {
				src += '<dt><a href="#" onclick="return showCreateTeam(' + t.tid
				  + ',\'' + t.name + '\');">Create a New Team</a></dt>';
			}
			src += '</dl>';
			src += '<h2>Free Agents</h2>';
		} else {
			src += '<h2>Players</h2>';
		}
		src += '<ul class="inline">';
		for (var i = 0; i < data.players.length; i++) {
			src += '<li' + (data.players[i].you ? ' class="you"' : '') + '>'
			  + data.players[i].dname + '</li>';
		}
		if (data.players.length == 0) {
			src += '<li><em>No free agents</em></li>';
		}
		src += '</ul>';
		$('#tournamentDetails').html(src);
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
}

function genTeam(team, t_joined, t_inteam) {
	var is_leader = team.is_leader == '1'
	var src = '<dt><strong>Team ' + team.name + (team.is_leader ? ' (your team)' : '') + '</strong>';
	if (team.teamsize > team.members.length) {
		src += ' &ndash; ' + (team.open == '1' ? 'OPEN team' : 'Closed team');
		if (t_joined == '1' && !t_inteam) {
			src += ' &ndash; <a href="#" onclick="return joinTeam(' + team.gid
			    + ');">Join Team</a>';
		}
		if (team.open != '1') {
			src += ' (Only if you know the team)';
		}
	}
	if (is_leader) {
		src += ' &ndash; <a href="#" onclick="return deleteTeam('
		  + team.gid + ');">Delete Team</a>';
	}
	src += '</dt>';
	for (var j = 0; j < team.members.length; j++) {
		var removeLink = !is_leader ? ''
		  : ' <small><a href="#" onclick="return removeTeamPlayer('
		  + team.gid + ',' + team.members[j].pid + ');">[Remove]</a></small>'
		src += '<dd' + (team.members[j].you ? ' class="you"' : '') + '>' + team.members[j].dname
		  + (team.members[j].you ? ' <small>[You]</small>' : removeLink)
		  + '</dd>';
	}
	if (team.teamsize > team.members.length) {
		src += '<dd><em>' + (team.teamsize - team.members.length) + ' vacant spots</em></dd>';
	}
	if (!is_leader && team.inteam) {
		src += ' &ndash; <a href="#" onclick="return removeTeamPlayer(' + team.gid
		    + ',\'me\');">Leave Team</a>';
	}
	return src;
}

var major_limit = 0;
function showMyTeams(data) {
	if (!session) {
		return;
	}
	var major_c = 0, crowd_c = 0;
	if (data.result != 'error') {
		for (var tid in data.myteams) {
			data.myteams[tid].major == '1' ? major_c++ : crowd_c++;
			$('#tour' + tid + ' .join, #tour' + tid + 'det .join').hide();
			$('#tour' + tid + ' .joined, #tour' + tid + 'det .joined').show();
			if (data.myteams[tid].gid) {
				$('#tour' + tid + ' .teamaction a, #tour' + tid + 'det .teamaction a').hide();
				$('#tour' + tid + ' .teamaction span, #tour' + tid + 'det .teamaction span').show();
			} else {
				$('#tour' + tid + ' .teamaction a, #tour' + tid + 'det .teamaction a').show();
				$('#tour' + tid + ' .teamaction span, #tour' + tid + 'det .teamaction span').hide();
			}
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

function reloadTournaments() {
	$.ajax({
	  url: '${ROOT}/a/gettournaments',
	  type: 'POST',
	  dataType: 'json'
	}).done(updateTournaments
	).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
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
	var frm = $('#createForm form').get(0);
	frm.subbtn.disabled = false;
	frm.tname.focus();
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
	frm.subbtn.disabled = true;
	
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

//-- [ Create Team ] -----------------------------------------------------------

var popup2;
function showCreateTeam(tid, tname) {
	popup2 = $('#createTeamForm').bPopup({
		onClose: function() {$('#createTeamForm form').each(function() {this.reset();});}
	});
	var frm = $('#createTeamForm form').get(0);
	frm.tid.value = tid;
	frm.tournament.value = tname;
	frm.subbtn.disabled = false;
	frm.tname.focus();
	return false;
}

function createTeam(frm) {
	if (!session) {
		alert('You need to login or purchase a BR6 ticket!');
		return false;
	}
	
	$(frm).find('input, textarea').removeClass('invalid');
	$(frm).find('p.error').remove();
	
	var error = false;
	if (frm.tname.value.trim().length < 3) {
		frm.tname.focus();
		$(frm.tname).addClass('invalid');
		$(frm.tname).after('<p class="error">Please specify a longer team name.</p>');
		error = true;
	}
	if (error) {
		$(frm.subbtn).after('<p class="error">There were errors in your submission.</p>');
		return false;
	}
	frm.subbtn.disabled = true;
	
	$.ajax({
	  url: '${ROOT}/a/createteam',
	  type: 'POST',
	  data: {
	  	tid: frm.tid.value,
	  	tname: frm.tname.value,
	  	open: frm.open.value,
	  	notes: frm.notes.value
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		var src = '';
		if (data.result == 'success') {
			reloadTournaments();
		}
		if (popup2 && popup2.close) {
			popup2.close();
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function deleteTeam(gid) {
	if (!session) {
		return false;
	}
	
	$.ajax({
	  url: '${ROOT}/a/deleteteam',
	  type: 'POST',
	  data: {
	  	gid: gid
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		if (data.result == 'success') {
			reloadTournaments();
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function joinTeam(gid) {
	if (!session) {
		return false;
	}
	
	$.ajax({
	  url: '${ROOT}/a/jointeam',
	  type: 'POST',
	  data: {
	  	gid: gid
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		if (data.result == 'success') {
			reloadTournaments();
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

function removeTeamPlayer(gid, pid) {
	if (!session) {
		return false;
	}
	
	$.ajax({
	  url: '${ROOT}/a/removeteamplayer',
	  type: 'POST',
	  data: {
	  	gid: gid,
	  	pid: pid
	  },
	  dataType: 'json'
	}).done(function(data, sts) {
		if (data.result == 'success') {
			reloadTournaments();
		} else {
			alert(data.result + ': ' + data.errorType); //TODO
		}
	}).fail(function(jqSHR, textStatus) {
		alert(textStatus + ': ' + jqSHR.responseText); //TODO
	});
	
	return false;
}

