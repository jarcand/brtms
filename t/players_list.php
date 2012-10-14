<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

$res = $db->query('SELECT `pid`, `fname`, `lname`, `credits`, `email`, `invitedts`, `lastlogints`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=1 AND `tp`.`pid`=`p`.`pid`) AS `tours_major`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=0 AND `tp`.`pid`=`p`.`pid`) AS `tours_crowd`,
  (SELECT COUNT(`gid`) FROM `tournament_players` `tp`
    WHERE `tp`.`pid`=`p`.`pid`) AS `teams`
  FROM `players` `p`');

$src = '<table cellspacing="0" class="border">
<tr><th>#</th><th>Name</th><th>Major</th><th>Crowd</th><th>Teams</th><th>Email</th><th>Invited</th><th>Last Login</th></tr>
';

while ($p = $res->fetch_assoc()) {
	$inv_src = $p['invitedts'];
	if (!$inv_src) {
		$inv_src = sPrintF('<a href="sendinvite?pid=%1$s">Send Invite</a>', $p['pid']);
	}
	$src .= sPrintF('<tr><td>%s</td><td>%s %s</td><td>%s/%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>
', $p['pid'], $p['fname'], $p['lname'], $p['tours_major'], $p['credits'], $p['tours_crowd'], $p['teams'], $p['email'], $inv_src, $p['lastlogints']);
}

$src .= '</table>';

mp($src);

