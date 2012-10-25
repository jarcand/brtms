<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$V = $_POST;
	
	$fields = array();
	$fields['tid']		= $V['tid'];;
	$fields['leader_pid']	= $_p['pid'];
	$fields['name']		= $V['tname'];
	$fields['open']		= @$V['open'] == 'Yes' ? 1 : 0;
	$fields['notes']	= $V['notes'];
	
	$sqlp = array();
	foreach ($fields as $key => $value) {
		$sqlp[] = sPrintF('`%s`=%s', $key, s($value));
	}
	
	if (!$db->query($sql = 'INSERT INTO `groups` SET ' . implode(', ', $sqlp))) {
		error($sql);
	}
	
	$gid = $db->insert_id;
	
	
	if (!$db->query($sql = sPrintF('UPDATE `tournament_players`
	  SET `gid`=%1$s
	  WHERE `pid`=%2$s AND `tid`=%3$s
	  ', $gid, $_p['pid'], $V['tid']))) {
		error($sql);
	}
	
	$ret = array('result' => 'success');
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

