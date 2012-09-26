<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/view.inc.php';

requireSession();

$res = $db->query('SELECT `pid`, `fname`, `lname`, `credits`, `email`,
  (SELECT COUNT(`gid`) FROM `tournament_players` `tp` WHERE `tp`.`pid`=`p`.`pid`) AS `teams`
  FROM `players` `p`');

$src = '<table cellspacing="0" class="border">
<tr><th>#</th><th>Name</th><th>Credits</th><th>Teams</th><th>Email</th></tr>
';

while ($p = $res->fetch_assoc()) {
	$src .= sPrintF('<tr><td>%s</td><td><a href="p%1$s/">%s %s</a></td><td>%s</td><td>%s</td><td>%s</td></tr>
', $p['pid'], $p['fname'], $p['lname'], $p['credits'], $p['teams'], $p['email']);
}

$src .= '</table>';

mp($src);

