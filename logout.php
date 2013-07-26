<?php

/**
 * Log out the current user.
 */

require_once dirname(__FILE__) . '/l/session.inc.php';

// Delete the session cookie
// Note: This does not invalidate the session token in the database.
// TODO: Invalidate the session token in the database for optimal security.
setCurrUser(NULL);

// Redirect the user to the referrer; they'll get redirected to the login page
// if the referrer requires an active user session
header('Location: ' . $_SERVER['HTTP_REFERER']);

