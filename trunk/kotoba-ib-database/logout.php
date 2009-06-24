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

kotoba_setup();

if (isset($_COOKIE[session_name()]))
    setcookie(session_name(), '', time() - 42000, '/');	// Удаление куки.

if(isset($_SESSION['isLoggedIn']))
	unset($_SESSION['isLoggedIn']);

session_destroy();
$smarty = new SmartyKotobaSetup();

$smarty->display('logout.tpl');
?>
