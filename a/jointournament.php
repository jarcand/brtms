<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$V = $_POST;
	
	$fields = array();
	$fields['tid']		= $V['tid'];
	$fields['pid']		= $_p['pid'];
	
	$sqlp = array();
	foreach ($fields as $key => $value) {
		$sqlp[] = sPrintF('`%s`=%s', $key, s($value));
	}
	
	$res = $db->query($sql = 'SELECT * FROM `tournament_players` WHERE ' . implode(' AND ', $sqlp));
	if (!$res) {
		error($sql);
	}
	
	$ret = array();
	
	if ($res->fetch_assoc()) {
		
		$ret['result'] = 'error';
		$ret['errorType'] = 'duplicateEntry';
		
	} else {
	
		if (!$db->query($sql = 'INSERT INTO `tournament_players` SET ' . implode(', ', $sqlp))) {
			error($sql);
		}
	
		$ret['result'] = 'success';
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

