<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/utils.inc.php';

requireSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$s_seat = s($_POST['seat']);
	
	$res = $db->query($sql = 'SELECT * FROM `players` WHERE `seat`=' . $s_seat);
	if (!$res) {
		error($sql);
	}
	
	if ($res->fetch_assoc()) {
		
		$ret = array('result' => 'invalid', 'errorType' => 'dupe');
		
	} else {
	
		if (!$db->query($sql = 'UPDATE `players` SET `seat`=' . $s_seat . ' WHERE `pid`=' . s($_p['pid']))) {
			error($sql);
		}
		$ret = array('result' => 'success');
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

