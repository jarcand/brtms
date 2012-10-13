<?php

require_once dirname(__FILE__) . '/l/session.inc.php';
require_once dirname(__FILE__) . '/l/view.inc.php';

requireSession();

$src = '<ul>
<li><a href="${ROOT}/players_import">Import Players</a></li>
<li><a href="${ROOT}/t/players">List all Players</a></li>
</ul>
';

mp($src);

