<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Session removal script.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';

kotoba_session_start();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - Config::SESSION_LIFETIME, '/');
}
session_destroy();
header('Location: ' . Config::DIR_PATH . '/');
?>