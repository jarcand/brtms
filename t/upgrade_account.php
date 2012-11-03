<?php

require_once dirname(__FILE__) . '/../l/config.inc.php';
require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();


$tickets = array(
	101 => 'Early Bird Ticket - 1 Major Tournament (upgrade)',
	103 => 'Early Bird Ticket - 2-3 Major Tournaments (upgrade)',
	110 => 'Early Bird Ticket - 4+ Major Tournaments (upgrade)',
	1 => 'Regular Ticket - 1 Major Tournament (upgrade)',
	3 => 'Regular Ticket - 2-3 Major Tournaments (upgrade)',
	10 => 'Regular Ticket - 4+ Major Tournaments (upgrade)',
	220 => 'Volunteer Ticket - 4+ Major Tournaments (upgrade)',
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$pid = $_POST['pid'];
	$tic = (int) $_POST['tic'];
	
	if ($tic == 0) {
		mp('<p>No Change.</p>');
	}
	
	$early = (int) ($tic / 100);
	$credits = $tic % 100;
	
	var_export($early);
	echo '/';
	var_export($credits);
	
	$res = $db->query($sql = sPrintF('UPDATE `players`
	  SET `early`=%2$s, `credits`=%3$s, `ticket`=%4$s
	  WHERE `pid`=%1$s
	  ', s($pid), s($early), s($credits), s($tickets[$tic])));
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

	$src = sPrintF('<h1>Reset Password: %1$s (%2$s %3$s)</h1>
	<p>Successful!</p>
	', $p['dname'], $p['fname'], $p['lname']);

	mp($src);

} else {
	
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

	$src = sPrintF('<h1>Upgrade Account: %1$s (%2$s %3$s)</h1>
	<form action="#" method="post">
<input type="hidden" name="pid" value="%4$s" />
<select name="tic">
<option value="0">No Change</option>
	', $p['dname'], $p['fname'], $p['lname'], $pid);

	foreach ($tickets as $val => $name) {
		$src .= sPrintF('<option value="%1$s">%2$s</option>',
		  $val, $name);
	}
	$src .= '</select><input type="submit" value="Change" /></form>';

	mp($src);	
}
