<?php

require_once '../l/config.inc.php';

$contents = file_get_contents('script_raw.js');
$contents = str_replace('${ROOT}', $config['ROOT'], $contents);

header('Content-Type: text/javascript');
echo $contents;

