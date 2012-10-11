<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';

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

$ret['myteams'] = $myteams;
$ret['limit'] = $_p['credits'];

header('Content-Type: application/json');
echo json_encode($ret);

