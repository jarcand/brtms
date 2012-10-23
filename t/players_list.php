<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

function fd($ts) {
	return !$ts ? '' : (subStr($ts, 8, 2) . '_' . subStr($ts, 11));
}

$src = '<h1>Players List</h1>';

$res = $db->query('SELECT
  (SELECT COUNT(*) FROM `players`) AS `total`,
  (SELECT COUNT(*) FROM `players` WHERE `firstlogints` IS NOT NULL) AS `signups`,
  (SELECT COUNT(*) FROM `players` WHERE `seat` IS NOT NULL) AS `seated`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` IS NULL) AS `notinvited`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` > DATE_SUB(NOW(), INTERVAL 1 HOUR)) AS `lasthour`,
  (SELECT `registeredts` FROM `players` ORDER BY `registeredts` DESC LIMIT 1) AS `last_registered`
  FROM DUAL');
$stats = $res->fetch_assoc();

$src .= '<div class="center">';
$src .= mt('Total Players', $stats['total'], 'yellow');
$src .= mt('Signed Up', $stats['signups'], 'green', 'equiv to ' . round($stats['signups'] / $stats['total'] * 100) . '%');
$src .= mt('Seated', $stats['seated'], 'green', 'equiv to ' . round($stats['seated'] / $stats['signups'] * 100) . '%');
$src .= mt('Not Invited', $stats['notinvited'], 'red');
$src .= mt('Invites Sent', $stats['lasthour'], 'orange', 'Last Hour');
$src .= '</div>';

$diff = time() - strToTime($stats['last_registered']);

$src .= sPrintF('
<div class="center faded-bg tac">
<form action="players_import_eventbrite" method="post">
<big><strong>Import from Eventbrite:</strong></big> <input type="submit" value="Import Now" /><br />
Last purchase made %1$.1f hours ago.
</form>
</div>
', ($diff / 3600) - 3);


$res = $db->query('SELECT `pid`, `fname`, `lname`, `credits`, `email`, `registeredts`, `invitedts`, `firstlogints`, `lastlogints`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=1 AND `tp`.`pid`=`p`.`pid`) AS `tours_major`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=0 AND `tp`.`pid`=`p`.`pid`) AS `tours_crowd`,
  (SELECT COUNT(`gid`) FROM `tournament_players` `tp`
    WHERE `tp`.`pid`=`p`.`pid`) AS `teams`
  FROM `players` `p`');

$src .= '<table cellspacing="0" class="border center">
';
$ths = '<tr><th>#</th><th>Name</th><th>Major</th><th>Crowd</th><th>Teams</th><th>Email</th><th>Registered</th><th>Invited</th><th>First Login</th></tr>';

$i = 0;
while ($p = $res->fetch_assoc()) {
	if (($i++ % 25) == 0) {
		$src .= $ths;
	}
	$inv_src = fd($p['invitedts']);
	if (!$inv_src) {
		$inv_src = sPrintF('<a href="sendinvite?pid=%1$s">Send Invite</a>', $p['pid']);
	}
	$inv_src2 = fd($p['firstlogints']);
	if (!$inv_src2 && strToTime($p['invitedts']) < strToTime('-3 days')) {
		$inv_src2 = sPrintF('<a href="sendinvite?pid=%1$s">Send Again</a>', $p['pid']);
	}
	$src .= sPrintF('<tr><td>%s</td><td>%s %s</td><td>%s/%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>
', $p['pid'], $p['fname'], $p['lname'], $p['tours_major'], $p['credits'], $p['tours_crowd'], $p['teams'], $p['email'], fd($p['registeredts']), $inv_src, $inv_src2);
}

$src .= '</table>';

mp($src, 'Players List');

