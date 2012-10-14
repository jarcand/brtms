<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

$src = '<h1>Players List Overview</h1>';

$res = $db->query('SELECT
  (SELECT COUNT(*) FROM `players`) AS `total`,
  (SELECT COUNT(*) FROM `players` WHERE `firstlogints` IS NOT NULL) AS `signups`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` IS NULL) AS `notinvited`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` > DATE_SUB(NOW(), INTERVAL 1 HOUR)) AS `lasthour`,
  (SELECT COUNT(*) FROM `tournament_players`
    INNER JOIN `tournaments` USING (`tid`)
    WHERE `major`=1) AS `joined_major`,
  (SELECT COUNT(*) FROM `tournament_players`
    INNER JOIN `tournaments` USING (`tid`)
    WHERE `major`=0) AS `joined_crowd`,
  (SELECT COUNT(*) FROM `tournaments` WHERE `major`=0) AS `tours_crowd`,
  (SELECT COUNT(*) FROM `tournaments` WHERE `published`=0) AS `tours_unpublished`
  FROM DUAL');
$stats = $res->fetch_assoc();

$src .= '<div class="center">';
$src .= mt('Total Players', $stats['total'], 'yellow');
$src .= mt('Signed Up', $stats['signups'], 'green');
$src .= mt('Not Invited', $stats['notinvited'], 'red');
$src .= mt('Invites Sent', $stats['lasthour'], 'orange', 'Last Hour');
$src .= '</div>';
$src .= '<div class="center">';
$src .= mt('Joined Majors', $stats['joined_major'], 'blue');
$src .= mt('Joined Crowds', $stats['joined_crowd'], 'blue');
$src .= mt('Tours Crowds', $stats['tours_crowd'], 'green');
$src .= mt('Unpublished', $stats['tours_unpublished'], 'red');
$src .= '</div>';


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

$src .= '<table cellspacing="0" class="border">
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

