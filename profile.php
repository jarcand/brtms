<?php

/**
 * Display the user's profile.
 */

require_once dirname(__FILE__) . '/l/db.inc.php';
require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

requireSession();

// Get the user's profile information
$res = $db->query($sql = sPrintF('SELECT
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=1 AND `pid`=%1$s) AS `tours_major`,
  (SELECT COUNT(`tid`) FROM `tournaments` `t`
    INNER JOIN `tournament_players` `tp` USING (`tid`)
    WHERE `major`=0 AND `pid`=%1$s) AS `tours_crowd`
  FROM DUAL', $_p['pid']));
$sp = $res->fetch_assoc();

$src = '
<div class="center">
<h1>Player Profile</h1>
';

// Generate the general account information page section
$src .= sPrintF('
<form action="#" onsubmit="return updateDname(this);">
<fieldset class="faded-bg" style="width:450px;">
<legend>Account Info</legend>
<table cellspacing="10" style="margin:-10px;" width="100%%">
<col width="180" /><col />
<tr><td>Name:</td><td>%1$s %2$s</td></tr>
<tr><td>Email:</td><td>%3$s<br /> <small><em>To change, contact <a href="mailto:accounts&#64;battleroyale.ca">Accounts</a> for assistance.</em></small></td></tr>
<tr><td>Display Name:</td><td><input type="text" name="dname" value="%5$s" /></td></tr>
<tr><td></td><td><input type="submit" name="subbtn" value="Update Display Name" /></td></tr>
</table>
</fieldset>
</form>
', $_p['fname'], $_p['lname'], $_p['email'], $_p['username'], $_p['dname']);

// Generate the change password page section
$src .= sPrintF('
<form action="#" onsubmit="return changePassword(this);">
<fieldset class="faded-bg" style="width:450px;">
<legend>Change Password</legend>
<table cellspacing="10" style="margin:-10px;" width="100%%">
<col width="180" /><col />
<tr><td>Username:</td><td>%4$s<br /> <small><em>To change, contact <a href="mailto:accounts&#64;battleroyale.ca">Accounts</a> for assistance.</em></small></td></tr>
<tr><td>Current Password:</td><td><input type="password" name="curpwd" /></td></tr>
<tr><td>New Password:<br /><small>Must be at least 8 characters long.</small></td><td><input type="password" name="pass1" /></td></tr>
<tr><td>Repeat Password:</td><td><input type="password" name="pass2" /></td></tr>
<tr><td></td><td><input type="submit" name="subbtn" value="Change Password" /></td></tr>
</table>
</fieldset>
</form>
', $_p['fname'], $_p['lname'], $_p['email'], $_p['username'], $_p['dname']);

// Check if the user selected a seat
$seat_release = '';
if ($_p['seat']) {
	$seat_release = '&ndash; <a href="javascript:releaseSeat();">Release Seat</a>';
}

// Generate the registration information page section
$src .= sPrintF('
<fieldset class="faded-bg" style="width:450px;">
<legend>Registration Info</legend>
<table cellspacing="10" style="margin:-10px;" width="100%%">
<col width="180" /><col />
<tr><td colspan="2" style="text-align:center;">%1$s<br /> <small><em>Upgrade Ticket?  Contact <a href="mailto:sales&#64;battleroyale.ca">Sales</a> for assistance.</em></small></td></tr>
<tr><td>Joined Tournaments</td><td>%2$s Major<br /> %3$s Ad-Hoc</td></tr>
<tr><td>Seat:</td><td><a href="${ROOT}/seats">%4$s</a> %5$s</td></tr>
<tr><td>IP Address:</td><td>%6$s</td></tr>
</table>
</fieldset>
', $_p['ticket'], $sp['tours_major'], $sp['tours_crowd'], $_p['seat'] ? $_p['seat'] : 'Not Selected', $seat_release, $_p['ip']);

$src .= '</div>';

// Output the generated HTML page
mp($src, 'Player Profile');

