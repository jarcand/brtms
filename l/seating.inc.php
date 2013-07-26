<?php

/**
 * This library file contains the functions used to generate the seating chart.
 */

require_once dirname(__FILE__) . '/session.inc.php';

/**
 * Generate a table cell in the seating chart.
 * Note: The chart will be displayed differently if the user is not logged in.
 * @param $seat - The seat number (ex: J14).
 * @param $res_sets - The states of the seats (ex: occupied, reserved, etc).
 * @return The HTML code of the generated table cell.
 */
function genSeatCell($seat, $res_seats = array()) {
	global $_p;
	
	if (isSet($res_seats[$seat])) {
		if ($res_seats[$seat] == ' Unavailable') {
			return '<th></th>';
		}
		return sPrintF('<th class="%3$s" title="Seat %1$s - %2$s" id="seat-%1$s">'
		  . '<input type="checkbox" /></th>',
			$seat,
			$res_seats[$seat] == ' Reserved' ? 'Reserved'
			  : (isSet($_p) ? 'Taken by '
			    . $res_seats[$seat] : 'Occupied'),
			$res_seats[$seat] == ' Reserved' ? 'rvd'
			  : (isSet($_p) ? ($seat == $_p['seat']
			    ? 'me' : 'occ') : 'occ'));
	} else {
		return sPrintF('<th class="%2$s" title="Seat %1$s - Vacant" id="seat-%1$s">'
		  . '<input type="radio" name="seat" value="%1$s" /></th>',
			$seat,
			isSet($_p) ? 'vac' : 'vac2');
	}
}

/**
 * Generate the seating chart.
 * Note: The chart will be displayed differently if the user is not logged in.
 * @param $res_sets - The states of the seats (ex: occupied, reserved, etc).
 * @return The HTML code of the generated chart.
 */
function genSeatChart($res_seats = array()) {
	
	$output = '<table cellspacing="0" class="seating-chart real">
<tr style="height:98px;"><td>&nbsp;</td></tr>
<tr>
<td>

<table cellspacing="0" class="p1">
';
	
	for ($j = 0; $j < 12; $j++) {
		$output .= '<tr><td class="lm"></td>';
		
		for ($i = 0; $i < 8; $i++) {
			$output .= genSeatCell(chr(65 + $i) . (2 * $j + 1), $res_seats)
			  . '<td class="tt">&nbsp;</td>';
			$output .= genSeatCell(chr(65 + $i) . (2 * $j + 2), $res_seats)
			  . '<td class="bt">&nbsp;</td>';
		}
		
		$output .= '<td class="rm"></td></tr>
';
	}
	
	$output .= '</table>

</td>
</tr>
<tr style="height:44px;"><td>&nbsp;</td></tr>
<tr>
<td>

<table cellspacing="0" class="p2">
';
	
	for ($i = 0; $i < 3; $i++) {
		$output .= '<tr class="ho"><td class="lm2"></td>';
	
		for ($j = 0; $j < 12; $j++) {
			$output .= genSeatCell(chr(74 + $i) . (23 - 2 * $j), $res_seats);
		}
		
		$output .= '<td class="rm2"></td></tr><tr style="height:26px;"><td>&nbsp;</td></tr>
<tr class="ho"><td class="lm2"></td>';
		
		for ($j = 0; $j < 12; $j++) {
			$output .= genSeatCell(chr(74 + $i) . (24 - 2 * $j), $res_seats);
		}
		
		$output .= '<td class="rm2"></td></tr><tr style="height:28px;"><td>&nbsp;</td></tr>
';
	}
	
	$output .= '</table>

</td>
</tr>
<tr style="height:107px;"><td>&nbsp;</td></tr>
</table>
';
	
	return $output;
}

