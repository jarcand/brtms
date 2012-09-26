<?php

require_once 'l/config.inc.php';
require_once 'l/db.inc.php';
require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

function outputPage($msg = '') {
	global $db;
	
	$pids_html = '';
	$res = $db->query('SELECT `pid`, `fname`, `lname`, `email`
	  FROM `players` `p`');
	
	while ($p = $res->fetch_assoc()) {
		$pids_html .= sPrintF('<option value="%s">%s %s (%s)</option>',
		  $p['pid'], $p['fname'], $p['lname'], $p['email']);
	}

	$msg_src = !$msg ? '' : sPrintF('<tr><td class="error tac" colspan="2">%s</td></tr>', $msg);
	
	$src = sPrintF('
<form action="login" method="post">
<input type="hidden" name="r" value="%1$s" />
<fieldset class="center faded-bg" style="width:500px;">
<legend>Login</legend>
<table cellspacing="10" class="center" style="margin-bottom:-10px;margin-top:-10px;">
<col width="90" /><col />
%2$s
<tr><td>Username:</td><td><input type="text" name="user" /></td></tr>
<tr><td>Password:</td><td><input type="password" name="pass" /></td></tr>
<tr><td></td><td><input type="submit" value="Login" /></td></tr>
</table>
</fieldset>
</form>

<form action="login" method="post">
<input type="hidden" name="r" value="%1$s" />
<input type="hidden" name="dev" value="true" />
<fieldset class="center faded-bg" style="width:500px;">
<legend>Dev-Only</legend>
<table cellspacing="10" class="center">
%2$s
<tr><td><select name="pid"><option value="0">(None)</option>%3$s</select></td></tr>
<tr><td><input type="submit" value="Login" /></td></tr>
</table>
</fieldset>
</form>
', @$_GET['r'], $msg_src, $pids_html);
	
	mp($src);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$r = @$_POST['r'];
	
	if (@$_POST['dev']) {
		$pid = $_POST['pid'];
	} else {
		
		$username = $_POST['user'];
		$password = encodePassword($_POST['pass']);
		
		$res = $db->query($sql = 'SELECT `pid`, `password` FROM `players`
		  WHERE `firstlogints` IS NOT NULL AND `username`=' . s($username));
		if (!$res) {
			error($res);
		}
		$p = $res->fetch_assoc();
		
		if (!$p || $p['password'] != $password) {
			
			outputPage('Invalid username or password.');
			
		} else {
			$pid = $p['pid'];
		}
	}
	
	$token = subStr(sha1($config['SALT'] . '-session-' . strFTime('%Y-%m-%d %H:%M:%S')), 15, 20);
	
	if (!$db->query($sql = 'UPDATE `players` SET `lastlogints`=NOW(), `token`=' . s($token) . ' WHERE `pid`=' . s($pid))) {
		error($sql);
	}
	
	setCurrUser($token);
	
	if (!$r) {
		$r = $config['ROOT'] . '/';
	}
	
	header('Location: ' . $r);
	
} else {
	
	outputPage();
}

