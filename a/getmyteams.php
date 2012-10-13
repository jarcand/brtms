<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';

requireSession('json');

$res = $db->query($sql = 'SELECT `tp`.`tid` AS `tp_tid`, `t`.`major` AS `t_major`, `g`.*
  FROM `tournament_players` `tp`
  INNER JOIN `tournaments` `t` USING (`tid`)
  LEFT JOIN `groups` `g` USING (`gid`)
  WHERE `pid`=' . s($_p['pid']));
if (!$res) {
	error($sql);
}

$myteams = array();
while ($o = $res->fetch_assoc()) {
	$myteams[$o['tp_tid']] = $o;
}

$limit_s = $_p['credits'];
if ($limit_s == '200') {
	$limit_s = '<small>unlimited</small>';
}

$ret['myteams'] = $myteams;
$ret['limit'] = $_p['credits'];
$ret['limit_s'] = $limit_s;

header('Content-Type: application/json');
echo json_encode($ret);

