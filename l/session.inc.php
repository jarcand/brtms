<?php

/**
 * This library file contains the functions to used to manage user sessions.
 */

require_once dirname(__FILE__) . '/config.inc.php';
require_once dirname(__FILE__) . '/db.inc.php';

// The session cookie name
define('SES_COOK', $config['instance'] . '-session');

/**
 * Encode the provided password with salt!
 * @param $password - The password to encode.
 * @return The sha1 hash encoded password.
 */
function encodePassword($password) {
	global $config;
	return sha1($config['SALT'] . '-password-' . $password);
}

/**
 * Check if there is currently a user session.
 * @return Whether or not there is a user session.
 */
function isSession() {
	global $_p;
	return isSet($_p);
}

/**
 * Load the current player's information.
 * Note: This function has the side-effect of storing the user's IP address in
 * the database, for use in determining their LAN IP address.
 * @global $_p - This function affects the global $_p variable.
 */
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
		
		// Update the user's IP address in the database, but
		// only change a known LAN IP for another IP on our LAN
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

/**
 * Enfore the existence of a user session.  If it does not exist, redirect to
 * the login page.
 * @param $type default('html') - The redirect output type.
 * @terminates This function may terminate the script.
 */
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

/**
 * Enfore the existence of a admin session.  If it does not exist, redirect to
 * the login page.
 * Note: Admin session is defined as a session for user with PID=1.
 * @TODO Modify the system to have a more flexible definition of admin user.
 * @param $type default('html') - The redirect output type.
 * @terminates This function may terminate the script.
 */
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

/**
 * Set the current user to the one with the specified session token.
 * @param $new_ses_token - The session token of the new user.
 */
function setCurrUser($new_ses_token) {
	global $ses_token;
	$ses_token = $new_ses_token;
	setCookie(SES_COOK, $ses_token, 0, '/');
	loadPlayer();
}

// Get the session token from the user's cookie
$ses_token = @$_COOKIE[SES_COOK];
$_p = NULL;

loadPlayer();

