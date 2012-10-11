<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$V = $_POST;
	
	$fields = array();
	$fields['owner_pid']	= $_p['pid'];
	$fields['name']		= $V['tname'];
	$fields['major']	= @$V['major'] == 'true' ? 1 : 0;
	$fields['teamsize']	= $V['teamsize'];
	$fields['game']		= $V['game'];
	$fields['desc']		= $V['desc'];
	$fields['prizes']	= $V['prizes'];
	$fields['notes']	= $V['notes'];
	
	$sqlp = array();
	foreach ($fields as $key => $value) {
		$sqlp[] = sPrintF('`%s`=%s', $key, s($value));
	}
	
	if (!$db->query($sql = 'INSERT INTO `tournaments` SET ' . implode(', ', $sqlp))) {
		error($sql);
	}
	
	$tid = $db->insert_id;
	
	$tids_str = $tid;
	$ret = array('result' => 'success');
	
	require_once 'gettournaments.php';
	
} else {
	http_response_code(400);
}

