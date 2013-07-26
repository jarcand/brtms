<?php

/**
 * Serve the seatsd_lo.js file with the ${ROOT} symbol replaced.
 * Note: Use with the experiemental seatsd.php page.
 */

require_once dirname(__FILE__) . '/l/config.inc.php';

$contents = file_get_contents('seatsd_lo.js');
$contents = str_replace('${ROOT}', $config['ROOT'], $contents);

header('Content-Type: text/javascript');
echo $contents;

