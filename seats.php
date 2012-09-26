<?php

require_once 'l/seating.inc.php';
require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

$res_seats = array(
  'A14' => ' Reserved',
  'B14' => ' Reserved',
  'C14' => ' Reserved',
  'D22' => ' Reserved',
  'D24' => ' Reserved',
  'E13' => ' Reserved',
  'F13' => ' Reserved',
  'G13' => ' Reserved',
  'H13' => ' Reserved',
  'J13' => ' Reserved',
  'K13' => ' Reserved',
  'L13' => ' Reserved',
);

$res = $db->query($sql = 'SELECT `seat`, `dname` FROM `players` WHERE `seat` IS NOT NULL');
if (!$res) {
	error($sql);
}

while ($p = $res->fetch_assoc()) {
	$res_seats[$p['seat']] = $p['dname'];
}

$src = '<h1>Seating Plan</h1>
';

if (isSet($_p)) {
	$src .= '
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

