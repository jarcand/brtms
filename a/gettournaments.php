<?php

/**
 * AJAX request to get the list of tournaments with brief details.
 */

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';

if (!isSet($tour)) {
	$tour = @$_GET['tour'];
}

if (!isSet($ret)) {
	$ret = array();
}

// Display published tournaments and tournaments owned by the current player
$cond1 = '(`published`=1 OR `owner_pid`=' . s($_p['pid']) . ')';
if ($_p['pid'] == '1') {
	$cond1 = '1';
}

$cond2 = '';
if ($tour != '') {
	$cond2 = sPrintF(' AND (`tid`=%1$s OR `shortcode`=%1$s)', s($tour));
}

// Get the brief details of the tournaments
$res = $db->query($sql = 'SELECT `tid`, `shortcode`, `name`, `major`, `published`, `game`, `desc`, `prizes`, `teamsize`,
  (SELECT `dname` FROM `players` `p` WHERE `p`.`pid`=`t`.`owner_pid`) AS `organizer`,
  (SELECT COUNT(*) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid`) AS `players`,
  (SELECT COUNT(DISTINCT `gid`) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid`) AS `teams`,
  (SELECT COUNT(*) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid` AND `gid` IS NULL) AS `freeagents`,
  (SELECT COUNT(*) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid` AND `tp`.`pid`=' . s($_p['pid']) . ') AS `joined`
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

// If there is a current player logged in, ...
if (isSession()) {
	
	// Get the list of their registered tournaments
	$res = $db->query($sql = 'SELECT `tid`, `major`, `gid`
	  FROM `tournament_players` `tp`
	  INNER JOIN `tournaments` `t` USING (`tid`)
	  WHERE `pid`=' . s($_p['pid']));
	if (!$res) {
		error($sql);
	}

	$myteams = array();
	while ($o = $res->fetch_assoc()) {
		$myteams[$o['tid']] = $o;
	}

	$limit_s = $_p['credits'];
	if (((int) $limit_s) >= 10) {
		$limit_s = '<small>unlimited</small>';
	}

	$ret['myteams'] = $myteams;
	$ret['limit'] = $_p['credits'];
	$ret['limit_s'] = $limit_s;
}

if (!isSet($embedded) || !$embedded) {
	header('Content-Type: application/json');
}
echo json_encode($ret);
unSet($ret);

