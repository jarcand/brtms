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

$res = $db->query($sql = sPrintF('SELECT `gid`, `name`, `open`
  FROM `groups` `g`
  WHERE `tid`=%1$s
  ORDER BY `name` ASC
  ', s($tid)));
if (!$res) {
	error($sql);
}

$groups = array();
while ($g = $res->fetch_assoc()) {
	$groups[$g['gid']] = array('name' => $g['name']);
}

foreach ($groups as $gid => $group) {
	
	$res = $db->query($sql = sPrintF('SELECT `dname`
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
		$members[] = $p['dname'];
	}
	
	$groups[$gid]['members'] = $members;
}

$ret['teams'] = array_values($groups);

if (!isSet($embedded) || !$embedded) {
	header('Content-Type: application/json');
}
echo json_encode($ret);
unSet($ret);

