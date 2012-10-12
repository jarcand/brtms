<?php

require_once '../l/config.inc.php';
require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$s_dname = s($_POST['dname']);
	
	if (strLen($s_dname) < 1) {
		
		$ret = array('result' => 'error', 'errorType' => 'invalidParameters');
		
	} else {
		
		$res = $db->query($sql = 'SELECT COUNT(*) AS `c` FROM `players` WHERE `dname`=' . $s_dname
		  . ' AND `pid`!=' . s($_p['pid']));
		if (!$res) {
			error($sql);
		}
		$c_dname = $res->fetch_assoc();
		
		if ($c_dname['c'] > 0) {
			$ret = array('result' => 'invalid', 'field' => 'dname');
		} else {
		
			if (!$db->query($sql = 'UPDATE `players` SET `dname`=' . $s_dname
			   . ' WHERE `pid`=' . s($_p['pid']))) {
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

