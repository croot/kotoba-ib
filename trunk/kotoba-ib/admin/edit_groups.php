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
// Скрипт редактирования групп.
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
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_GROUPS'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	Logging::close_logfile();
	$groups = groups_get_all();
	$delete_list = array();		// Массив идентификаторов групп для удаления.
	$reload_groups = false;		// Были ли произведены изменения в группах.
	/* Если создаётся новая группа. */
	if(isset($_POST['new_group']) && $_POST['new_group'] !== '')
	{
		$new_group = groups_check_name($_POST['new_group']);
		groups_add($new_group);
		$reload_groups = true;
	}
	/* Удаление группы. */
	foreach($groups as $group)
		if(isset($_POST['delete_' . $group['id']]))
			array_push($delete_list, $group['id']);
	if(count($delete_list) > 0)
	{
		groups_delete($delete_list);
		$reload_groups = true;
	}
	if($reload_groups)
		$groups = groups_get_all();
	DataExchange::releaseResources();
	$smarty->assign('groups', $groups);
	$smarty->display('edit_groups.tpl');
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>