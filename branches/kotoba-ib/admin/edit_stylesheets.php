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
// Скрипт редактирования стилей оформления.
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
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_STYLESHEETS'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$stylesheets = stylesheets_get_all();
	$reload_stylesheets = false;	// Были ли произведены изменения.
	// Добавление стиля оформления.
	if(isset($_POST['new_stylesheet']) && $_POST['new_stylesheet'] !== '')
	{
		stylesheets_add(stylesheets_check_name($_POST['new_stylesheet']));
		$reload_stylesheets = true;
	}
	// Удаление стилей оформления.
	foreach($stylesheets as $stylesheet)
		if(isset($_POST['delete_' . $stylesheet['id']]))
		{
			stylesheets_delete($stylesheet['id']);
			$reload_stylesheets = true;
		}
	// Вывод формы редактирования.
	if($reload_stylesheets)
		$stylesheets = stylesheets_get_all();
	DataExchange::releaseResources();
	$smarty->assign('stylesheets', $stylesheets);
	$smarty->display('edit_stylesheets.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>