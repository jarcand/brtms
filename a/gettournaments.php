<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';

if (!isSet($tour)) {
	$tour = @$_GET['tour'];
}

if (!isSet($ret)) {
	$ret = array();
}

$cond1 = '(`published`=1 OR `owner_pid`=' . s($_p['pid']) . ')';
if ($_p['pid'] == '1') {
	$cond1 = '1';
}

$cond2 = '';
if ($tour != '') {
	$cond2 = sPrintF(' AND (`tid`=%1$s OR `shortcode`=%1$s)', s($tour));
}

$res = $db->query($sql = 'SELECT `tid`, `shortcode`, `name`, `major`, `published`, `game`, `desc`, `prizes`, `teamsize`,
  (SELECT `dname` FROM `players` `p` WHERE `p`.`pid`=`t`.`owner_pid`) AS `organizer`,
  (SELECT COUNT(*) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid`) AS `players`,
  (SELECT COUNT(`gid`) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid`) AS `teams`
  FROM `tournaments` `t`
  WHERE ' . $cond1 . $cond2 . '
  ORDER BY `major` DESC, `players` DESC');
if (!$res) {
	error($sql);
}

$tournaments = array();
while ($t = $res->fetch_assoc()) {
	$tournaments[] = $t;
}

$ret['tournaments'] = $tournaments;

header('Content-Type: application/json');
echo json_encode($ret);

