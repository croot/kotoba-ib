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

require '../kwrapper.php';
require_once Config::ABS_PATH . '/lang/' . Config::LANGUAGE . '/logging.php';

kotoba_setup($link, $smarty);
if(! in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'], $smarty, basename(__FILE__) . ' ' . __LINE__);
}
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'], 'Редактировать принадлежность пользователей группам', $_SESSION['user'], $_SERVER['REMOTE_ADDR']), Logmsgs::open_logfile(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log'));
$groups = db_group_get($link, $smarty);
/*
 * Добавим в список групп пользователей фиктивную группу Remove,
 * перезакрепление за которой будет означать удаление текущего закрепления.
 */
array_push($groups, array('id' => -1, 'name' => 'Remove'));
$user_groups = db_user_groups_get($link, $smarty);
$reload_user_groups = false;	// Были ли произведены изменения.
/*
 * Сначала добавим пользователя в новую группу.
 */
if(isset($_POST['new_bind_user']) && isset($_POST['new_bind_group']) && $_POST['new_bind_user'] != '')
{
	if(($new_bind_user = check_format('id', $_POST['new_bind_user'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['USER_ID'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_bind_group = check_format('id', $_POST['new_bind_group'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['GROUP_ID'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	db_user_groups_add($new_bind_user, $new_bind_group, $link, $smarty);
	$reload_user_groups = true;
}
/*
 * Переместим пользователя в другую группу или удалим из группы.
 */
foreach($user_groups as $user_group)
	if(isset($_POST["group_{$user_group['user']}_{$user_group['group']}"]))
		if($_POST["group_{$user_group['user']}_{$user_group['group']}"] != $user_group['group'])
		{
			if($_POST["group_{$user_group['user']}_{$user_group['group']}"] === '-1')
			{
				db_user_groups_delete($user_group['user'], $user_group['group'], $link, $smarty);
				$reload_user_groups = true;
				continue;
			}
			if(($new_group_id = check_format('id', $_POST["group_{$user_group['user']}_{$user_group['group']}"])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['GROUP_ID'], $smarty, basename(__FILE__) . ' ' . __LINE__);
			}
			db_user_groups_edit($user_group['user'], $user_group['group'], $new_group_id, $link, $smarty);
			$reload_user_groups = true;
		}
/*
 * Вывод формы редактирования.
 */
if($reload_user_groups)
	$user_groups = db_user_groups_get($link, $smarty);
mysqli_close($link);
$smarty->assign('groups', $groups);
$smarty->assign('user_groups', $user_groups);
$smarty->display('edit_user_groups.tpl');
?>