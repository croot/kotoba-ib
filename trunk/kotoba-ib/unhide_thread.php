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
// Скрипт отмены скрытия нити.
require 'config.php';
require 'modules/errors.php';
require 'modules/lang/' . Config::LANGUAGE . '/errors.php';
require 'modules/db.php';
require 'modules/cache.php';
require 'modules/common.php';
require 'modules/popdown_handlers.php';
require 'modules/events.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'],
		$_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(in_array(Config::GST_GROUP_NAME, $_SESSION['groups']))
		throw new PremissionException(PremissionException::$messages['GUEST']);
// Проверка входных параметров.
	if(isset($_GET['board']) && isset($_GET['thread']))
	{
		$board = boards_get_specifed_byname(boards_check_name($_GET['board']));
		$thread = threads_get_specifed_view_hiden($board['id'],
			threads_check_number($_GET['thread']), $_SESSION['user']);
// Отмена скрытия нити.
		hidden_threads_delete($thread['id'], $_SESSION['user']);
	}
// Перенаправление.
	DataExchange::releaseResources();
	header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>