<?php

require_once dirname(__FILE__) . '/../l/seating.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

$res_seats = array();

$res = $db->query($sql = 'SELECT `seat`, `dname`, `ip` FROM `players` WHERE `seat` IS NOT NULL');
if (!$res) {
	error($sql);
}

$ips = array();

while ($p = $res->fetch_assoc()) {
	if (!isSet($res_seats[$p['seat']])) {
		if ($p['ip']) {
			$res_seats[$p['seat']] = $p['dname'] . ' - ' . $p['ip'];
			$ips[$p['ip']] = @$ips[$p['ip']] . $p['seat'] . ' - ' . $p['dname'] . ', ';
		}
	}
}

$src = '';

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

$src .= genSeatChart($res_seats);

$src .= '<table cellspacing="0" class="border">';
foreach ($ips as $ip => $seat) {
	$src .= sPrintF('<tr><td>%1$s</td><td>%2$s</td></tr>',
	  $ip, $seat);
}
$src .= '</table>';


mp($src, 'Seating Plan');

