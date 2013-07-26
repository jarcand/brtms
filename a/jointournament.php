<?php

/**
 * AJAX request for the current user to join a tournament.
 * Note: Returns the updated list of tournaments.
 */

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$tid_s = s($_POST['tid']);
	
	$ret = array();
	$count = 0;
	
	$res = $db->query($sql = sPrintF('SELECT `major` FROM `tournaments`
	  WHERE `tid`=%1$s', $tid_s));
	if (!$res) {
		error($sql);
	}
	$row = $res->fetch_assoc();
	
	// Check the number of major tournaments the user is registered in
	if ($row['major'] == '1') {
		$res = $db->query($sql = sPrintF('SELECT COUNT(*) AS `c` FROM `tournament_players`
		  INNER JOIN `tournaments` USING (`tid`)
		  WHERE `major`=1 AND `pid`=%1$s', s($_p['pid'])));
		if (!$res) {
			error($sql);
		}
		$row = $res->fetch_assoc();
		$count = $row['c'];
	}
	
	// Ensure they have not surpassed their credit limit
	if ($count >= $_p['credits']) {
		$ret['result'] = 'error';
		$ret['errorType'] = 'overlimit';
		
	} else {
		
		$res = $db->query($sql = sPrintF('SELECT * FROM `tournament_players`
		  WHERE `pid`=%1$s AND `tid`=%2$s', s($_p['pid']), $tid_s));
		if (!$res) {
			error($sql);
		}
		
		// Ensure they are not already registered for the tournament
		if ($res->fetch_assoc()) {
		
			$ret['result'] = 'error';
			$ret['errorType'] = 'duplicateEntry';
		
		} else {
			
			// Update the DB
			if (!$db->query($sql = sPrintF('INSERT INTO `tournament_players`
			  SET `pid`=%1$s, `tid`=%2$s', s($_p['pid']), s($_POST['tid'])))) {
				error($sql);
			}
	
			$ret['result'] = 'success';
		}
	}
	
	// Return the updated list of tournaments
	require 'gettournaments.php';
	
} else {
	http_response_code(400);
}

