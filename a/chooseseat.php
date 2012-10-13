<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$seat = $_POST['seat'];
	if ($seat == 'release') {
		$seat = null;
	}
	
	if ($seat != null && !preg_match('/^[a-z][0-9][0-9]?$/i', $seat)) {
		$ret = array('result' => 'invalid', 'errorType' => 'invalidParameter');
		
	} else {
		
		$s_seat = s($seat);
	
		$res = $db->query($sql = 'SELECT * FROM `players` WHERE `seat`=' . $s_seat);
		if (!$res) {
			error($sql);
		}
	
		if ($res->fetch_assoc()) {
		
			$ret = array('result' => 'invalid', 'errorType' => 'dupe');
		
		} else {
	
			if (!$db->query($sql = 'UPDATE `players` SET `seatts`=NOW(), `seat`=' . $s_seat . ' WHERE `pid`=' . s($_p['pid']))) {
				error($sql);
			}
			$ret = array('result' => 'success');
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

