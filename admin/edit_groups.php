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
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'], 'Редактировать группы пользователей', $_SESSION['user'], $_SERVER['REMOTE_ADDR']), Logmsgs::open_logfile(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log'));
$groups = db_group_get($link, $smarty);
$delete_list = array();		// Массив имён групп для удаления.
$reload_groups = false;	// Были ли произведены изменения.
/*
 * Сначала создадим группу, если передано её имя.
 */
if(isset($_POST['new_group']) && $_POST['new_group'] !== '')
{
	if(($new_group_name = check_format('group', $_POST['new_group'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['GROUP_NAME'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	db_group_add($new_group_name, $link, $smarty);
	$reload_groups = true;
}
/*
 * Затем удалим группы, которые были выбраны.
 */
foreach($groups as $group)
	if(isset($_POST['delete_' . $group['name']]))
		array_push($delete_list, $group['id']);
if(count($delete_list) > 0)
{
	db_group_delete($delete_list, $link, $smarty);
	$reload_groups = true;
}
/*
 * Если группы не удалялись и не добавлялись, то получать их список заново
 * не нужно.
 */
if($reload_groups)
	$groups = db_group_get($link, $smarty);
mysqli_close($link);
$smarty->assign('groups', $groups);
$smarty->display('edit_groups.tpl');
?>