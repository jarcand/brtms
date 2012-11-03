<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

function fd($ts) {
	return !$ts ? '' : (subStr($ts, 8, 2) . strToLower(subStr(strFTime('%b', strToTime($ts)), 0, 1)) . subStr($ts, 11));
}

$src = '<h1>Players List</h1>';

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
  (SELECT COUNT(DISTINCT `gid`) FROM `tournament_players`) AS `total_teams`,
  (SELECT COUNT(`gid`) FROM `tournament_players`) AS `total_team_members`,
  (SELECT COUNT(*) FROM `tournament_players`
    INNER JOIN `tournaments` USING (`tid`)
    WHERE `teamsize`>1 AND `gid` IS NULL) AS `free_agents`
  FROM DUAL');
$stats = $res->fetch_assoc();

$prize_budget1 = $stats['tickets_1cred'] * 3 + $stats['tickets_3cred'] * 8 + $stats['tickets_10cred'] * 13;
$prize_budget2 = $stats['tickets_1cred'] * 10 + $stats['tickets_3cred'] * 15 + $stats['tickets_10cred'] * 20;

$src .= '<div class="center">';
$src .= mt('Total Players', $stats['total'], 'yellow');
$src .= mt('Signed Up', $stats['signups'], 'green', sPrintF('equiv to %d%%', $stats['signups'] / $stats['total'] * 100));
$src .= mt('Seated', $stats['seated'], 'green', sPrintF('equiv to %d%%', $stats['seated'] / $stats['signups'] * 100));
$src .= mt('Not Invited', $stats['notinvited'], 'red');
$src .= mt('Invites Sent', $stats['lasthour'], 'orange', 'Last Hour');
$src .= '</div>';
$src .= '<div class="center">';
$src .= mt('Tickets', $stats['tickets_1cred'] . '/' . $stats['tickets_3cred']
  . '/' . $stats['tickets_10cred'], 'yellow', 'of 1/2-3/4+');
$src .= mt('Prize Budget', $prize_budget1 . '$', 'orange', 'up to ' . $prize_budget2 . '$');
$src .= mt('Joined Majors', $stats['joined_major'], 'blue', 'out of ' . $stats['credits_major']);
$src .= mt('Prize per Join', sPrintF('%.2f$', $prize_budget1 / $stats['joined_major'], 2), 'blue',
  sPrintF('up to %.2f$', $prize_budget2 / $stats['joined_major'], 2));
$src .= '</div>';
$src .= '<div class="center">';
$src .= mt('Crowd Tours', $stats['tours_crowd'], 'green');
$src .= mt('Joined Crowds', $stats['joined_crowd'], 'blue');
$src .= mt('Unpublished', $stats['tours_unpublished'], 'red');
$src .= mt('Total Teams', $stats['total_teams'], 'green', $stats['total_team_members'] . ' members');
$src .= mt('Free Agents', $stats['free_agents'], 'yellow');
$src .= '</div>';

$res = $db->query('SELECT `pid`, `fname`, `lname`, `seat`, `credits`, `firstlogints`, `lastlogints`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=1 AND `tp`.`pid`=`p`.`pid`) AS `tours_major`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=0 AND `tp`.`pid`=`p`.`pid`) AS `tours_crowd`,
  (SELECT COUNT(`gid`) FROM `tournament_players` `tp`
    WHERE `tp`.`pid`=`p`.`pid`) AS `teams`
  FROM `players` `p`
  ORDER BY `tours_major`, `credits`, `tours_crowd`, `firstlogints`');

$src .= '<table cellspacing="0" class="border center">
';
$ths = '<tr><th>#</th><th>Name</th><th>Seat</th><th>Major</th><th>Crowd</th><th>Teams</th><th>First Login</th><th>Last Login</th><th>Actions</th></tr>';

$i = 0;
while ($p = $res->fetch_assoc()) {
	if (($i++ % 25) == 0) {
		$src .= $ths;
	}
	$last_login = fd($p['lastlogints']);
	if ((strToTime($p['lastlogints']) - strToTime($p['firstlogints'])) < 60) {
		$last_login = '--';
	}
	$src .= sPrintF('<tr><td>%s</td><td>%s %s</td><td>%s</td><td>%s/%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><a href="reset_password?pid=%1$s" title="Reset Password">[R]</td></tr>
', $p['pid'], $p['fname'], $p['lname'], $p['seat'], $p['tours_major'], $p['credits'], $p['tours_crowd'], $p['teams'], fd($p['firstlogints']), $last_login);
}

$src .= '</table>';

mp($src, 'Players List');

