<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/view.inc.php';

requireSession();

$res = $db->query($sql = sPrintF('SELECT t.tid, t.name AS `tname`, op.fname AS `tfname`, op.lname AS `tlname`,
    g.gid, g.name AS `gname`, lp.fname AS `gfname`, lp.lname AS `glname`
  FROM `tournaments` `t`
  INNER JOIN `players` `op` ON `owner_pid`=`op`.`pid`
  INNER JOIN `groups` `g` USING (`gid`)
  INNER JOIN `players` `lp` ON `leader_pid`=`lp`.`pid`
  INNER JOIN `tournament_players` `tp` USING (`gid`)
  WHERE `tp`.`pid`=' . s($_pid)));
if (!$res) {
	error($sql);
}

$src = '<ul>
';

#foreach ($tournaments as $t) {
while ($t = $res->fetch_assoc()) {
	$src .= sPrintF('<li>%s: <strong>%s</strong> (managed by %s %s)
<ul>', $t['tid'], $t['tname'], $t['tfname'], $t['tlname']);
	$src .= sPrintF('<li>%s: <strong>%s</strong> (lead by %s %s)
<ul>
', $t['mid'], $t['mname'], $t['mfname'], $t['mlname']);
	
	$res2 = $db->query($sql = sPrintF('SELECT * FROM `players`
	  INNER JOIN `team_players` USING (`pid`)
	  WHERE `mid`=' . s($t['mid'])));
	
	while ($p = $res2->fetch_assoc()) {
		$src .= sPrintF('<li>%s %s</li>
', $p['fname'], $p['lname']);
	}
	
	$src .= '</ul></li></ul></li>';
}

$src .= '</ul>';


mp($src);

