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
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'],
		$smarty,
		basename(__FILE__) . ' ' . __LINE__);
}
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'],
		'Редактирование типов загружаемых файлов для досок',
		$_SESSION['user'],
		$_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$upload_types = db_upload_types_get($link, $smarty);
$boards = db_boards_get_all($link, $smarty);
$board_upload_types = db_board_upload_types_get($link, $smarty);
$reload_board_upload_types = false;	// Были ли произведены изменения.
/*
 * Создадим новую привязку типа загружаемого файла к доске.
 */
if(isset($_POST['submited']) &&
	isset($_POST['new_bind_board']) &&
	isset($_POST['new_bind_upload_type']) &&
	$_POST['new_bind_board'] !== '' &&
	$_POST['new_bind_upload_type'] !== '')
{
	/*
	 * Проверка входных данных.
	 */
	if(($board_id = check_format('id', $_POST['new_bind_board'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BOARD_ID'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	$found = false;
	foreach($boards as $board)
		if($board['id'] == $board_id)
		{
			$found = true;
			break;
		}
	if(! $found)
	{
		mysqli_close($link);
		kotoba_error(sprintf(Errmsgs::$messages['BOARD_ID_NOT_FOUND'],
				$board_id),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	if(($upload_type_id = check_format('id', $_POST['new_bind_upload_type'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['UPLOAD_TYPE_ID'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	$found = false;
	foreach($upload_types as $upload_type)
		if($upload_type['id'] == $upload_type_id)
		{
			$found = true;
			break;
		}
	if(! $found)
	{
		mysqli_close($link);
		kotoba_error(sprintf(Errmsgs::$messages['UPLOAD_TYPE_ID_NOT_FOUND'],
				$upload_type_id),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	/*
	 * Добавление типа загружаемого файла для доски.
	 */
	db_board_upload_types_add($board_id, $upload_type_id, $link, $smarty);
	$reload_board_upload_types = true;
}
/*
 * Удалим выбранные привязки типов загружаемых файлов к доскам.
 */
if(isset($_POST['submited']))
	foreach($board_upload_types as $board_upload_type)
		if(isset($_POST["delete_{$board_upload_type['board']}_{$board_upload_type['upload_type']}"]))
		{
			db_board_upload_types_delete($board_upload_type['board'], $board_upload_type['upload_type'], $link, $smarty);
			$reload_board_upload_types = true;
		}
/*
 * Обновление списка типов файлов для досок, если нужно.
 * Вывод формы редактирования.
 */
if($reload_board_upload_types)
	$board_upload_types = db_board_upload_types_get($link, $smarty);
mysqli_close($link);
$smarty->assign('upload_types', $upload_types);
$smarty->assign('boards', $boards);
$smarty->assign('board_upload_types', $board_upload_types);
$smarty->display('edit_board_upload_types.tpl');
?>