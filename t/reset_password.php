<?php

require_once dirname(__FILE__) . '/../l/config.inc.php';
require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

$pid = $_GET['pid'];

$res = $db->query($sql = sPrintF('UPDATE `players`
  SET `firstlogints`=NULL
  WHERE `pid`=%1$s
  ', s($pid)));
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

$url = '//' . $_SERVER['SERVER_NAME'] . $config['ROOT'] . '/invitation?t=' . $p['token'];

$src = sPrintF('<h1>Reset Password: %1$s (%2$s %3$s)</h1>
<p>Successful, and here is the reset link: <a href="%4$s">%4$s</a>.</p>
', $p['dname'], $p['fname'], $p['lname'], $url);

mp($src);
	

