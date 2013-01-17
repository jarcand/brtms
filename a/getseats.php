<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';

requireSession('json');

$res = $db->query($sql = 'SELECT `seat`, `dname` FROM `players` WHERE `seat` IS NOT NULL');
if (!$res) {
	error($sql);
}

while ($p = $res->fetch_assoc()) {
	if (!isSet($res_seats[$p['seat']])) {
		$res_seats[$p['seat']] = $p['dname'];
	}
}

header('Content-Type: application/json');
echo json_encode($res_seats);
unSet($ret);

