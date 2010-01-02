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
// Скрипт скрытия нитей.
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
	// Возможно завершение работы скрипта.
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
	if(is_guest())
		throw new PremissionException(PremissionException::$messages['GUEST']);
// Проверка входных параметров и скрытие нити.
	if(isset($_POST['thread']) && isset($_POST['board_name']))
	{
		$thread_id = threads_check_id($_POST['thread']);
		$board_name = boards_check_name($_POST['board_name']);
	}
	elseif(isset($_GET['thread']) && isset($_GET['board_name']))
	{
		$thread_id = threads_check_id($_GET['thread']);
		$board_name = boards_check_name($_GET['board_name']);
	}
	else
	{
		header('Location: http://z0r.de/?id=114');
		DataExchange::releaseResources();
		exit;
	}
	hidden_threads_add($thread_id, $_SESSION['user']);
// Перенаправление.
	DataExchange::releaseResources();
	header('Location: ' . Config::DIR_PATH . "/$board_name/");
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>