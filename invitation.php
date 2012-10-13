<?php

require_once dirname(__FILE__) . '/l/db.inc.php';
require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

$tok = @$_GET['t'];

$p = NULL;
if ($tok) {
	$res = $db->query('SELECT * FROM `players` WHERE `firstlogints` IS NULL AND `token`=' . s($tok));
	$p = $res->fetch_assoc();
}

if (!isSet($p)) {
	mp('<h1>Invalid Token</h1>
<p>The link you are using is invalid, either because it is wrong or it has been used before.
  Please contact <a href="mailto:jeffrey&#64;battleroyale.ca">Jeffrey</a> for assitance.</p>');
}

setCurrUser(0);

$res = $db->query('SELECT COUNT(`email`) AS `c` FROM `players` WHERE `email`=' . s($p['email']));
$count = $res->fetch_assoc();

$username = '';
if ($count['c'] <= 1) {
	$username = $p['email'];
}

$src = sPrintF('
<h1>Create Player Account</h1>

<fieldset class="faded-bg" style="width:500px;">
<legend>Instructions</legend>
<p>Hello %1$s %2$s,</p>
<p>Please complete the following form to complete creating your player account.</p>
<p><strong>Note:</strong> Please double-check your email address because it is difficult to change it later.
  If you have any difficulties, please contact <a href="mailto:jeffrey&#64;battleroyale.ca">Jeffrey</a> for assistance.</p>
<p>Thanks,<br />
  Battle Royale VI Organizers</p>
</fieldset>

<form action="#" onsubmit="return createAccount(this);">
<input type="hidden" name="tok" value="%7$s" />
<fieldset class="faded-bg" style="width:500px;">
<legend>Account Info</legend>
<table cellspacing="10" style="margin:-10px;" width="100%%">
<col width="145" /><col />
<tr><td>Name:</td><td>%1$s %2$s</td></tr>
<tr><td>Ticket:</td><td>%6$s</td></tr>
<tr><td colspan="2"><hr /></td></tr>
<tr><td><strong>Display Name:</strong><br /><small>The name that will be displayed to other players.</small></td>
  <td><input type="text" name="dname" value="%5$s" /></td></tr>
<tr><td><strong>Email:</strong><br /><small>Used for account management and notifications.</small></td>
  <td><input type="text" name="email" value="%3$s" /></td></tr>
<tr><td><strong>Username:</strong><br /><small>For login purpuses only.</small></td>
  <td><input type="text" name="user" value="%4$s" /></td></tr>
<tr><td><strong>New Password:</strong><br /><small>Must be at least 8 characters long.</small></td>
  <td><input type="password" name="pass1" /></td></tr>
<tr><td><strong>Confirm Password:</strong></td><td><input type="password" name="pass2" /></td></tr>
<tr><td></td><td><input type="submit" name="subbtn" value="Create Account" /></td></tr>
</table>
</fieldset>
</form>
', $p['fname'], $p['lname'], $p['email'], $username, $p['dname'], $p['ticket'], $tok);

mp($src);

