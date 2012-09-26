<?php

require_once 'session.inc.php';

function mp($main_body) {
	global $config, $_p;
	
	$title = 'Battle Royale VI - Tournament Management System';
	
	$str = file_get_contents(dirname(__FILE__) . '/../style/top.inc');
	$contents = str_replace('%%TITLE%%', $title, $str);
	$contents .= $main_body;
	$contents .= file_get_contents(dirname(__FILE__) . '/../style/bottom.inc');
	
	if ($_p) {
		$user_html = sPrintF('<li>#</li><li><a href="${ROOT}/profile">%s</a></li><li><a href="${ROOT}/logout">Logout</a></li>',
		  $_p['dname']);
	} else {
		$user_html = '<li>#</li><li><a href="${ROOT}/login">Login</a></li>';
	}
	$contents = str_replace('${CURR_USER}', $user_html, $contents);
	$contents = str_replace('${ROOT}', $config['ROOT'], $contents);
	
	echo $contents;
	die;
}

