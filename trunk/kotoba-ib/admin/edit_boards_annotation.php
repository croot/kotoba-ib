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
// Скрипт редактирования аннотаций досок.
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
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_BOARDS_ANNOTATION'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$boards = boards_get_all();
	$reload_boards = false;	// Были ли произведены изменения.
	if(isset($_POST['submited']))
	{
// Изменение аннотаций существующих досок.
		foreach($boards as $board)
		{
			// Был ли изменена аннотация доски?
			$param_name = "annotation_{$board['id']}";
			$new_annotation = $board['annotation'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $board['annotation'])
			{
				if($_POST[$param_name] == '')
					$new_annotation = null;
				else
					$new_annotation = boards_check_annotation($_POST[$param_name]);
			}
			if($new_annotation != $board['annotation'])
			{
				boards_edit_annotation($board['id'], $new_annotation);
				$reload_boards = true;
			}
		}
	}
// Вывод формы редактирования.
	if($reload_boards)
		$boards = boards_get_all();
	DataExchange::releaseResources();
	$smarty->assign('boards', $boards);
	$smarty->display('edit_boards_annotation.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>