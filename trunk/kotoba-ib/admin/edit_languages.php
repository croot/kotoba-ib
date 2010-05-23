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
require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_LANGUAGES'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$languages = languages_get_all();
	$reload_languages = false;	// Были ли произведены изменения.
	// Добавление нового языка.
	if(isset($_POST['new_language']) && $_POST['new_language'] !== '')
	{
		$name = languages_check_name($_POST['new_language']);
		languages_add($name);
		create_language_directories($name);
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