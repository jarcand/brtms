<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

function fd($ts) {
	return !$ts ? '' : (subStr($ts, 8, 2) . strToLower(subStr(strFTime('%b', strToTime($ts)), 0, 1)) . subStr($ts, 11));
}

$src = '<h1>Admin Dashboard</h1>';

$res = $db->query('SELECT
  (SELECT COUNT(*) FROM `players`) AS `total`,
  (SELECT COUNT(*) FROM `players` WHERE `firstlogints` IS NOT NULL) AS `signups`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` IS NULL) AS `notinvited`,
  (SELECT COUNT(*) FROM `players` WHERE `invitedts` > DATE_SUB(NOW(), INTERVAL 1 HOUR)) AS `lasthour`,
  (SELECT COUNT(*) FROM `tournaments` WHERE `published`=0) AS `tours_unpublished`,
  (SELECT `registeredts` FROM `players` ORDER BY `registeredts` DESC LIMIT 1) AS `last_registered`
  FROM DUAL');
$stats = $res->fetch_assoc();

$src .= '<div class="center">';
$src .= mt('Total Players', $stats['total'], 'yellow');
$src .= mt('Signed Up', $stats['signups'], 'green', sPrintF('equiv to %d%%', $stats['signups'] / $stats['total'] * 100));
$src .= mt('Not Invited', $stats['notinvited'], 'red');
$src .= mt('Invites Sent', $stats['lasthour'], 'orange', 'Last Hour');
$src .= mt('Unpublished', $stats['tours_unpublished'], 'red', 'Crowd Tours');
$src .= '</div>';

date_default_timezone_set('America/Montreal');
$diff = time() - strToTime($stats['last_registered'] . ' EST');

$src .= sPrintF('
<div class="center faded-bg tac">
<form action="players_import_eventbrite" method="post">
<big><strong>Import from Eventbrite:</strong></big> <input type="submit" value="Import Now" /><br />
Last purchase made %1$.1f hours ago.
</form>
</div>
<p class="tac"><a href="full">See full players list</a></p>
', $diff / 3600);


$res = $db->query('SELECT `pid`, `fname`, `lname`, `credits`, `email`, `registeredts`, `invitedts`, `firstlogints`, `lastlogints`
  FROM `players` `p`
  WHERE `firstlogints` IS NULL
  ORDER BY `credits`, `pid`');

$src .= '<table cellspacing="0" class="border center">
';
$ths = '<tr><th>#</th><th>Name</th><th>Credits</th><th>Email</th><th>Registered</th><th>Invited</th><th>First Login</th></tr>';

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
	$src .= sPrintF('<tr><td>%s</td><td>%s %s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>
', $p['pid'], $p['fname'], $p['lname'], $p['credits'], $p['email'], fd($p['registeredts']), $inv_src, $inv_src2);
}

$src .= '</table>';

mp($src, 'Admin Dashboard');

