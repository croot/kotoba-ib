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
require 'modules/errors.php';
require 'modules/lang/' . Config::LANGUAGE . '/errors.php';
require 'modules/db.php';
require 'modules/cache.php';
require 'modules/common.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	$boards = boards_get_all_view($_SESSION['user']);
	if(count($boards) > 0)
	{
		$smarty->assign('boards_exist', true);
		$smarty->assign('boards', $boards);
	}
	if(in_array(Config::MOD_GROUP_NAME, $_SESSION['groups']))
		$smarty->assign('mod_panel', true);
	elseif(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$smarty->assign('adm_panel', true);
	$smarty->assign('version', '$Revision$');
	$smarty->assign('date', '$Date$');
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