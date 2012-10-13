<?php

require_once 'l/seating.inc.php';
require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

$res_seats = array(
  'A14' => ' Unavailable',
  'B14' => ' Unavailable',
  'C14' => ' Unavailable',
  'D22' => 'Network Admin',
  'D24' => 'Network Admin',
  'E13' => ' Unavailable',
  'F13' => ' Unavailable',
  'G13' => ' Unavailable',
  'H13' => ' Unavailable',
  'J13' => ' Unavailable',
  'K13' => ' Unavailable',
  'L13' => ' Unavailable',
);

$res = $db->query($sql = 'SELECT `seat`, `dname` FROM `players` WHERE `seat` IS NOT NULL');
if (!$res) {
	error($sql);
}

while ($p = $res->fetch_assoc()) {
	if (!isSet($res_seats[$p['seat']])) {
		$res_seats[$p['seat']] = $p['dname'];
	}
}

$src = '';

if (isSet($_p)) {
	$seat_str = '';
	if ($_p['seat']) {
		$seat_str = sPrintF(', seat %1$s', $_p['seat']);
	}
	$src .= sPrintF('
<fieldset class="faded-bg" style="float:right;margin-left:1em;width:380px;">
<legend>Seat Legend</legend>
<table class="center seating-chart">
<col /><col /><col width="25" />
<col /><col /><col width="25" />
<col /><col />
<tr>
<th class="vac"><input type="radio" /></th><td>&nbsp; Available</td><td></td>
<th class="occ"><input type="checkbox" /></th><td>&nbsp; Taken</td><td></td>
<th class="me"><input type="checkbox" /></th><td>&nbsp; You%1$s</td>
</tr>
</table>
</fieldset>

<h1>Seating Plan</h1>
<p>Choose your desired seat on the map below.  Your seat number will be used to determine which network port and power outlet you should use to setup your computer.</p>

<form action="#" onsubmit="return chooseSeat(this);">
', $seat_str);
	
} else {
	$src .= '
<fieldset class="faded-bg" style="float:right;margin-left:1em;width:380px;">
<legend>Seat Legend</legend>
<table class="center seating-chart">
<col /><col /><col width="25" />
<col /><col />
<tr>
<th class="vac2"><input type="checkbox" /></th><td>&nbsp; Available</td><td></td>
<th class="occ"><input type="checkbox" /></th><td>&nbsp; Taken</td>
</tr>
</table>
</fieldset>

<h1>Seating Plan</h1>
<p>You must login in order to choose your seat.</p>
';
}

$src .= genSeatChart($res_seats);

if (isSet($_p)) {
	$src .= '
</form>

<script type="text/javascript">
	$(".seating-chart.real").find("input").click(function() {chooseSeat(this.form);});
</script>
';
}

mp($src);

