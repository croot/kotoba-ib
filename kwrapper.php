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

require_once 'config.php';
if(!isset($_SESSION['language'])) {
	$_SESSION['language'] = Config::LANGUAGE;
}
require_once "lang/$_SESSION[language]/errors.php";
require_once 'common.php';
?>
