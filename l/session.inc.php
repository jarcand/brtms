<?php


require_once dirname(__FILE__) . '/config.inc.php';
require_once dirname(__FILE__) . '/db.inc.php';

define('SES_COOK', $config['instance'] . '-session');

function encodePassword($password) {
	global $config;
	return sha1($config['SALT'] . '-password-' . $password);
}

function isSession() {
	global $_p;
	return isSet($_p);
}

function loadPlayer() {
	global $db, $ses_token, $_p;
	if ($ses_token) {
		$res = $db->query($sql = sPrintF('SELECT * FROM `players`
		  WHERE `firstlogints` IS NOT NULL AND `token`=' . s($ses_token)));
		if (!$res) {
			error($sql);
		}
		$_p = $res->fetch_assoc();
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if (preg_match('/^134[.]117[.]20[67][.]/', $ip)
		  || !preg_match('/^134[.]117[.]20[67][.]/', $_p['ip'])) {
			$res = $db->query($sql = sPrintF('UPDATE `players`
			  SET `ip`=%1$s WHERE `pid`=%2$s
			  ', s($ip), s($_p['pid'])));
			if (!$res) {
				error($sql);
			}
		}
	} else {
		$_p = NULL;
	}
}

function requireSession($type = 'html') {
	global $config, $_p;
	if (!isSession()) {
		
		if ($type == 'json') {
			header('Content-Type: application/json');
			die('{"result":"error","errorType":"auth"}');
			
		} else if ($type == 'html') {
			$parts = parse_url($_SERVER['REQUEST_URI']);
			header('Location: ' . $config['ROOT'] . '/login?r=' . $parts['path']);
			die;
		}
	}
}

function requireAdminSession($type = 'html') {
	global $config, $_p;
	if (!isSession() || $_p['pid'] != 1) {
		if ($type == 'json') {
			header('Content-Type: application/json');
			die('{"result":"error","errorType":"autha"}');
			
		} else if ($type == 'html') {
			header('Location: ' . $config['ROOT'] . '/');
			die;
		}
	}
}

function setCurrUser($new_ses_token) {
	global $ses_token;
	$ses_token = $new_ses_token;
	setCookie(SES_COOK, $ses_token, 0, '/');
	loadPlayer();
}

$ses_token = @$_COOKIE[SES_COOK];
$_p = NULL;

loadPlayer();

