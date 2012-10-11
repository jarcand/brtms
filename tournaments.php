<?php

require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

$src = sPrintF('
<div id="registration-overview"></div>
<div style="display:table;">
<div id="tournaments"><h2 class="loading">Loading Tournament List&hellip;</h2></div>
<script type="text/javascript">
	var session = %1$s;
	var tournament_list = true;
	loadTournaments();
</script>
', $_p['pid'] ? 'true' : 'false');

if ($_p['pid']) {
	$src .= sPrintF('

<h2><a href="#" onclick="return showCreate();">Create a New Tournament</a></h2>

<div class="faded-bg" id="createForm" style="display:none;width:540px;">
<input type="button" class="closeButton" value="X" onclick="popup.close();" />
<h2>Create a New Tournament</h2>
<form action="#" onsubmit="return createTournament(this);">
<table cellspacing="10">
<col width="100" /><col width="350" />
<tr><td>Name:</td><td><input type="text" name="tname" size="40" /></td></tr>
<tr><td>Organizer:</td><td>you, %1$s</td></tr>
<tr><td>Game:</td><td><input type="text" name="game" size="40" /></td></tr>
<tr><td>Description/Rules:</td><td><textarea name="desc" cols="50" rows="2"></textarea></td></tr>
<tr><td>Prizes:</td><td><textarea name="prizes" cols="50" rows="2"></textarea></td></tr>
<tr><td>Teamsize:</td><td><select name="teamsize">
	<option value="1">1 - Single</option><option>2</option><option>3</option><option>4</option><option>5</option>
	<option>6</option><option>7</option><option>8</option><option>9</option><option>10</option>
	<option>11</option><option>12</option><option>13</option><option>14</option><option>15</option>
	<option>16</option><option>17</option><option>18</option><option>19</option><option>20</option>
	<option>21</option><option>22</option><option>23</option><option>24</option><option>25</option>
	</select></td></tr>
<tr><td>Options:</td><td>
	<label><input type="checkbox" name="major" value="yes" /> Major Tournament</label><br />
	</td></tr>
<tr><td>Notes:</td><td><textarea name="notes" cols="50" rows="2"></textarea></td></tr>
<tr><td></td><td><input type="submit" value="Create Tournament" /></td></tr>
</table>
</form>
</div>
', $_p['dname']);
}

$src .= '</div>';

mp($src);

