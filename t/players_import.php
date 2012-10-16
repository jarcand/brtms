<?php

require_once dirname(__FILE__) . '/../l/config.inc.php';
require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

function parse_line($line) {
	return explode('","', subStr($line, 1, -1));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$csv = $_POST['csv'];
	$lines = explode("\n", $csv);
	
	$headers = parse_line(array_shift($lines));
	
	$keys = array();
	$keys['attendeeno']	= array_search('Attendee #', $headers);
	$keys['date']		= array_search('Date', $headers);
	$keys['lname']		= array_search('Last Name', $headers);
	$keys['fname']		= array_search('First Name', $headers);
	$keys['email']		= array_search('Email', $headers);
	$keys['ticket']		= array_search('Ticket Type', $headers);
	$keys['orderno']	= array_search('Order #', $headers);
	$keys['mobile']		= array_search('Mobile Phone', $headers);
	$keys['gender']		= array_search('Gender', $headers);
	
	$c_inserts = 0;
	$c_skips = 0;
	foreach ($lines as $line) {
		if ($line == '') {
			continue;
		}
		
		$parts = parse_line($line);
		
		$res = $db->query($sql = sPrintF('SELECT COUNT(*) AS `c` FROM `players`
		  WHERE `orderno`=%1$s AND `attendeeno`=%2$s
		  ', s($parts[$keys['orderno']]), s($parts[$keys['attendeeno']])));
		$row = $res->fetch_assoc();
		
		if ($row['c'] != '0') {
			$c_skips++;
			continue;
		}
		
		$ticket = $parts[$keys['ticket']];
		$credits = 0;
		if ($ticket == 'Early Bird Ticket - 1 Major Tournament'
		  || $ticket == 'Regular Ticket - 1 Major Tournament') {
			$credits = 1;
		} else if ($ticket == 'Early Bird Ticket - 2-3 Major Tournaments'
		  || $ticket == 'Regular Ticket - 2-3 Major Tournaments') {
			$credits = 3;
		} else if ($ticket == 'Early Bird Ticket - 4+ Major Tournaments'
		  || $ticket == 'Volunteer Ticket - 4+ Major Tournaments'
		  || $ticket == 'Regular Ticket - 4+ Major Tournaments') {
			$credits = 10;
		}
		$early = 0;
		if ($ticket == 'Early Bird Ticket - 1 Major Tournament'
		  || $ticket == 'Early Bird Ticket - 2-3 Major Tournaments'
		  || $ticket == 'Early Bird Ticket - 4+ Major Tournaments') {
			$early = 1;
		} else if ($ticket == 'Volunteer Ticket - 4+ Major Tournaments') {
			$early = 2;
		}
		
		$fields = array();
		$fields['token']	= subStr(sha1($config['SALT'] . '-invite-' . $parts[$keys['attendeeno']]), 10, 20);
		$fields['credits']	= $credits;
		$fields['early']	= $early;
		$fields['dname']	= $parts[$keys['fname']] . ' ' . $parts[$keys['lname']];
		$fields['email']	= $parts[$keys['email']];
		$fields['registeredts']	= strFTime('%Y-%m-%d %H:%M:%S', strToTime($parts[$keys['date']]));
		$fields['attendeeno']	= $parts[$keys['attendeeno']];
		$fields['lname']	= $parts[$keys['lname']];
		$fields['fname']	= $parts[$keys['fname']];
		$fields['ticket']	= $ticket;
		$fields['orderno']	= $parts[$keys['orderno']];
		$fields['mobile']	= $parts[$keys['mobile']];
		$fields['gender']	= $parts[$keys['gender']];
		
		$sqlp = array();
		foreach ($fields as $key => $value) {
			$sqlp[] = sPrintF('`%s`=%s', $key, s($value));
		}
		
		if (!$db->query($sql = 'INSERT INTO `players` SET ' . implode(', ', $sqlp))) {
			error($sql);
		}
		$c_inserts++;
	}
	
	
	$src = sPrintF('<h1>Import Players: Results</h1>
<p>Successfully imported %s players, skipped %2$s existing players.</p>', $c_inserts, $c_skips);
	
	mp($src);
	
} else {

	$src = '<h1>Import Players</h1>

<form action="#" method="post">
<textarea name="csv" cols="120" rows="25">
</textarea>
<p><input type="submit" value="Import" /></p>
</form>
';
	
	mp($src);
}

