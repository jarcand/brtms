<?php

/**
 * Import players from the EventBrite JSON feed, or display a form to do so.
 */

require_once dirname(__FILE__) . '/../l/config.inc.php';
require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

function parse_line($line) {
	return explode('","', subStr($line, 1, -1));
}

// If the form is submitted, ...
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Get the JSON feed
	$json = file_get_contents($config['eventbrite_attendee_list']);
	
	$data = json_decode($json, true);
	
	// Check if each player was already imported
	$stmt = $db->stmt_init();
	$stmt->prepare($sql = 'SELECT COUNT(*) AS `c` FROM `players`
	  WHERE `orderno`=? AND `attendeeno`=?');
	
	$orderno = 0;
	$attendeeno = 0;
	$c_players = 0;
	
	$stmt->bind_param('ss', $orderno, $attendeeno);
	$stmt->bind_result($c_players);
	
	$to_add = array();
	$c_skips = 0;
	foreach ($data['attendees'] as $attendee) {
		$att = $attendee['attendee'];
		
		$orderno = $att['order_id'];
		$attendeeno = $att['id'];
		
		$stmt->execute();
		$stmt->fetch();
		
		// If so, skip
		if ($c_players != '0') {
			$c_skips++;
		} else {
			$to_add[] = $att;
		}
	}
	
	$stmt->close();
	
	$d = '';
	$c_inserts = 0;
	foreach ($to_add as $att) {
		
		// Determine their credits and early-bird status
		$ticket_id = $att['ticket_id'];
		$ticket = '';
		$credits = 0;
		$early = 0;
		if ($ticket_id == '14910684') {
			$ticket = 'Early Bird Ticket - 1 Major Tournament';
			$credits = 1;
			$early = 1;
		} else if ($ticket_id == '14910686') {
			$ticket = 'Early Bird Ticket - 2-3 Major Tournaments';
			$credits = 3;
			$early = 1;
		} else if ($ticket_id == '14910690') {
			$ticket = 'Early Bird Ticket - 4+ Major Tournaments';
			$credits = 10;
			$early = 1;
		} else if ($ticket_id == '14910694') {
			$ticket = 'Regular Ticket - 1 Major Tournament';
			$credits = 1;
			$early = 0;
		} else if ($ticket_id == '14910698') {
			$ticket = 'Regular Ticket - 2-3 Major Tournaments';
			$credits = 3;
			$early = 0;
		} else if ($ticket_id == '14910700') {
			$ticket = 'Regular Ticket - 4+ Major Tournaments';
			$credits = 10;
			$early = 0;
		}
		
		// Determine all their fields
		$fields = array();
		$fields['token']	= subStr(sha1($config['SALT'] . '-invite-' . $att['id']), 10, 20);
		$fields['credits']	= $credits;
		$fields['early']	= $early;
		$fields['dname']	= $att['first_name'] . ' ' . $att['last_name'];
		$fields['email']	= $att['email'];
		$fields['registeredts']	= $att['created'];
		$fields['attendeeno']	= $att['id'];
		$fields['lname']	= $att['last_name'];
		$fields['fname']	= $att['first_name'];
		$fields['ticket']	= $ticket;
		$fields['orderno']	= $att['order_id'];
		$fields['mobile']	= 'N/I';
		$fields['gender']	= 'N/I';
		
		$sqlp = array();
		foreach ($fields as $key => $value) {
			$sqlp[] = sPrintF('`%s`=%s', $key, s($value));
		}
		
		// Add the player to the DB
		if (!$db->query($sql = 'INSERT INTO `players` SET ' . implode(', ', $sqlp))) {
			error($sql);
		}
		$c_inserts++;
	}
	
	// Display the results page
	$src = sPrintF('<h1>Import Players: Results</h1>
<p>Successfully imported %s players, skipped %2$s existing players.</p><pre>%3$s</pre>', $c_inserts, $c_skips, $d);
	
	mp($src);
	
} else {
	
	// Display the form
	$src = '<h1>Import Players from Eventbrite</h1>

<form action="#" method="post">
<p><input type="submit" value="Import from Eventbrite" /></p>
</form>
';
	
	mp($src, 'Import Players from Eventbrite');
}

