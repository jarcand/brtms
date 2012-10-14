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
	
	$json = file_get_contents($config['eventbrite_attendee_list']);
	
	$data = json_decode($json, true);
	
	$c_inserts = 0;
	$c_skips = 0;
	foreach ($data['attendees'] as $attendee) {
		$att = $attendee['attendee'];
		
		$res = $db->query($sql = sPrintF('SELECT COUNT(*) AS `c` FROM `players`
		  WHERE `orderno`=%1$s AND `attendeeno`=%2$s
		  ', s($att['order_id']), s($att['id'])));
		$row = $res->fetch_assoc();
		
		if ($row['c'] != '0') {
			$c_skips++;
			continue;
		}
		
		$c_inserts++;
	}
	
	
	$src = sPrintF('<h1>Import Players: Results</h1>
<p>Need to import %s players, skipped %2$s existing players.</p>', $c_inserts, $c_skips);
	
	mp($src);
	
} else {

	$src = '<h1>Import Players</h1>

<form action="#" method="post">
<p><input type="submit" value="Import from Event Brite" /></p>
</form>
';
	
	mp($src);
}

