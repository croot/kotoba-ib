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
// Скрипт редактирования типов загружаемых файлов.
require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_UPLOAD_TYPES'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$upload_handlers = upload_handlers_get_all();
	$upload_types = upload_types_get_all();
	$reload_upload_types = false;	// Были ли произведены изменения.
	if(isset($_POST['submited']))
	{
		// Добавление нового типа загружаемых файлов.
		if(isset($_POST['new_extension'])
			&& isset($_POST['new_store_extension'])
			&& isset($_POST['new_upload_handler'])
			&& isset($_POST['new_thumbnail_image'])
			&& $_POST['new_extension'] !== ''
			&& $_POST['new_store_extension'] !== ''
			&& $_POST['new_upload_handler'] !== '')
		{
			$new_extension =  upload_types_check_extension($_POST['new_extension']);
			$new_store_extension = upload_types_check_store_extension($_POST['new_store_extension']);
			$new_is_image = isset($_POST['new_is_image']) ? 1 : 0;
			$new_upload_handler_id = upload_handlers_check_id($_POST['new_upload_handler']);
			if($_POST['new_thumbnail_image'] === '')
				$new_thumbnail_image = null;
			else
				$new_thumbnail_image = upload_types_check_thumbnail_image($_POST['new_thumbnail_image']);
			/*
			 * Проверим, нет ли уже такого типа загружаемых файлов, если есть,
			 * то изменим существующий.
			 */
			$found = false;
			foreach($upload_types as $upload_type)
				if($upload_type['extension'] == $new_extension && $found = true)
				{
					upload_types_edit($upload_type['id'], $new_store_extension,
						$new_is_image, $new_upload_handler_id,
						$new_thumbnail_image);
					$reload_upload_types = true;
					break;
				}
			if(!$found)
			{
				upload_types_add($new_extension, $new_store_extension,
						$new_is_image, $new_upload_handler_id,
						$new_thumbnail_image);
					$reload_upload_types = true;
			}
		}// Добавление нового типа загружаемых файлов.
		// Изменение существующего типа загружаемых файлов.
		foreach($upload_types as $upload_type)
		{
			// Было изменено сохраняемое расширеное файла?
			$param_name = 'store_extension_' . $upload_type['id'];
			$new_store_extension = $upload_type['store_extension'];
			if(isset($_POST[$param_name]) && ($_POST[$param_name] !=
					$upload_type['store_extension']))
			{
				$new_store_extension = upload_types_check_store_extension($_POST[$param_name]);
			}
			// Был изменен флаг изображения?
			$param_name = 'is_image_' . $upload_type['id'];
			$new_is_image = $upload_type['is_image'];
			if(isset($_POST[$param_name]) && ($_POST[$param_name] !=
					$upload_type['is_image']))
			{
				$new_is_image = 1;
			}
			if(!isset($_POST[$param_name]) && $upload_type['is_image'])
			{
				$new_is_image = 0;
			}
			// Был изменён обработчик загружаемых файлов?
			$param_name = 'upload_handler_' . $upload_type['id'];
			$new_upload_handler_id = $upload_type['upload_handler'];
			if(isset($_POST[$param_name]) && ($_POST[$param_name] !=
					$upload_type['upload_handler']))
			{
				$new_upload_handler_id = upload_handlers_check_id($_POST[$param_name]);
			}
			// Было изменено имя картинки для файлов, не являющихся изображением?
			$param_name = 'thumbnail_image_' . $upload_type['id'];
			$new_thumbnail_image = $upload_type['thumbnail_image'];
			if(isset($_POST[$param_name]) && ($_POST[$param_name] !=
					$upload_type['thumbnail_image']))
			{
				if($_POST[$param_name] === '')
					$new_thumbnail_image = null;
				else
					$new_thumbnail_image = upload_types_check_thumbnail_image($_POST[$param_name]);
			}
			// Было произведено хотя бы одно изменение?
			if($new_store_extension != $upload_type['store_extension']
				|| $new_is_image != $upload_type['is_image']
				|| $new_upload_handler_id != $upload_type['upload_handler']
				|| $new_thumbnail_image != $upload_type['thumbnail_image'])
			{
				upload_types_edit($upload_type['id'], $new_store_extension,
					$new_is_image, $new_upload_handler_id, $new_thumbnail_image);
				$reload_upload_types = true;
			}
		}// Изменение существующего типа загружаемых файлов.
		// Удаление типа загружаемых файлов.
		foreach($upload_types as $upload_type)
			if(isset($_POST['delete_' . $upload_type['id']]))
			{
				upload_types_delete($upload_type['id']);
				$reload_upload_types = true;
			}
	}
	// Вывод формы редактирования.
	if($reload_upload_types)
		$upload_types = upload_types_get_all();
	DataExchange::releaseResources();
	$smarty->assign('upload_handlers', $upload_handlers);
	$smarty->assign('upload_types', $upload_types);
	$smarty->display('edit_upload_types.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>