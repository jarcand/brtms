<?php

require_once 'l/config.inc.php';
require_once 'l/db.inc.php';
require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

$tid = @$_GET['tid'];
$scode = @$_GET['scode'];

if (!$tid) {
	
	$res = $db->query($sql = sPrintF('SELECT `tid` FROM `tournaments` WHERE `shortcode`=%1$s', s($scode)));
	if (!$res) {
		error($sql);
	}
	$t = $res->fetch_assoc();
	
	if ($t) {
		$tid = $t['tid'];
	} else {
		header('Location: ' . $config['ROOT'] . '/tournaments');
	}
}

$src = sPrintF('
<div id="registration-overview"></div>
<div style="display:table;">
<div id="tournaments"><h2 class="loading">Loading Tournament List&hellip;</h2></div>
<script type="text/javascript">
	var session = %1$s;
	var tournament_list = false;
	var tid = %2$s;
	loadTournaments(showTournament);
</script>
', $_p['pid'] ? 'true' : 'false', $tid);

if ($_p['pid']) {
	$src .= sPrintF('

<h2>Discussion</h2>

<div id="disqus_thread"></div>
<!--
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = \'battleroyaletms\'; // required: replace example with your forum shortname
    var disqus_identifier = \'tournament-%1$s\';

    /* * * DON\'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
        dsq.src = \'http://\' + disqus_shortname + \'.disqus.com/embed.js\';
        (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
    })();
</script>
-->
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>


', $tid);
}

$src .= '</div>';

mp($src);

