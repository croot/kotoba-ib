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
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'],
		$_SESSION['stylesheet']);
	// Возможно завершение работы скрипта.
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
	if(is_guest())
		throw new PermissionException(PermissionException::$messages['GUEST']);
// Проверка входных параметров и отмена скрытия нити.
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
	hidden_threads_delete($thread_id, $_SESSION['user']);
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