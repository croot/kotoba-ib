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

/*
 * Скприпт редактирования принадлежности пользователей группам.
 */

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
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_USER_GROUPS'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$groups = groups_get_all();
	$users = users_get_all();
	$user_groups = user_groups_get_all();
	$reload_user_groups = false;	// Были ли произведены изменения.
	/* Добавление нового закрепления. */
	if(isset($_POST['new_bind_user']) && isset($_POST['new_bind_group'])
		&& $_POST['new_bind_user'] != '' && $_POST['new_bind_group'] != '')
	{
		$new_bind_user = users_check_id($_POST['new_bind_user']);
		$new_bind_group = groups_check_id($_POST['new_bind_group']);
		user_groups_add($new_bind_user, $new_bind_group);
		$reload_user_groups = true;
	}
	/* Перезакрепление пользователя. */
	foreach($user_groups as $user_group)
		if(isset($_POST["group_{$user_group['user']}_{$user_group['group']}"])
			&& $_POST["group_{$user_group['user']}_{$user_group['group']}"] != $user_group['group'])
		{
			$new_group_id = groups_check_id($_POST["group_{$user_group['user']}_{$user_group['group']}"]);
			foreach($groups as $group)
				if($group['id'] == $new_group_id)
				{
					user_groups_edit($user_group['user'], $user_group['group'], $new_group_id);
					$reload_user_groups = true;
				}
		}
	/* Удаление закреплений. */
	foreach($user_groups as $user_group)
		if(isset($_POST["delete_{$user_group['user']}_{$user_group['group']}"]))
		{
			user_groups_delete($user_group['user'], $user_group['group']);
			$reload_user_groups = true;
		}
	if($reload_user_groups)
	{
		$groups = groups_get_all();
		$users = users_get_all();
		$user_groups = user_groups_get_all();
	}
	DataExchange::releaseResources();
	$smarty->assign('groups', $groups);
	$smarty->assign('users', $users);
	$smarty->assign('user_groups', $user_groups);
	$smarty->display('edit_user_groups.tpl');
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>