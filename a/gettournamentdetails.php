<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';

requireSession('json');

$tid = @$_GET['tid'];

$res = $db->query($sql = sPrintF('SELECT `dname`
  FROM `players` `p`
  INNER JOIN `tournament_players` USING (`pid`)
  WHERE `tid`=%1$s AND `gid` IS NULL
  ORDER BY `dname` ASC
  ', s($tid)));
if (!$res) {
	error($sql);
}

$players = array();
while ($p = $res->fetch_assoc()) {
	$players[] = $p['dname'];
}

$ret = array();
$ret['players'] = $players;

$res = $db->query($sql = sPrintF('SELECT `gid`, `g`.`name`, `open`, `teamsize`,
  (`leader_pid`=%2$s) AS `is_leader`
  FROM `groups` `g`
  INNER JOIN `tournaments` USING (`tid`)
  WHERE `tid`=%1$s
  ORDER BY `is_leader` DESC, `name` ASC
  ', s($tid), s($_p['pid'])));
if (!$res) {
	error($sql);
}

$groups = array();
while ($g = $res->fetch_assoc()) {
	$groups[$g['gid']] = array(
	  'gid' => $g['gid'],
	  'name' => $g['name'],
	  'open' => $g['open'],
	  'teamsize' => $g['teamsize'],
	  'is_leader' => $g['is_leader'] == '1',
	);
}

foreach ($groups as $gid => $group) {
	
	$res = $db->query($sql = sPrintF('SELECT `pid`, `dname`
	  FROM `players` `p`
	  INNER JOIN `tournament_players` USING (`pid`)
	  WHERE `tid`=%1$s AND `gid`=%2$s
	  ORDER BY `dname` ASC
	  ', s($tid), s($gid)));
	if (!$res) {
		error($sql);
	}

	$members = array();
	while ($p = $res->fetch_assoc()) {
		$you = $p['pid'] == $_p['pid'];
		$members[] = !$groups[$gid]['is_leader']
		  ? $p['dname']
		  : array(
		    'dname' => $p['dname'],
		    'pid' => $p['pid'],
		    'you' => $you,
		  );
		if ($you) {
			$groups[$gid]['inteam'] = true;
		}
	}
	
	$groups[$gid]['members'] = $members;
}

$ret['teams'] = array_values($groups);

if (!isSet($embedded) || !$embedded) {
	header('Content-Type: application/json');
}
echo json_encode($ret);
unSet($ret);

