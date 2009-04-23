<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

require 'config.php';
require 'common.php';

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

if (isset($_COOKIE[session_name()]))
    setcookie(session_name(), '', time() - 42000, '/');	// Удаление куки.

if(isset($_SESSION['isLoggedIn']))
	unset($_SESSION['isLoggedIn']);

session_destroy();

die("<html>\n<head>\n\t<title>Kotoba Logout</title>\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n\t" .
	"<link rel=\"stylesheet\" type=\"text/css\" href=\"" . KOTOBA_DIR_PATH . "/kotoba.css\">\n</head>\n<body>\n<p>\n\t" .
	"Вы вышли.<br>\n\t<a href=\"" . KOTOBA_DIR_PATH . "/login.php\">Войти</a><br>\n\t" .
	"<a href=\"" . KOTOBA_DIR_PATH . "/index.php\">На главную</a>\n</p>\n</body>\n</html>");
?>
