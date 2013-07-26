<?php

/**
 * Generate the HTML output to display the Disqus discussion area.
 * Note: We're showing this page in an Iframe because we can't determine
 * if we can dynamically load the Disqus on the tournaments.php page.
 */

require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

requireSession();

$tid = @$_GET['tid'];

$res = $db->query($sql = sPrintF('SELECT `name` FROM `tournaments` WHERE `tid`=%1$s', s($tid)));
if (!$res) {
	error($sql);
}
$t = $res->fetch_assoc();

if (!$t) {
	header('Location: ' . $config['ROOT'] . '/tournaments');
}

$src = sPrintF('
<div id="disqus_thread"></div>
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
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
', $tid);

mpb($src, 'Tournament: ' . $t['name']);

