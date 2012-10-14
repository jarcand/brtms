<?php

require_once dirname(__FILE__) . '/session.inc.php';

function mt($title, $value, $color = '', $title2 = '') {
	return sPrintF('<div class="tile %1$s"><span>%2$s</span> %3$s '
	  . '<span>%4$s</span></div>',
	  $color, $title, $value, $title2);
}

function mp($main_body) {
	global $config, $_p;
	
	$title = 'Battle Royale VI - Players Portal';
	
	$str = file_get_contents(dirname(__FILE__) . '/../style/top.inc');
	$contents = str_replace('%%TITLE%%', $title, $str);
	$contents .= $main_body;
	$contents .= file_get_contents(dirname(__FILE__) . '/../style/bottom.inc');
	
	if ($_p) {
		if ($_p['credits'] == 200) {
			$limit = 'Unlimited';
		} else if ($_p['credits'] == 1) {
			$limit = '1 Major';
		} else {
			$limit = $_p['credits'] . ' Majors';
		}
		$user_html = sPrintF('<li><a href="${ROOT}/profile">%1$s</a></li>
<li><a href="${ROOT}/logout">Logout</a></li>',
		  $_p['dname'], $limit);
	} else {
		$user_html = '<li>Welcome Guest!</li>
<li><a href="${ROOT}/login">Login</a></li>';
	}
	$contents = str_replace('${CURR_USER}', $user_html, $contents);
	$contents = str_replace('${ROOT}', $config['ROOT'], $contents);
	
	echo $contents;
	die;
}

