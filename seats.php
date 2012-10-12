<?php

require_once 'l/seating.inc.php';
require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

$res_seats = array(
  'A14' => ' Unavailable',
  'B14' => ' Unavailable',
  'C14' => ' Unavailable',
  'D22' => ' Reserved',
  'D24' => ' Reserved',
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

$src = '<h1>Seating Plan</h1>
';

if (isSet($_p)) {
	$src .= '
<p>Choose your desired seat on the map below.  Available seats are in indicated with white circles.  Red squares are taken seats, gray squares are reserved, and the lime square is your seat.</p>

<form action="#" onsubmit="return chooseSeat(this);">
';
}
$src .= genSeatChart($res_seats);
if (isSet($_p)) {
	$src .= '
<p style="margin-top:1em;"><input type="submit" class="submit" value="Submit" /></p>
</form>

<script type="text/javascript">
	$(\'#seating\').find(\'input\').click(function() {chooseSeat(this.form);});
</script>
';
}

mp($src);

