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
// Скрипт редактирования категорий досок.
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
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_CATEGORIES'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$categories = categories_get_all();
	$reload_categories = false;	// Были ли произведены изменения.
	// Добавление категории досок.
	if(isset($_POST['new_category']) && $_POST['new_category'] !== '')
	{
		categories_add(categories_check_name($_POST['new_category']));
		$reload_categories = true;
	}
	// Удаление категорий.
	foreach($categories as $category)
		if(isset($_POST['delete_' . $category['id']]))
		{
			categories_delete($category['id']);
			$reload_categories = true;
		}
	// Вывод формы редактирования.
	if($reload_categories)
		$categories = categories_get_all();
	DataExchange::releaseResources();
	$smarty->assign('categories', $categories);
	$smarty->display('edit_categories.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>