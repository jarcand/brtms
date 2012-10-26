
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
				debug && alert(data.result + ': ' + data.errorType);
			}
		} else {
			debug && alert(data.result + ': ' + data.errorType);
		}
	}).fail(function(jqSHR, textStatus) {
		debug && alert(textStatus + ': ' + jqSHR.responseText);
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
				debug && alert(data.result + ': ' + data.errorType);
			}
		} else {
			debug && alert(data.result + ': ' + data.errorType);
		}
	}).fail(function(jqSHR, textStatus) {
		debug && alert(textStatus + ': ' + jqSHR.responseText);
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
			debug && alert(data.result + ': ' + data.errorType);
		}
	}).fail(function(jqSHR, textStatus) {
		debug && alert(textStatus + ': ' + jqSHR.responseText);
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
			debug && alert(data.result + ': ' + data.errorType);
		}
	}).fail(function(jqSHR, textStatus) {
		debug && alert(textStatus + ': ' + jqSHR.responseText);
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
			debug && alert(data.result + ': ' + data.errorType);
		}
	}).fail(function(jqSHR, textStatus) {
		debug && alert(textStatus + ': ' + jqSHR.responseText);
	});
	
	return false;
}

