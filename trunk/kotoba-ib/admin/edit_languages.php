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
// Скрипт редактирования языков.
// TODO Добавить создание и удаление директорий при добавлении и удалении языка, соответственно.
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
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_LANGUAGES'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$languages = languages_get_all();
	$reload_languages = false;	// Были ли произведены изменения.
	// Добавление нового языка.
	if(isset($_POST['new_language']) && $_POST['new_language'] !== '')
	{
		languages_add(languages_check_name($_POST['new_language']));
		$reload_languages = true;
	}
	// Удаление языков.
	foreach($languages as $language)
		if(isset($_POST['delete_' . $language['id']]))
		{
			languages_delete($language['id']);
			$reload_languages = true;
		}
	// Вывод формы редактирования.
	if($reload_languages)
		$languages = languages_get_all();
	DataExchange::releaseResources();
	$smarty->assign('languages', $languages);
	$smarty->display('edit_languages.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>