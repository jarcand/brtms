<?php

/**
 * AJAX request to remove a player from a team/group.
 */

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$ret = array();
	$gid = @$_POST['gid'];
	$pid = @$_POST['pid'];
	if ($pid == 'me') {
		$pid = $_p['pid'];
	}
	
	// Remove them from the group
	$res = $db->query($sql = sPrintF('UPDATE `tournament_players`
	  SET `gid`=NULL
	  WHERE `gid`=%1$s AND `pid`=%2$s
	  ', s($gid), s($pid)));
	if (!$res) {
		error($sql);
	}

	$affected = $db->affected_rows;
	if ($affected == 1) {
		$ret = array('result' => 'success');
	} else {
		$ret = array('result' => 'error', 'errorType' => 'affected: ' . $affected);
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
	
} else {
	http_response_code(400);
}

