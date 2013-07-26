<?php

/**
 * Reset the password for a user or display a form to submit such a request.
 */

require_once dirname(__FILE__) . '/../l/config.inc.php';
require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

// If the form was submitted, ...
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$pid = $_POST['pid'];
	
	// Clear the first login field, and give them a new invitation token
	$res = $db->query($sql = sPrintF('UPDATE `players`
	  SET `firstlogints`=NULL, `token`=%2$s
	  WHERE `pid`=%1$s
	  ', s($pid), s(subStr(sha1($config['SALT'] . '-invite-' . $pid), 10, 20))));
	if (!$res) {
		error($sql);
	}
	
	$res = $db->query($sql = sPrintF('SELECT `dname`, `fname`, `lname`, `token`
	  FROM `players`
	  WHERE `pid`=%1$s
	  ', s($pid)));
	if (!$res) {
		error($sql);
	}
	$p = $res->fetch_assoc();
	if (!$p) {
		die('Could not find player.');
	}
	
	// Generate the invitation URL
	$url = '//' . $_SERVER['SERVER_NAME'] . $config['ROOT'] . '/invitation?t=' . $p['token'];
	
	// Display the results page
	$src = sPrintF('<h1>Reset Password: %1$s (%2$s %3$s)</h1>
	<p>Successful, and here is the reset link: <a href="%4$s">%4$s</a>.</p>
	', $p['dname'], $p['fname'], $p['lname'], $url);
	
	mp($src);
	
} else {
	
	// Form was not submitted; display the form
	$pid = $_GET['pid'];
	
	$res = $db->query($sql = sPrintF('SELECT `dname`, `fname`, `lname`, `token`
	  FROM `players`
	  WHERE `pid`=%1$s
	  ', s($pid)));
	if (!$res) {
		error($sql);
	}
	$p = $res->fetch_assoc();
	if (!$p) {
		die('Could not find player.');
	}
	
	// Display the reset request form
	$src = sPrintF('<h1>Reset Password: %1$s (%2$s %3$s)</h1>
	<form action="#" method="post">
<input type="hidden" name="pid" value="%4$s" />
<input type="submit" value="Reset Account Password" />
</form>
	', $p['dname'], $p['fname'], $p['lname'], $pid);
	
	mp($src);
}
