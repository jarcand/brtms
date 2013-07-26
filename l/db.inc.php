<?php

/**
 * This library file contains the functions necessary to use the MySQL database.
 */

require_once dirname(__FILE__) . '/config.inc.php';

/**
 * Output an JSON error page when there's a database problem.
 * @param $sql - The SQL query that caused the problem.
 * @terminates This function always terminates the script.
 */
function error($sql) {
	if (defined('DEBUG')) {
		die($sql);
	}
	header('Content-Type: application/json');
	die('{"result":"error"}');
}

/**
 * Encode the provided string value against SQL injections.
 * @param $val - The string value to encode.
 * @param $type_check default(true) - Whether to do strict type checking when comparing to null.
 * @return The SQL encoded string.
 */
function s($val, $type_check = true) {
	global $db;
	return ($type_check ? $val === NULL : $val == NULL)
	  ? 'NULL' :
	  sPrintF('_utf8"%s"', $db->real_escape_string($val));
}

// Make the database connection
$db = new mysqli($config['DBHOST'], $config['DBUSER'], $config['DBPASS'], $config['DBNAME']);

// Set the connection to use UTF-8 character encoding
$db->query('SET character_set_client=utf8');
$db->query('SET character_set_connection=utf8');
$db->query('SET character_set_results=utf8');

