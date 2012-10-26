<?php

require_once dirname(__FILE__) . '/l/config.inc.php';
require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

$src = '
<div id="registration-overview"></div>
<div id="non-overview-wrapper">
<h1 id="header1">Tournaments List</h1>
<div id="tournamentsListContent">
<p>Use the JOIN links to the right of each tournament to make your selection.  Based on your ticket type, you will have a limit to the number of Major Tournaments (top of the list) you may join, however you can join as many Crowdsourced Tournaments (bottom of list) as you like.  The lists are ordered with most popular tournaments first.</p>
<p>If you would like to create a new tournament, you can use the link at the bottom of the page.  However, before creating a tournament, we ask that you review the existing list to avoid duplication.</p>
<div id="tournamentsListDynContent"></div>
';

if ($_p['pid']) {
	$src .= sPrintF('

<h2 id="createTournamentLink"><a href="#" onclick="return showCreate();">Create a New Tournament</a></h2>

<div class="faded-bg" id="createForm" style="display:none;width:540px;">
<input type="button" class="closeButton" value="X" onclick="popup.close();" />
<h2>Create a new Crowdsourced Tournament</h2>
<form action="#" onsubmit="return createTournament(this);">
<table cellspacing="10">
<col width="100" /><col width="350" />
<tr><td>Organizer:</td><td>you, %1$s</td></tr>
<tr><td>Name:</td><td><input type="text" name="tname" size="40" /></td></tr>
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
<tr><td>Notes:</td><td><textarea name="notes" cols="50" rows="2"></textarea></td></tr>
<tr><td colspan="2"><strong>Note:</strong> For the moment, you cannot directly edit your tournament, so please double-check your inputs.  Edit feature is coming soon!</td></tr>
<tr><td></td><td><input name="subbtn" type="submit" value="Create Tournament" /></td></tr>
</table>
</form>
</div>

<div class="faded-bg" id="createTeamForm" style="display:none;width:540px;">
<input type="button" class="closeButton" value="X" onclick="popup2.close();" />
<h2>Create a new Team</h2>
<form action="#" onsubmit="return createTeam(this);">
<table cellspacing="10">
<col width="100" /><col width="350" />
<tr><td>Tournament:</td><td><input type="hidden" name="tid" /><input type="text" name="tournament" size="40" readonly="readonly" /></td></tr>
<tr><td>Leader:</td><td>you, %1$s</td></tr>
<tr><td>Team Name:</td><td><input type="text" name="tname" size="40" /></td></tr>
<tr><td>Status</td><td><select name="open">
	<option value="Yes">Open Team &ndash; We are looking for additional players</option>
	<option value="No">Closed Team &ndash; We have all the players we need</option>
	</select></td></tr>
<tr><td>Notes:</td><td><textarea name="notes" cols="50" rows="2"></textarea></td></tr>
<tr><td colspan="2"><strong>Note:</strong> For the moment, you cannot directly edit your team name or options, so please double-check your inputs.  Edit feature is coming soon!</td></tr>
<tr><td></td><td><input name="subbtn" type="submit" value="Create Team" /></td></tr>
</table>
</form>
</div>

', $_p['dname']);
}

$src .= sPrintF('</div>
<div id="tournamentContent">
<p><a href="${ROOT}/tournaments#">&lt;&lt; Go back to full list</a></p>
<div id="tournamentDynContent"></div>
</div>
</div>

<script type="text/javascript">
	var session = %1$s;
	var preloadData = ', $_p['pid'] ? 'true' : 'false');

$embedded = true;
ob_start();
require 'a/gettournaments.php';
$src .= ob_get_clean();

$src .= ';
	tournamentsPageInit();
</script>
';

mp($src);

