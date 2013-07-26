<?php

/**
 * Display a static seatign chart that shows the players' LAN IP addresses.
 */

require_once dirname(__FILE__) . '/../l/seating.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

// Specify the seats that are special
$res_seats = array();

$res = $db->query($sql = 'SELECT `seat`, `dname`, `ip` FROM `players` WHERE `seat` IS NOT NULL');
if (!$res) {
	error($sql);
}

$ips = array();

// Generate the array of occupied seats, with LAN IP addresses, and the array of IPs addresses and their players
while ($p = $res->fetch_assoc()) {
	if (!isSet($res_seats[$p['seat']])) {
		if ($p['ip']) {
			if (preg_match('/^134[.]117[.]20[67][.]/', $p['ip'])) {
				$res_seats[$p['seat']] = $p['dname'] . ' - ' . $p['ip'];
			}
			$ips[$p['ip']] = @$ips[$p['ip']] . $p['seat'] . ' - ' . $p['dname'] . ', ';
		}
	}
}

kSort($ips);

$src = '';

// Display instructions and legend
$seat_str = '';
if ($_p['seat']) {
	$seat_str = sPrintF(', seat %1$s', $_p['seat']);
}
$src .= sPrintF('
<fieldset class="faded-bg" style="float:right;margin-left:1em;width:450px;">
<legend>Seat Legend</legend>
<table class="center seating-chart">
<col /><col /><col width="25" />
<col /><col /><col width="25" />
<col /><col />
<tr>
<th class="vac"><input type="radio" /></th><td>&nbsp; No LAN IP</td><td></td>
<th class="occ"><input type="checkbox" /></th><td>&nbsp; Has LAN IP</td><td></td>
<th class="me"><input type="checkbox" /></th><td>&nbsp; You%1$s</td>
</tr>
</table>
</fieldset>

<h1>IP Address Seating Plan</h1>
', $seat_str);

// Generate the chart
$src .= genSeatChart($res_seats);

// Generate a lis tof IPs and their associated player's seat and  display name
$src .= '<table cellspacing="0" class="border">';
foreach ($ips as $ip => $seat) {
	$src .= sPrintF('<tr><td>%1$s</td><td>%2$s</td></tr>',
	  $ip, $seat);
}
$src .= '</table>';

mp($src, 'Seating Plan');

