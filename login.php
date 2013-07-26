<?php

/**
 * Login the user or show the login page.
 */

require_once dirname(__FILE__) . '/l/config.inc.php';
require_once dirname(__FILE__) . '/l/db.inc.php';
require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

/**
 * Output the login page/form with the specified error message.
 * @param $msg default('') - The error message to display.
 */
function outputPage($msg = '') {
	global $db;
	
	$msg_src = !$msg ? '' : sPrintF('<tr><td class="error tac" colspan="2">%s</td></tr>', $msg);
	
	$r = @$_GET['r'];
	if (!$r) {
		$r = $_SERVER['HTTP_REFERER'];
	}
	
	$user = @$_GET['user'];
	
	$src = sPrintF('
<form action="login" method="post">
<input type="hidden" name="r" value="%1$s" />
<fieldset class="center faded-bg" style="width:500px;">
<legend>Login</legend>
<table cellspacing="10" class="center" style="margin-bottom:-10px;margin-top:-10px;">
<col width="90" /><col />
%2$s
<tr><td>Username:</td><td><input type="text" name="user" value="%3$s" /></td></tr>
<tr><td>Password:</td><td><input type="password" name="pass" /><br />
 <small><em>Forgot password?  Contact <a href="mailto:accounts&#64;battleroyale.ca">Accounts</a> for assistance.</em></small></td></tr>
<tr><td></td><td><input type="submit" value="Login" /></td></tr>
</table>
</fieldset>
</form>
', $r, $msg_src, htmlEntities($user));
	
	mp($src, 'Login');
}

// Check if the request is a POST which would be a form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Get the URL of the original page requested
	$r = @$_POST['r'];
	
	// Get the user's information
	$username = $_POST['user'];
	$password = encodePassword($_POST['pass']);
	
	// Check if we can find the user (and they've accepted the invitation)
	$res = $db->query($sql = 'SELECT `pid`, `password` FROM `players`
	  WHERE `firstlogints` IS NOT NULL AND `username`=' . s($username));
	if (!$res) {
		error($res);
	}
	$p = $res->fetch_assoc();
	
	// Either didn't find the user, or their password is wrong
	if (!$p || $p['password'] != $password) {
		
		outputPage('Invalid username or password.');
		
	} else {
		
		// Found the user
		$pid = $p['pid'];
	}
	
	// Generate a session token for the user
	$token = subStr(sha1($config['SALT'] . '-session-' . strFTime('%Y-%m-%d %H:%M:%S')), 15, 20);
	
	// Save the token to the database
	// Note: This mechanism only allows one active session per user.
	// TODO: Change the mechanism to allow multiple user sessions.
	if (!$db->query($sql = 'UPDATE `players`
	  SET `lastlogints`=NOW(), `token`=' . s($token) . '
	  WHERE `pid`=' . s($pid))) {
		error($sql);
	}
	
	// Set the current user
	setCurrUser($token);
	
	// If the form didn't specify a valid redirect URL, redirect to the home page
	if (!$r || preg_match('#/(login|invitation)$#', parse_url($r, PHP_URL_PATH))) {
		$r = $config['ROOT'] . '/';
	}
	
	// Redirect to the specified URL
	header('Location: ' . $r);
	
} else {
	
	// Not a form submission, output the login page/form
	outputPage();
}

