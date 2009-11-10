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
/*
 * Только для администраторов.
 */
if(! in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'],
		$smarty,
		basename(__FILE__) . ' ' . __LINE__);
}
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'],
		'Редактирование категорий досок',
		$_SESSION['user'],
		$_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
try
{
	$categories = db_categories_get($link);
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	if(isset($link) && $link instanceof MySQLi)
		mysqli_close($link);
	die($smarty->fetch('error.tpl'));
}
$reload_categories = false;	// Были ли произведены изменения.
/*
 * Добавим категорию досок.
 */
if(isset($_POST['new_category']) && $_POST['new_category'] !== '')
{
	if(($new_category_name = check_format('category', $_POST['new_category'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['CATEGORY_NAME'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	db_categories_add($new_category_name, $link, $smarty);
	$reload_categories = true;
}
/*
 * Удалим категории.
 */
foreach($categories as $category)
	if(isset($_POST['delete_' . $category['id']]))
	{
		db_categories_delete($category['id'], $link, $smarty);
		$reload_categories = true;
	}
/*
 * Если нужно, получение обновлённого списка категорий досок, вывод формы
 * редактирования.
 */
if($reload_categories)
{
	try
	{
		$categories = db_categories_get($link);
	}
	catch(Exception $e)
	{
		$smarty->assign('msg', $e->__toString());
		if(isset($link) && $link instanceof MySQLi)
			mysqli_close($link);
		die($smarty->fetch('error.tpl'));
	}
}
mysqli_close($link);
$smarty->assign('categories', $categories);
$smarty->display('edit_categories.tpl');
?>