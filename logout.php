<?php

require_once 'l/session.inc.php';

setCurrUser(NULL);

header('Location: ' . $_SERVER['HTTP_REFERER']);

