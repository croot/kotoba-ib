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

kotoba_setup($link, $smarty);
if(! in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'], $smarty, basename(__FILE__) . ' ' . __LINE__);
}
$group_list = db_get_group_list($link, $smarty);
$delete_list = array();		// Массив имён групп для удаления.
$reload_group_list = false;	// Были ли произведены изменения.
/*
 * Сначала создадим группу, если передано её имя.
 */
if(isset($_POST['new_group']) && $_POST['new_group'] !== '')
{
	$new_group_name = check_format('group', $_POST['new_group']);
	if($new_group_name == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['GROUP_NAME'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	db_group_add($new_group_name, $link, $smarty);
	$reload_group_list = true;
}
/*
 * Затем удалим группы, которые были выбраны.
 */
foreach($group_list as $group_name)
	if(isset($_POST['delete_' . $group_name]))
		array_push($delete_list, $group_name);
if(count($delete_list) > 0)
{
	db_group_delete($delete_list, $link, $smarty);
	$reload_group_list = true;
}
/*
 * Если группы не удалялись и не добавлялись, то получать их список заново
 * не нужно.
 */
if($reload_group_list)
	$group_list = db_get_group_list($link, $smarty);
mysqli_close($link);
$smarty->assign('group_list', $group_list);
$smarty->display('edit_groups.tpl');
?>