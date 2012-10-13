<?php

require_once dirname(__FILE__) . '/l/session.inc.php';

setCurrUser(NULL);

header('Location: ' . $_SERVER['HTTP_REFERER']);

