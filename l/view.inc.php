<?php

/**
 * This library file contains the functions to generate an HTML page/view.
 */

require_once dirname(__FILE__) . '/session.inc.php';

/**
 * Make a dashboard stats tile with the provided information.
 * @param $title - The top line of the tile, generally the title.
 * @param $value - The stats value to display in the tile.
 * @param $color default('') - The CSS class name to style the tile.
 * @param $title2 default('') - The bottom line of the tile, often a unit of measure.
 * @return The HTML code of the generated tile.
 */
function mt($title, $value, $color = '', $title2 = '') {
	return sPrintF('<div class="tile %1$s"><span>%2$s</span> %3$s '
	  . '<span>%4$s</span></div>',
	  $color, $title, $value, $title2);
}

/**
 * Output a themed HTML page - the brief version.
 * Note: This function replaces the %%TITLE%% and ${ROOT} special symbols in the
 * template files and the main body with their appropriate values.
 * @param $main_body - The HTML code of the main body of the page.
 * @param $subtitle default('') - The section title of the page.
 * @terminates This function always terminates the script.
 */
function mpb($main_body, $subtitle = '') {
	global $config, $_p;
	
	$title = 'Battle Royale VI - Players Portal';
	if ($subtitle != '') {
		$title .= ' - ' . $subtitle;
	}
	
	$str = file_get_contents(dirname(__FILE__) . '/../style/top-brief.inc');
	$contents = str_replace('%%TITLE%%', $title, $str);
	$contents .= $main_body;
	$contents .= file_get_contents(dirname(__FILE__) . '/../style/bottom-brief.inc');
	$contents = str_replace('${ROOT}', $config['ROOT'], $contents);
	
	echo $contents;
	die;
}

/**
 * Output a themed HTML page - the regular version.
 * Note: This function replaces the %%TITLE%%, ${ROOT}, and ${CURR_USER} special
 * symbols in the template files and main body with their appropriate values.
 * @param $main_body - The HTML code of the main body of the page.
 * @param $subtitle default('') - The section title of the page.
 * @terminates This function always terminates the script.
 */
function mp($main_body, $subtitle = '') {
	global $config, $_p;
	
	$title = 'Battle Royale VI - Players Portal';
	if ($subtitle != '') {
		$title .= ' - ' . $subtitle;
	}
	
	$str = file_get_contents(dirname(__FILE__) . '/../style/top.inc');
	$contents = str_replace('%%TITLE%%', $title, $str);
	$contents .= $main_body;
	$contents .= file_get_contents(dirname(__FILE__) . '/../style/bottom.inc');
	
	if ($_p) {
		$user_html = sPrintF('<li><a href="${ROOT}/profile">%1$s</a></li>
<li><a href="${ROOT}/logout">Logout</a></li>',
		  $_p['dname']);
	} else {
		$user_html = '<li>Welcome Guest!</li>
<li><a href="${ROOT}/login">Login</a></li>';
	}
	$contents = str_replace('${CURR_USER}', $user_html, $contents);
	$contents = str_replace('${ROOT}', $config['ROOT'], $contents);
	
	echo $contents;
	die;
}

