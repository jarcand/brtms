<?php

require_once '../l/db.inc.php';
require_once '../l/session.inc.php';
require_once '../l/view.inc.php';

requireAdminSession();

$pid = $_GET['pid'];

$res = $db->query($sql = sPrintF('SELECT * FROM `players`
  WHERE `pid`=' . s($pid)));
if (!$res) {
	error($sql);
}
$p = $res->fetch_assoc();


// -----------------------------------------------------------------------------
$headers = array(
	'From: invite-bot@battleroyale.ca',
	'Reply-To: accounts@battleroyale.ca',
	'Bcc: accounts@battleroyale.ca',
	'X-Mailer: PHP/' . phpversion(),
);
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
$msg = sPrintF('Hello %1$s %2$s!

Thank you for purchasing a ticket for Battle Royale VI, happening
November 3-4, 2012.

We would like to take this opportunity to invite you to beta test the
new Battle Royale Players Portal.  It is through this portal that you
will be able to join tournaments, create new tournaments, and select
your seat.

Please use the personalize link below to accept your invitation.
http://players.battleroyale.ca/invite?t=%3$s

If you have any problems, please email us at accounts@battleroyale.ca

For more information, see our website www.battleroyale.ca

Thanks,
Battle Royale Organizing Committee

', $p['fname'], $p['lname'], $p['token']);
// -----------------------------------------------------------------------------

$src = sPrintF('
<div class="center">
<h1>Send Invitation Email</h1>
<form action="#" method="post">
<table>
<tr><td>To:</td><td><input type="text" name="to" size="60" value="%1$s %2$s &lt;%3$s&gt;" /></td></tr>
<tr><td>Subject:</td><td><input type="text" name="subject" size="60" value="Your Invitation to the Battle Royale Players Portal" /></td></tr>
<tr><td>Headers:</td><td><textarea cols="40" rows="4" style="font-family:sans-serif;font-size:10pt;">%4$s</textarea></td></tr>
<tr><td>Message:</td><td><textarea cols="60" rows="20" style="font-family:sans-serif;font-size:9pt;">%5$s</textarea></td></tr>
<tr><td></td><td><input type="submit" value="Send Email Invitation" /> %6$s</td></tr>
</table>
</form>
</div>
', $p['fname'], $p['lname'], $p['email'], implode("\r\n", $headers), $msg, $p['invitedts']);

mp($src);

