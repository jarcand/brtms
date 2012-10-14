<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';

if (!isSet($tids_str)) {
	$tids_str = @$_GET['tids'];
}

if (!isSet($ret)) {
	$ret = array();
}

$cond1 = '(`published`=1 OR `owner_pid`=' . s($_p['pid']) . ')';
if ($_p['pid'] == 1) {
	$cond1 = '1';
}

$cond2 = '';
if ($tids_str != '') {
	$tids = explode(',', $tids_str);
	$s_tids = array();
	foreach ($tids as $tid) {
		$s_tids[] = (int) $tid;
	}
	$cond2 = sPrintF(' AND `tid` IN (%s)', implode(',', $s_tids));
}

$res = $db->query('SELECT *,
  (SELECT `dname` FROM `players` `p` WHERE `p`.`pid`=`t`.`owner_pid`) AS `organizer`,
  (SELECT COUNT(*) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid`) AS `players`,
  (SELECT COUNT(`gid`) FROM `tournament_players` `tp` WHERE `tp`.`tid`=`t`.`tid`) AS `teams`
  FROM `tournaments` `t`
  WHERE ' . $cond1 . $cond2 . '
  ORDER BY `major` DESC, `players` DESC');
if (!$res) {
	error($sql);
}

$tournaments = array();
while ($t = $res->fetch_assoc()) {
	$tournaments[] = $t;
}

$ret['tournaments'] = $tournaments;

header('Content-Type: application/json');
echo json_encode($ret);

