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
// Скрипт редактирования связей загружаемых типов файлов с досками.
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
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_BOARD_UPLOAD_TYPES'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$upload_types = upload_types_get_all();
	$boards = boards_get_all();
	$board_upload_types = board_upload_types_get_all();
	$reload_board_upload_types = false;	// Были ли произведены изменения.
	if(isset($_POST['submited']))
	{
		// Новая привязка типа загружаемого файла к доске.
		if(isset($_POST['new_bind_board'])
			&& isset($_POST['new_bind_upload_type'])
			&& $_POST['new_bind_board'] !== ''
			&& $_POST['new_bind_upload_type'] !== '')
		{
			board_upload_types_add(boards_check_id($_POST['new_bind_board']),
				upload_types_check_id($_POST['new_bind_upload_type']));
			$reload_board_upload_types = true;
		}
		// Удаление привязок типов загружаемых файлов к доскам.
		foreach($board_upload_types as $board_upload_type)
			if(isset($_POST["delete_{$board_upload_type['board']}_{$board_upload_type['upload_type']}"]))
			{
				board_upload_types_delete($board_upload_type['board'],
					$board_upload_type['upload_type']);
				$reload_board_upload_types = true;
			}
	}
	// Вывод формы редактирования.
	if($reload_board_upload_types)
		$board_upload_types = board_upload_types_get_all();
	DataExchange::releaseResources();
	$smarty->assign('upload_types', $upload_types);
	$smarty->assign('boards', $boards);
	$smarty->assign('board_upload_types', $board_upload_types);
	$smarty->display('edit_board_upload_types.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>