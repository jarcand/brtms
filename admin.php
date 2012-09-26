<?php

require_once 'l/session.inc.php';
require_once 'l/view.inc.php';

requireSession();

$src = '<ul>
<li><a href="${ROOT}/players_import">Import Players</a></li>
<li><a href="${ROOT}/t/players">List all Players</a></li>
</ul>
';

mp($src);

