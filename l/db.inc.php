<?php

require_once dirname(__FILE__) . '/config.inc.php';

function error($sql) {
	if (defined('DEBUG')) {
		die($sql);
	}
	header('Content-Type: application/json');
	die('{"result":"error"}');
}

function s($val, $type_check = true) {
	global $db;
	return ($type_check ? $val === NULL : $val == NULL)
	  ? 'NULL' :
	  sPrintF('_utf8"%s"', $db->real_escape_string($val));
}

/*
function q($sql) {
	global $db;
	if (!$db->query($sql)) {
		die('ERROR: Query failed: ' . $sql);
	}
}
*/

$db = new mysqli($config['DBHOST'], $config['DBUSER'], $config['DBPASS'], $config['DBNAME']);

$db->query('SET character_set_client=utf8');
$db->query('SET character_set_connection=utf8');
$db->query('SET character_set_results=utf8');

