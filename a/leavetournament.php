<?php

/**
 * AJAX request for the current user to leave a tournament.
 * Note: Returns the updated list of tournaments.
 */

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/utils.inc.php';

requireSession('json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$V = $_POST;
	
	$fields = array();
	$fields['tid']		= $V['tid'];
	$fields['pid']		= $_p['pid'];
	
	$sqlp = array();
	foreach ($fields as $key => $value) {
		$sqlp[] = sPrintF('`%s`=%s', $key, s($value));
	}
	
	// Update the DB
	$res = $db->query($sql = 'DELETE FROM `tournament_players` WHERE ' . implode(' AND ', $sqlp));
	if (!$res) {
		error($sql);
	}
	
	$ret['result'] = 'success';
	
	// Return the updated list of tournaments
	require 'gettournaments.php';
	
} else {
	http_response_code(400);
}

