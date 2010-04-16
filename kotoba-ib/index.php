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
// Скрипт главной страницы имейджборды.

require 'config.php';
require 'lib/errors.php';
require 'locale/' . Config::LANGUAGE . '/errors.php';
require 'lib/mysql.php';
require 'lib/db.php';
require 'lib/misc.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
	$boards = boards_get_visible($_SESSION['user']);
	if(count($boards) > 0)
	{
		$smarty->assign('boards_exist', true);
		$smarty->assign('boards', $boards);
	}
	$smarty->assign('version', '$Revision$');
	$smarty->assign('date', '$Date$');
	$smarty->assign('ib_name', Config::IB_NAME);
	$smarty->display('index.tpl');
	DataExchange::releaseResources();
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>
