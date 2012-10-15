<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

function fd($ts) {
	return !$ts ? '' : (subStr($ts, 8, 2) . '_' . subStr($ts, 11));
}

$src = '<h1>Players List Overview</h1>';

$res = $db->query('SELECT
  (SELECT COUNT(*) FROM `players`) AS `total`,
  (SELECT COUNT(*) FROM `players` WHERE `firstlogints` IS NOT NULL) AS `signups`,
  (SELECT COUNT(*) FROM `players` WHERE `seat` IS NOT NULL) AS `seated`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` IS NULL) AS `notinvited`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` > DATE_SUB(NOW(), INTERVAL 1 HOUR)) AS `lasthour`,

  (SELECT COUNT(*) FROM `players`
    WHERE `early`!=2 AND `credits`=1) AS `tickets_1cred`,
  (SELECT COUNT(*) FROM `players`
    WHERE `early`!=2 AND `credits`=3) AS `tickets_3cred`,
  (SELECT COUNT(*) FROM `players`
    WHERE `early`!=2 AND `credits`=10) AS `tickets_10cred`,
  (SELECT COUNT(*) FROM `tournament_players`
    INNER JOIN `tournaments` USING (`tid`)
    INNER JOIN `players` USING (`pid`)
    WHERE `major`=1 AND `early`!=2) AS `joined_major`,
  (SELECT SUM(`credits`) FROM `players`
    WHERE `early`!=2) AS `credits_major`,
  (SELECT COUNT(*) FROM `tournament_players`
    INNER JOIN `tournaments` USING (`tid`)
    WHERE `major`=0) AS `joined_crowd`,
  (SELECT COUNT(*) FROM `tournaments` WHERE `major`=0) AS `tours_crowd`,
  (SELECT COUNT(*) FROM `tournaments` WHERE `published`=0) AS `tours_unpublished`,
  (SELECT `registeredts` FROM `players` ORDER BY `registeredts` DESC LIMIT 1) AS `last_registered`
  FROM DUAL');
$stats = $res->fetch_assoc();

$src .= '<div class="center">';
$src .= mt('Total Players', $stats['total'], 'yellow');
$src .= mt('Signed Up', $stats['signups'], 'green');
$src .= mt('Seated', $stats['seated'], 'green');
$src .= mt('Not Invited', $stats['notinvited'], 'red');
$src .= mt('Invites Sent', $stats['lasthour'], 'orange', 'Last Hour');
$src .= '</div>';
$src .= '<div class="center">';
$src .= mt('Tickets', $stats['tickets_1cred'] . '/' . $stats['tickets_3cred'] . '/' . $stats['tickets_10cred'], 'yellow', 'of 1/2-3/4+');
$src .= mt('Joined Majors', $stats['joined_major'], 'blue', 'out of ' . $stats['credits_major']);
$src .= mt('Joined Crowds', $stats['joined_crowd'], 'blue');
$src .= mt('Crowd Tours', $stats['tours_crowd'], 'green');
$src .= mt('Unpublished', $stats['tours_unpublished'], 'red');
$src .= '</div>';

$diff = time() - strToTime($stats['last_registered']);

$src .= sPrintF('
<div class="center faded-bg tac">
<form action="players_import_eventbrite" method="post">
<big><strong>Import from Eventbrite:</strong></big> <input type="submit" value="Import Now" /><br />
Last purchase made %1$.1f hours ago.
</form>
</div>
', $diff / 3600);


$res = $db->query('SELECT `pid`, `fname`, `lname`, `credits`, `email`, `invitedts`, `firstlogints`, `lastlogints`,
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
$ths = '<tr><th>#</th><th>Name</th><th>Major</th><th>Crowd</th><th>Teams</th><th>Email</th><th>Invited</th><th>First Login</th></tr>';

$i = 0;
while ($p = $res->fetch_assoc()) {
	if (($i++ % 25) == 0) {
		$src .= $ths;
	}
	$inv_src = fd($p['invitedts']);
	if (!$inv_src) {
		$inv_src = sPrintF('<a href="sendinvite?pid=%1$s">Send Invite</a>', $p['pid']);
	}
	$src .= sPrintF('<tr><td>%s</td><td>%s %s</td><td>%s/%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>
', $p['pid'], $p['fname'], $p['lname'], $p['tours_major'], $p['credits'], $p['tours_crowd'], $p['teams'], $p['email'], $inv_src, fd($p['firstlogints']));
}

$src .= '</table>';

mp($src);

