<?php

/**
 * AJAX request to delete a team/group.
 */

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$ret = array();
	$gid = @$_POST['gid'];
	
	$res = $db->query($sql = sPrintF('SELECT * FROM `groups`
	  WHERE `gid`=%1$s
	  ', s($gid)));
	if (!$res) {
		error($sql);
	}
	$g = $res->fetch_assoc();
	
	// Ensure the current user is the team/group's leader
	if ($g['leader_pid'] != $_p['pid']) {
		$ret = array('result' => 'error', 'errorType' => 'not leader');
	} else {
		
		// Remove all players from the team/group
		if (!$db->query($sql = sPrintF('UPDATE `tournament_players`
		  SET `gid`=NULL
		  WHERE `gid`=%1$s
		  ', s($gid)))) {
			error($sql);
		}
		
		// Delete the team/group from the DB
		if (!$db->query($sql = sPrintF('DELETE FROM `groups`
		  WHERE `gid`=%1$s
		  ', s($gid)))) {
			error($sql);
		}
	
		$affected = $db->affected_rows;
		if ($affected == 1) {
			$ret = array('result' => 'success');
		} else {
			$ret = array('result' => 'error', 'errorType' => 'affected: ' . $affected);
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

