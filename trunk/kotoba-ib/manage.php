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
// Скрипт страницы административных фукнций и фукнций модераторов.
require 'config.php';
require 'modules/errors.php';
require 'modules/lang/' . Config::LANGUAGE . '/errors.php';
require 'modules/logging.php';
require 'modules/lang/' . Config::LANGUAGE . '/logging.php';
require 'modules/db.php';
require 'modules/cache.php';
require 'modules/common.php';
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
	if(in_array(Config::MOD_GROUP_NAME, $_SESSION['groups']))
		$smarty->assign('mod_panel', true);
	elseif(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$smarty->assign('adm_panel', true);
	DataExchange::releaseResources();
	$smarty->display('manage.tpl');
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>