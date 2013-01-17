<?php

require_once dirname(__FILE__) . '/l/seating.inc.php';
require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

$res_seats = array(
  'A14' => ' Unavailable',
  'B14' => ' Unavailable',
  'C14' => ' Unavailable',
  'D22' => 'Network Admin',
  'D24' => 'Network Admin',
  'E21' => 'Players Portal Admin',
  'E23' => 'Players Portal Admin',
  'F13' => ' Unavailable',
  'G13' => ' Unavailable',
  'H13' => ' Unavailable',
  'J13' => ' Unavailable',
  'K1' => ' Unavailable',
  'K2' => ' Unavailable',
  'K3' => ' Unavailable',
  'K4' => ' Unavailable',
  'K5' => ' Unavailable',
  'K6' => ' Unavailable',
  'K7' => ' Unavailable',
  'K8' => ' Unavailable',
  'K9' => ' Unavailable',
  'K10' => ' Unavailable',
  'K11' => ' Unavailable',
  'K12' => ' Unavailable',
  'K13' => ' Unavailable',
  'K14' => ' Unavailable',
  'K15' => ' Unavailable',
  'K16' => ' Unavailable',
  'K17' => ' Unavailable',
  'K18' => ' Unavailable',
  'K19' => ' Unavailable',
  'K20' => ' Unavailable',
  'K21' => ' Unavailable',
  'K22' => ' Unavailable',
  'K23' => ' Unavailable',
  'K24' => ' Unavailable',
  'L13' => ' Unavailable',
);

$src = '';

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
<p>You may hover over any seat with your cursor to see its status.</p>
';

unSet($_p);

$src .= genSeatChart($res_seats);

$src .= '
<script src="seatsd_l.js" type="text/javascript"></script>
';

mp($src, 'Seating Plan');

