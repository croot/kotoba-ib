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
// Скрипт для блокировки диапазона IP адресов в фаерволе.
require '../config.php';
require Config::ABS_PATH . '/modules/errors.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/modules/logging.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/modules/db.php';
require Config::ABS_PATH . '/modules/cache.php';
require Config::ABS_PATH . '/modules/common.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_BANS'],
					$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
				Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	elseif(in_array(Config::MOD_GROUP_NAME, $_SESSION['groups']))
		Logging::write_message(sprintf(Logging::$messages['MOD_FUNCTIONS_EDIT_BANS'],
					$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
				Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	else
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']
		 . PremissionException::$messages['NOT_MOD']);
	
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