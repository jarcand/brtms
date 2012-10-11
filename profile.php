<?php

require_once 'l/db.inc.php';
require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

requireSession();

$res = $db->query($sql = sPrintF('SELECT
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=1 AND `pid`=%1$s) AS `tours_major`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=0 AND `pid`=%1$s) AS `tours_adhoc`
  FROM DUAL', $_p['pid']));
$sp = $res->fetch_assoc();

$src = '<h1>Player Profile</h1>';

$src .= sPrintF('<fieldset class="faded-bg" style="width:400px;">
<legend>Account Info</legend>
<table cellspacing="10" style="margin:-10px;" width="100%%">
<col width="160" /><col />
<tr><td>Name:</td><td>%1$s %2$s</td></tr>
<tr><td>Display Name:</td><td><input type="text" name="dname" value="%5$s" /></td></tr>
<tr><td>Email:</td><td>%3$s<br /> <a href="#">Change</a></td></tr>
<tr><td>Username:</td><td><input type="text" name="user" value="%4$s" /></td></tr>
<tr><td>Password:</td><td><a href="#">Change Password</a><br /> <a href="#">Forgot Password</a></td></tr>
</table>
</fieldset>
', $_p['fname'], $_p['lname'], $_p['email'], $_p['username'], $_p['dname']);

$src .= sPrintF('<fieldset class="faded-bg" style="width:400px;">
<legend>Registration Info</legend>
<table cellspacing="10" style="margin:-10px;" width="100%%">
<col width="160" /><col />
<tr><td colspan="2" style="text-align:center;">%1$s</td></tr>
<tr><td>Joined Tournaments</td><td>%2$s Major<br /> %3$s Ad-Hoc</td></tr>
<tr><td>Seat:</td><td><a href="${ROOT}/seats">%4$s</a></td></tr>
</table>
</fieldset>
', $_p['ticket'], $sp['tours_major'], $sp['tours_adhoc'], $_p['seat'] ? $_p['seat'] : 'Not Selected');

mp($src);

