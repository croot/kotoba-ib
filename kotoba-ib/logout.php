<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Session removal script.

require_once 'config.php';

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - Config::SESSION_LIFETIME, '/');
}

if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

session_destroy();
?>