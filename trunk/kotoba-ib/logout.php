<?php
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/k/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

if (isset($_COOKIE[session_name()]))
{
    setcookie(session_name(), '', time() - 42000, '/');
}

if(isset($_SESSION['isLoggedIn']))
{
	unset($_SESSION['isLoggedIn']);
}

session_destroy();
?>
<html>
<head>
	<title>Kotoba Logout</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="kotoba.css">
</head>
<body>
<p>
	Logged out.
	<br><a href="login.php">[Login]</a>
</p>
</body>
</html>