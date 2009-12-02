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
// Скрипт редактирования досок.
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
	$smarty = new SmartyKotobaSetup($_SESSION['language'],
		$_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_BOARDS'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$popdown_handlers = popdown_handlers_get_all();
	$categories = categories_get_all();
	$boards = boards_get_all();
	$reload_boards = false;	// Были ли произведены изменения.
	if(isset($_POST['submited']))
	{
// Создание новой доски.
		if(isset($_POST['new_name'])
			&& isset($_POST['new_title'])
			&& isset($_POST['new_bump_limit'])
			&& isset($_POST['new_default_name'])
			&& isset($_POST['new_same_upload'])
			&& isset($_POST['new_popdown_handler'])
			&& isset($_POST['new_category'])
			&& $_POST['new_name'] != ''
			&& $_POST['new_bump_limit'] != ''
			&& $_POST['new_same_upload'] != ''
			&& $_POST['new_popdown_handler'] != ''
			&& $_POST['new_category'] != '')
		{
			$new_name = boards_check_name($_POST['new_name']);
			if($_POST['new_title'] == '')
				$new_title = null;
			else
				$new_title = boards_check_title($_POST['new_title']);
			$new_bump_limit = boards_check_bump_limit($_POST['new_bump_limit']);
			if(isset($_POST['new_force_anonymous']))
				$new_force_anonymous = '1';
			else
				$new_force_anonymous = '0';
			if($_POST['new_default_name'] == '')
				$new_default_name = null;
			else
				$new_default_name = boards_check_default_name($_POST['new_default_name']);
			if(isset($_POST['new_with_files']))
				$new_with_files = '1';
			else
				$new_with_files = '0';
			$new_same_upload = boards_check_same_upload($_POST['new_same_upload']);
			$new_popdown_handler = popdown_handlers_check_id($_POST['new_popdown_handler']);
			$new_category = categories_check_id($_POST['new_category']);
			/*
			 * Проверим, нет ли уже доски с таким именем. Если есть, то изменим
			 * параметры существующей.
			 */
			$found = false;
			foreach($boards as $board)
				if($board['name'] == $new_name && $found = true)
				{
					boards_edit($board['id'], $new_title, $new_bump_limit,
						$new_force_anonymous, $new_default_name,
						$new_with_files, $new_same_upload, $new_popdown_handler,
						$new_category);
					$reload_boards = true;
					break;
				}
			if(!$found)
			{
				boards_add($new_name, $new_title, $new_bump_limit,
					$new_force_anonymous, $new_default_name, $new_with_files,
					$new_same_upload, $new_popdown_handler, $new_category);
				create_directories($new_name);
				$reload_boards = true;
			}
		}// Создание новой доски.
// Изменение параметров существующих досок.
		foreach($boards as $board)
		{
			// Был ли изменён заголовок доски?
			$param_name = "title_{$board['id']}";
			$new_title = $board['title'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $board['title'])
			{
				if($_POST[$param_name] == '')
					$new_title = null;
				else
					$new_title = boards_check_title($_POST[$param_name]);
			}
			// Был ли изменён специфичный для доски бамплимит?
			$param_name = "bump_limit_{$board['id']}";
			$new_bump_limit = $board['bump_limit'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $board['bump_limit'])
			{
				$new_bump_limit = boards_check_bump_limit($_POST[$param_name]);
			}
			// Был ли изменен флаг отображения имени отправителя.
			$param_name = "force_anonymous_{$board['id']}";
			$new_force_anonymous = $board['force_anonymous'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $board['force_anonymous'])
			{
				// Флаг был установлен 0 -> 1
				$new_force_anonymous = '1';
			}
			if(!isset($_POST[$param_name]) && $board['force_anonymous'])
			{
				// Флаг был снят 1 -> 0
				$new_force_anonymous = '0';
			}
			// Было ли изменено имя по умолчанию?
			$param_name = "default_name_{$board['id']}";
			$new_default_name = $board['default_name'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $board['default_name'])
			{
				if($_POST[$param_name] == '')
					$new_default_name = null;
				else
					$new_default_name = boards_check_default_name($_POST[$param_name]);
			}
			// Был ли изменен флаг загрузки файлов.
			$param_name = "with_files_{$board['id']}";
			$new_with_files = $board['with_files'];
			if(isset($_POST[$param_name])
					&& $_POST[$param_name] != $board['with_files'])
			{
				// Флаг был установлен 0 -> 1
				$new_with_files = '1';
			}
			if(!isset($_POST[$param_name]) && $board['with_files'])
			{
				// Флаг был снят 1 -> 0
				$new_with_files = '0';
			}
			// Была ли изменена политика загрузки одинаковых файлов?
			$param_name = "same_upload_{$board['id']}";
			$new_same_upload = $board['same_upload'];
			if(isset($_POST[$param_name]) &&
				$_POST[$param_name] != $board['same_upload'])
			{
				$new_same_upload = boards_check_same_upload($_POST[$param_name]);
			}
			// Был ли изменён обработчик удаления нитей?
			$param_name = "popdown_handler_{$board['id']}";
			$new_popdown_handler = $board['popdown_handler'];
			if(isset($_POST[$param_name]) &&
				$_POST[$param_name] != $board['popdown_handler'])
			{
				$new_popdown_handler = popdown_handlers_check_id($_POST[$param_name]);
			}
			// Была ли изменена категория доски?
			$param_name = "category_{$board['id']}";
			$new_category = $board['category'];
			if(isset($_POST[$param_name]) &&
				$_POST[$param_name] != $board['category'])
			{
				$new_category = categories_check_id($_POST[$param_name]);
			}
			// Были ли произведены какие-либо изменения?
			if($new_title != $board['title']
				|| $new_bump_limit != $board['bump_limit']
				|| $new_force_anonymous != $board['force_anonymous']
				|| $new_default_name != $board['default_name']
				|| $new_with_files != $board['with_files']
				|| $new_same_upload != $board['same_upload']
				|| $new_popdown_handler != $board['popdown_handler']
				|| $new_category != $board['category'])
			{
				boards_edit($board['id'], $new_title, $new_bump_limit,
					$new_force_anonymous, $new_default_name, $new_with_files,
					$new_same_upload, $new_popdown_handler, $new_category);
				$reload_boards = true;
			}
		}// Изменение параметров существующих досок.
// Удаление выбранных досок.
		foreach($boards as $board)
			if(isset($_POST["delete_{$board['id']}"]))
			{
				boards_delete($board['id']);
				$reload_boards = true;
			}
	}
// Вывод формы редактирования.
	if($reload_boards)
		$boards = boards_get_all();
	DataExchange::releaseResources();
	$smarty->assign('popdown_handlers', $popdown_handlers);
	$smarty->assign('categories', $categories);
	$smarty->assign('boards', $boards);
	$smarty->display('edit_boards.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>