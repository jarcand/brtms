<?php

require_once dirname(__FILE__) . '/l/config.inc.php';
require_once dirname(__FILE__) . '/l/db.inc.php';
require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

function outputPage($msg = '') {
	global $db;
	
	$msg_src = !$msg ? '' : sPrintF('<tr><td class="error tac" colspan="2">%s</td></tr>', $msg);
	
	$r = @$_GET['r'];
	if (!$r) {
		$r = $_SERVER['HTTP_REFERER'];
	}
	
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
', $r, $msg_src);
	
	mp($src);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$r = @$_POST['r'];
	
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
	
	$token = subStr(sha1($config['SALT'] . '-session-' . strFTime('%Y-%m-%d %H:%M:%S')), 15, 20);
	
	if (!$db->query($sql = 'UPDATE `players` SET `lastlogints`=NOW(), `token`=' . s($token) . ' WHERE `pid`=' . s($pid))) {
		error($sql);
	}
	
	setCurrUser($token);
	
	if (!$r || preg_match('#/login$#', $r)) {
		$r = $config['ROOT'] . '/';
	}
	
	header('Location: ' . $r);
	
} else {
	
	outputPage();
}

