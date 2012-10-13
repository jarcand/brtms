<?php

require_once dirname(__FILE__) . '/../l/config.inc.php';

$contents = file_get_contents('script_raw.js');
$contents = str_replace('${ROOT}', $config['ROOT'], $contents);

header('Content-Type: text/javascript');
echo $contents;

