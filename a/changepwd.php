<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$ret = array();
	
	$newpwd_r = @$_POST['newpwd'];
	$curpwd = encodePassword(@$_POST['curpwd']);
	$newpwd = encodePassword($newpwd_r);
	
	$res = $db->query($sql = 'SELECT `password` FROM `players`
	  WHERE `firstlogints` IS NOT NULL AND `pid`=' . s($_p['pid']));
	if (!$res) {
		error($res);
	}
	$p = $res->fetch_assoc();
	
	if (!$p || $p['password'] != $curpwd) {
		
		$ret = array('result' => 'error', 'errorType' => 'invalidPassword');
		
	} else if (strLen($newpwd_r) < 8) {
		
		$ret = array('result' => 'error', 'errorType' => 'invalidParameters');
		
	} else {
		
		$res = $db->query($sql = 'UPDATE `players`
		  SET `password`=' . s($newpwd) . '
		  WHERE `pid`=' . s($_p['pid']));
		if (!$res) {
			error($res);
		}
		
		$ret = array('result' => 'success');
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

