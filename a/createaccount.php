<?php

require_once dirname(__FILE__) . '/../l/config.inc.php';
require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/utils.inc.php';

setCurrUser(0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$V = $_POST;
	
	$fields = array();
	$fields['token']	= NULL;
	$fields['dname']	= trim($V['dname']);
	$fields['email']	= trim($V['email']);
	$fields['username']	= trim($V['user']);
	$fields['password']	= trim($V['pass']);
	
	if (strLen($fields['dname']) < 1
	  || !preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $fields['email'])
	  || !preg_match('/^[A-Z0-9._+-@]+$/i', $fields['username'])
	  || strLen($fields['password']) < 8) {
		
		$ret = array('result' => 'error', 'errorType' => 'invalidParameters');
		
	} else {
		
		$fields['password'] = encodePassword($fields['password']);
		
		$s_tok = s($V['tok']);
		
		$res = $db->query($sql = 'SELECT COUNT(*) AS `c` FROM `players` WHERE `dname`=' . s($fields['dname'])
		  . ' AND (`token` IS NULL OR `token`!=' . $s_tok . ')');
		if (!$res) {
			error($sql);
		}
		$c_dname = $res->fetch_assoc();
		$res = $db->query($sql = 'SELECT COUNT(*) AS `c` FROM `players` WHERE `username`=' . s($fields['username'])
		  . ' AND (`token` IS NULL OR `token`!=' . $s_tok . ')');
		if (!$res) {
			error($sql);
		}
		$c_username = $res->fetch_assoc();
		
		if ($c_dname['c'] > 0) {
			$ret = array('result' => 'invalid', 'field' => 'dname');
		} else if ($c_username['c'] > 0) {
			$ret = array('result' => 'invalid', 'field' => 'user');
		} else {
		
			$res = $db->query($sql = 'SELECT `pid` FROM `players` WHERE `token`=' . $s_tok);
			if (!$res) {
				error($sql);
			}
			$p = $res->fetch_assoc();
			
			$sqlp = array();
			foreach ($fields as $key => $value) {
				$sqlp[] = sPrintF('`%s`=%s', $key, s($value));
			}
	
			if (!$db->query($sql = 'UPDATE `players` SET `firstlogints`=NOW(), '
			   . implode(', ', $sqlp) . ' WHERE `token`=' . $s_tok)) {
				error($sql);
			}
			
			$ret = array('result' => 'success', 'redirect' => $config['ROOT'] . '/login');
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

