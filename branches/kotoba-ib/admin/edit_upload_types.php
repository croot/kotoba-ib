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

require_once '../kwrapper.php';
require_once Config::ABS_PATH . '/lang/' . Config::LANGUAGE . '/logging.php';

kotoba_setup($link, $smarty);
if(! in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'],
		$smarty,
		basename(__FILE__) . ' ' . __LINE__);
}
// TODO локализация действий, записывающихся в лог.
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'],
		'Редактирование типов загружаемых файлов',
		$_SESSION['user'],
		$_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$upload_handlers = db_upload_handlers_get($link, $smarty);
$upload_types = db_upload_types_get($link, $smarty);
$reload_upload_types = false;	// Были ли произведены изменения.
/*
 * Добавим новый тип загружаемых файлов.
 */
if(isset($_POST['submited']) &&
	isset($_POST['new_extension']) &&
	isset($_POST['new_store_extension']) &&
	isset($_POST['new_upload_handler']) &&
	isset($_POST['new_thumbnail_image']) &&
	$_POST['new_extension'] !== '' &&
	$_POST['new_store_extension'] !== '' &&
	$_POST['new_upload_handler'] !== '')
{
	/*
	 * Проверим правильность всех входных параметров.
	 */
	if(($new_extension = check_format('extension',
				$_POST['new_extension'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['EXTENSION_NAME'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_store_extension = check_format('store_extension',
				$_POST['new_store_extension'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['EXTENSION_NAME'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_upload_handler_id = check_format('id',
				$_POST['new_upload_handler'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['UPLOAD_HANDLER_ID'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	$found = false;
	foreach($upload_handlers as $handler)
		if($handler['id'] == $new_upload_handler_id)
		{
			$found = true;
			break;
		}
	if(! $found)
	{
		mysqli_close($link);
		kotoba_error(sprintf(Errmsgs::$messages['UPLOAD_HANDLER_NOT_FOUND'],
				$new_upload_handler_id),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	if($_POST['new_thumbnail_image'] === '')
		$new_thumbnail_image_name = '';
	else
	{
		if(($new_thumbnail_image_name = check_format('thumbnail_image',
					$_POST['new_thumbnail_image'])) == false)
		{
			mysqli_close($link);
			kotoba_error(Errmsgs::$messages['THUMBNAIL_IMAGE_NAME'],
				$smarty,
				basename(__FILE__) . ' ' . __LINE__);
		}
	}
	/*
	 * Проверим, нет ли уже такого типа загружаемых файлов, если есть, то
	 * изменим существующий, если нет, то добавим новый.
	 */
	$found = false;
	foreach($upload_types as $upload_type)
	{
		if($upload_type['extension'] == $new_extension)
		{
			$found = true;
			db_upload_types_edit($upload_type['id'],
				$new_store_extension,
				$new_upload_handler_id,
				$new_thumbnail_image_name,
				$link,
				$smarty);
			$reload_upload_types = true;
			break;
		}
	}
	if(! $found)
	{
		db_upload_types_add($new_extension, $new_store_extension,
				$new_upload_handler_id,
				$new_thumbnail_image_name,
				$link,
				$smarty);
			$reload_upload_types = true;
	}
}
/*
 * Изменим обработчик загружаемых файлов.
 */
if(isset($_POST['submited']))
	foreach($upload_types as $upload_type)
	{
		/* Сохраняемый тип файла был изменен. */
		$param_name = 'store_extension_' . $upload_type['id'];
		$new_store_extension = $upload_type['store_extension'];
		if(isset($_POST[$param_name]) && ($_POST[$param_name] !=
				$upload_type['store_extension']))
		{
			if(($new_store_extension = check_format('store_extension',
						$_POST[$param_name])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['EXTENSION_NAME'],
					$smarty,
					basename(__FILE__) . ' ' . __LINE__);
			}
		}
		/* Обработчик загружаемых файлов был изменён. */
		$param_name = 'upload_handler_' . $upload_type['id'];
		$new_upload_handler_id = $upload_type['upload_handler'];
		if(isset($_POST[$param_name]) && ($_POST[$param_name] !=
				$upload_type['upload_handler']))
		{
			if(($new_upload_handler_id = check_format('id',
						$_POST[$param_name])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['UPLOAD_HANDLER_ID'],
					$smarty,
					basename(__FILE__) . ' ' . __LINE__);
			}
			$found = false;
			foreach($upload_handlers as $handler)
				if($handler['id'] == $new_upload_handler_id)
				{
					$found = true;
					break;
				}
			if(! $found)
			{
				mysqli_close($link);
				kotoba_error(sprintf(Errmsgs::$messages['UPLOAD_HANDLER_NOT_FOUND'],
						$new_upload_handler_id),
					$smarty,
					basename(__FILE__) . ' ' . __LINE__);
			}
		}
		/* Уменьшенная копия была изменена. */
		$param_name = 'thumbnail_image_' . $upload_type['id'];
		$new_thumbnail_image_name = $upload_type['thumbnail_image'];
		if(isset($_POST[$param_name]) && ($_POST[$param_name] !=
				$upload_type['thumbnail_image']))
		{
			if(($new_thumbnail_image_name = check_format('thumbnail_image',
						$_POST[$param_name])) == false)
			{
				mysqli_close($link);
				kotoba_error(Errmsgs::$messages['THUMBNAIL_IMAGE_NAME'],
					$smarty,
					basename(__FILE__) . ' ' . __LINE__);
			}
		}
		/* Было произведено хотя бы одно изменение. */
		if($new_store_extension != $upload_type['store_extension'] ||
			$new_upload_handler_id != $upload_type['upload_handler'] ||
			$new_thumbnail_image_name != $upload_type['thumbnail_image'])
		{
			db_upload_types_edit($upload_type['id'],
				$new_store_extension,
				$new_upload_handler_id,
				$new_thumbnail_image_name,
				$link,
				$smarty);
			$reload_upload_types = true;
		}
	}
/*
 * Удалим тип загружаемых файлов.
 */
if(isset($_POST['submited']))
	foreach($upload_types as $upload_type)
		if(isset($_POST['delete_' . $upload_type['id']]))
		{
			db_upload_types_delete($upload_type['id'], $link, $smarty);
			$reload_upload_types = true;
		}
/*
 * Если нужно, получение обновлённого списка типов загружаемых файлов,
 * вывод формы редактирования.
 */
if($reload_upload_types)
	$upload_types = db_upload_types_get($link, $smarty);
mysqli_close($link);
$smarty->assign('upload_handlers', $upload_handlers);
$smarty->assign('upload_types', $upload_types);
$smarty->display('edit_upload_types.tpl');
?>