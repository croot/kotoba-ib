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
var_dump($_SESSION['groups']);
if(! in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'],
		$smarty, basename(__FILE__) . ' ' . __LINE__);
}
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'],
		'Редактирование досок', $_SESSION['user'], $_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$popdown_handlers = db_popdown_handlers_get($link, $smarty);
$categories = db_categories_get($link, $smarty);
$boards = db_boards_get_all($link, $smarty);
$reload_boards = false;	// Были ли произведены изменения.
/*
 * Создание новой доски.
 */
if(isset($_POST['submited']) &&
	isset($_POST['new_name']) &&
	isset($_POST['new_title']) &&
	isset($_POST['new_bump_limit']) &&
	isset($_POST['new_same_upload']) &&
	isset($_POST['new_popdown_handler']) &&
	isset($_POST['new_category']) &&
	$_POST['new_name'] !== '' &&
	$_POST['new_bump_limit'] !== '' &&
	$_POST['new_same_upload'] !== '' &&
	$_POST['new_popdown_handler'] !== '' &&
	$_POST['new_category'] !== '')
{
	/*
	 * Проверка входных параметров.
	 */
	if(($new_name = check_format('board', $_POST['new_name'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BOARD_NAME'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if($_POST['new_title'] === '')
		$new_title = '';
	elseif((($new_title = check_format('board_title',
					$_POST['new_title'])) == false))
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BOARD_TITLE'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_bump_limit = check_format('id',
				$_POST['new_bump_limit'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BUMP_LIMIT'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_same_upload = check_format('same_upload',
				$_POST['new_same_upload'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['SAME_UPLOAD'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_popdown_handler = check_format('id',
				$_POST['new_popdown_handler'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['POPDOWN_HANDLER_ID'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$found = false;
	foreach($popdown_handlers as $popdown_handler)
		if($popdown_handler['id'] == $new_popdown_handler)
		{
			$found = true;
			break;
		}
	if(! $found)
	{
		mysqli_close($link);
		kotoba_error(sprintf(Errmsgs::$messages['POPDOWN_HANDLER_NOT_FOUND'],
				$new_popdown_handler),
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_category = check_format('id',
				$_POST['new_category'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['CATEGORY_ID'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$found = false;
	foreach($categories as $category)
		if($category['id'] == $new_category)
		{
			$found = true;
			break;
		}
	if(! $found)
	{
		mysqli_close($link);
		kotoba_error(sprintf(Errmsgs::$messages['CATEGORY_NOT_FOUND'],
				$new_category),
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	/*
	 * Проверим, нет ли уже доски с таким именем. Если есть, то изменим
	 * параметры существующей, если нет, то добавим новую.
	 */
	$found = false;
	foreach($boards as $board)
	{
		if($board['name'] == $new_name)
		{
			$found = true;
			db_boards_edit($board['id'], $new_title, $new_bump_limit,
				$new_same_upload, $new_popdown_handler, $new_category,
				$link, $smarty);
			$reload_boards = true;
		}
	}
	if(! $found)
	{
		db_boards_add($new_name, $new_title, $new_bump_limit, $new_same_upload,
			$new_popdown_handler, $new_category, $link, $smarty);
		create_directories($new_name);
		$reload_boards = true;
	}
}
/*
 * Изменение параметров существующих досок.
 */
if(isset($_POST['submited']))
	foreach($boards as $board)
	{
		$param_name = "title_{$board['id']}";
		$new_title = $board['title'];
		if(isset($_POST[$param_name]) && $_POST[$param_name] != $board['title'])
		{
			if($_POST[$param_name] === '')
				$new_title = '';
			elseif((($new_title = check_format('board_title',
								$_POST[$param_name])) == false))
				{
					mysqli_close($link);
					kotoba_error(Errmsgs::$messages['BOARD_TITLE'],
						$smarty, basename(__FILE__) . ' ' . __LINE__);
				}
		}
		$param_name = "bump_limit_{$board['id']}";
		$new_bump_limit = $board['bump_limit'];
		if(isset($_POST[$param_name]) &&
			$_POST[$param_name] != $board['bump_limit'])
		{
			if(($new_bump_limit = check_format('id',
						$_POST[$param_name])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['BUMP_LIMIT'],
					$smarty, basename(__FILE__) . ' ' . __LINE__);
			}
		}
		$param_name = "same_upload_{$board['id']}";
		$new_same_upload = $board['same_upload'];
		if(isset($_POST[$param_name]) &&
			$_POST[$param_name] != $board['same_upload'])
		{
			if(($new_same_upload = check_format('same_upload',
						$_POST[$param_name])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['SAME_UPLOAD'],
					$smarty, basename(__FILE__) . ' ' . __LINE__);
			}
		}
		$param_name = "popdown_handler_{$board['id']}";
		$new_popdown_handler = $board['popdown_handler'];
		if(isset($_POST[$param_name]) &&
			$_POST[$param_name] != $board['popdown_handler'])
		{
			if(($new_popdown_handler = check_format('id',
						$_POST[$param_name])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['POPDOWN_HANDLER_ID'],
					$smarty, basename(__FILE__) . ' ' . __LINE__);
			}
			$found = false;
			foreach($popdown_handlers as $popdown_handler)
				if($popdown_handler['id'] == $new_popdown_handler)
				{
					$found = true;
					break;
				}
			if(! $found)
			{
				mysqli_close($link);
				kotoba_error(sprintf(
						Errmsgs::$messages['POPDOWN_HANDLER_NOT_FOUND'],
						$new_popdown_handler),
					$smarty, basename(__FILE__) . ' ' . __LINE__);
			}
		}
		$param_name = "category_{$board['id']}";
		$new_category = $board['category'];
		if(isset($_POST[$param_name]) &&
			$_POST[$param_name] != $board['category'])
		{
			if(($new_category = check_format('id',
						$_POST[$param_name])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['CATEGORY_ID'],
					$smarty, basename(__FILE__) . ' ' . __LINE__);
			}
			$found = false;
			foreach($categories as $category)
				if($category['id'] == $new_category)
				{
					$found = true;
					break;
				}
			if(! $found)
			{
				mysqli_close($link);
				kotoba_error(sprintf(Errmsgs::$messages['CATEGORY_NOT_FOUND'],
						$new_category),
					$smarty, basename(__FILE__) . ' ' . __LINE__);
			}
		}
		/*
		 * Если были изменения.
		 */
		if($new_title != $board['title'] ||
			$new_bump_limit != $board['bump_limit'] ||
			$new_same_upload != $board['same_upload'] ||
			$new_popdown_handler != $board['popdown_handler'] ||
			$new_category != $board['category'])
		{
			db_boards_edit($board['id'], $new_title, $new_bump_limit,
				$new_same_upload, $new_popdown_handler, $new_category,
				$link, $smarty);
			$reload_boards = true;
		}
	}
/*
 * Удаление выбранных досок.
 */
if(isset($_POST['submited']))
	foreach($boards as $board)
		if(isset($_POST["delete_{$board['id']}"]))
		{
			db_boards_delete($board['id'], $link, $smarty);
			$reload_boards = true;
		}
/*
 * Обновление списка досок, если нужно. Вывод формы редактирования.
 */
if($reload_boards)
	$boards = db_boards_get_all($link, $smarty);
mysqli_close($link);
$smarty->assign('popdown_handlers', $popdown_handlers);
$smarty->assign('categories', $categories);
$smarty->assign('boards', $boards);
$smarty->display('edit_boards.tpl');
?>
