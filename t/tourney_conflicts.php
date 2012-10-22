<?php

require_once dirname(__FILE__) . '/../l/db.inc.php';
require_once dirname(__FILE__) . '/../l/session.inc.php';
require_once dirname(__FILE__) . '/../l/view.inc.php';

requireAdminSession();

$res = $db->query('SELECT `tid`, `name`, `shortcode`
  FROM `tournaments`
  WHERE `major`=1');

$ts = array();
while ($t = $res->fetch_assoc()) {
	$ts[$t['tid']] = $t['shortcode'];
}

$res = $db->query('SELECT
  `pid`, `tid`
  FROM `tournament_players`
  INNER JOIN `tournaments` USING (`tid`)
  INNER JOIN `players` USING (`pid`)
  WHERE `major`=1 AND `early`!=2
  ORDER BY `pid`, `tid`');

$t1 = array();
while ($tp = $res->fetch_assoc()) {
	
	if (!isSet($tourneys[$tp['pid']])) {
		$tourneys[$tp['pid']] = array();
	}
	
	$t1[$tp['pid']][] = $tp['tid'];
}

$t2 = array();
foreach ($t1 as $pid => $tids) {
	
	if (count($tids) <= 1) {
		continue;
	}
	
	$s_tids = implode(',', $tids);
	if (!isSet($t2[$s_tids])) {
		$t2[$s_tids] = 0;
	}
	$t2[$s_tids]++;
}
arSort($t2);

$src = '<h1>Players Tournament Conflicts</h1>';

$src .= '<table>
<tr><th>Freq</th><th>Tids</th></tr>';
foreach ($t2 as $tids => $count) {
	
	$src .= sPrintF('<tr><td>%s</td><td>%s</td></tr>
', $count, $tids);
}
$src .= '</table>';

$t3 = array();
foreach ($t1 as $pid => $tids) {
	
	$c = count($tids);
	if ($c <= 1) {
		continue;
	}
	
	for ($i = 0; $i < $c - 1; $i++) {
		for ($j = $i + 1; $j < $c; $j++) {
			$s_tids = $tids[$i] . ',' . $tids[$j];
			if (!isSet($t3[$s_tids])) {
				$t3[$s_tids] = 0;
			}
			$t3[$s_tids]++;
		}
	}
}
arSort($t3);

$src .= '<table>
<tr><th>Tourney1</th><th>Tourney2</th><th>Freq</th></tr>';
foreach ($t3 as $s_tids => $count) {
	
	$tids = explode(',', $s_tids);
	
	$src .= sPrintF('<tr><td>%s</td><td>%s</td><td>%s</td></tr>
', $ts[$tids[0]], $ts[$tids[1]], $count);
}
$src .= '</table>';

mp($src);

