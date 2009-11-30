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

/*
require 'kwrapper.php';
kotoba_setup($link, $smarty);
 */
if (isset($_COOKIE[session_name()]))
    setcookie(session_name(), '', time() - 42000, '/');	// Удаление куки.

if(isset($_SESSION['user']))
	unset($_SESSION['user']);

session_destroy();
?>