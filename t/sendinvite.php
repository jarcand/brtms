<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

$pid = $_GET['pid'];

$res = $db->query($sql = sPrintF('SELECT * FROM `players`
  WHERE `pid`=' . s($pid)));
if (!$res) {
	error($sql);
}
$p = $res->fetch_assoc();

// -----------------------------------------------------------------------------
$to = sPrintF('%1$s %2$s &lt;%3$s&gt;', $p['fname'], $p['lname'], $p['email']);
$to = sPrintF('%1$s %2$s &lt;%3$s&gt;', $p['fname'], $p['lname'], 'jeffrey@battleroyale.ca');
$subject = 'Your Invitation to the Battle Royale Players Portal';
// -----------------------------------------------------------------------------
$headers = implode("\r\n", array(
	'From: invite-bot@battleroyale.ca',
	'Reply-To: accounts@battleroyale.ca',
	'CC: accounts@battleroyale.ca',
	'Content-Type: text/plain; charset=ISO-8859-1',
	'X-Mailer: PHP/' . phpversion(),
));
// -----------------------------------------------------------------------------


$date = strtotime("2012-11-03 06:00:00");
$remaining = $date - time();
$remaining_days = floor($remaining / 3600 / 24);

// -----------------------------------------------------------------------------
$message = sPrintF('Hello %1$s %2$s!

Thank you for purchasing a ticket for Battle Royale VI, happening
November 3-4, 2012.  That is in just %4$s days!

We would like to take this opportunity to invite you to beta test the
new Battle Royale Players Portal.  It is through this portal that you
will be able to join tournaments, create new tournaments, and select
your seat.

Please use the personalize link below to accept your invitation.
http://players.battleroyale.ca/invitation?t=%3$s

If you have any problems, please email us at accounts@battleroyale.ca

For more information, see our website www.battleroyale.ca

Thanks,
Battle Royale Organizing Committee

', $p['fname'], $p['lname'], $p['token'], $remaining_days);
// -----------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$result = @mail($to, $subject, str_replace('<br />', "\n", $message), $headers);
	
	if ($result) {

		header('Content-Type: text/plain');
		echo 'Email invitation successfully sent!';
		
		$res = $db->query($sql = sPrintF('UPDATE `players`
		  SET `invitedts`=NOW()
		  WHERE `pid`=' . s($pid)));
		if (!$res) {
			error($sql);
		}
		echo '  Database succesSfully updated!';
		
	} else {
		header('Content-Type: text/plain');
		echo 'Oh noes!  There were errors!';
	}
	
} else {

	$src = sPrintF('
<div class="center">
<h1>Send Invitation Email</h1>
<form action="#" method="post">
<table>
<tr><td>To:</td><td><input type="text" name="to" readonly="readonly" size="60" value="%1$s" /></td></tr>
<tr><td>Subject:</td><td><input type="text" name="subject" readonly="readonly" size="60" value="%2$s" /></td></tr>
<tr><td>Headers:</td><td><textarea cols="40" readonly="readonly" rows="4" style="font-family:sans-serif;font-size:9pt;">%3$s</textarea></td></tr>
<tr><td>Message:</td><td><textarea cols="60" readonly="readonly" rows="20" style="font-family:sans-serif;font-size:9pt;">%4$s</textarea></td></tr>
<tr><td></td><td><input type="submit" value="Send Email Invitation" /> %5$s</td></tr>
</table>
</form>
</div>
', $to, $subject, $headers, $message, $p['invitedts']);
	
	mp($src);
}

