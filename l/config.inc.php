<?php

/**
 * This library file is used to configure the BRTMS installation.  This file
 * contains default values that should be overwritten by ../config.inc.php.
 */

$config = array();

// Used to ensure session cookies don't conflict if BRTMS is
// installed multiple times on a single domain
$config['instance'] = 'instance1';

// Used when hashing the passwords, sessions, and invites
$config['SALT'] = '3eb9441e25';

// The location of the root of the BRTMS installation relative
// to the server's document root
$config['ROOT'] = '';

// The database connection information
$config['DBHOST'] = '';
$config['DBUSER'] = '';
$config['DBPASS'] = '';
$config['DBNAME'] = '';

// Overwrite these defaults with new values
require_once dirname(__FILE__) . '/../config.inc.php';

