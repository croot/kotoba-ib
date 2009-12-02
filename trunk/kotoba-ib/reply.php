<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.		   *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/
// Скрипт ответа в нить.
require 'config.php';
require 'modules/errors.php';
require 'modules/lang/' . Config::LANGUAGE . '/errors.php';
require 'modules/db.php';
require 'modules/cache.php';
require 'modules/common.php';
require 'modules/popdown_handlers.php';
require 'modules/upload_handlers.php';
try
{
// 0. Инициализация.
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
// 1. Проверка входных параметров.
	$thread = threads_get_specifed_change(threads_check_id($_POST['t']),
		$_SESSION['user']);
	$board = boards_get_specifed($thread['board']);
	if($thread['archived'])
		throw new CommonException(CommonException::$messages['THREAD_ARCHIVED']);
	$message_pass = null;
	if(isset($_POST['message_pass']) && $_POST['message_pass'] != '')
	{
		$message_pass = posts_check_password($_POST['message_pass']);
		if(!isset($_SESSION['rempass']) || $_SESSION['rempass'] != $message_pass)
			$_SESSION['rempass'] = $message_pass;
	}
	$with_file = false;	// Сообщение с файлом.
	if($board['with_files'])
	{
		if($_FILES['message_img']['error'] == UPLOAD_ERR_NO_FILE
			&& !isset($_POST['message_text']) || $_POST['message_text'] == '')
		{
			// Файл не был загружен и текст сообщения пуст.
			throw new NodataException(NodataException::$messages['EMPTY_MESSAGE']);
		}
		if($_FILES['message_img']['error'] != UPLOAD_ERR_NO_FILE)
		{
			// По крайней мере была попытка загрузить файл.
			$with_file = true;
			try
			{
				check_upload_error($_FILES['message_img']['error']);
			}
			catch(UploadException $e)
			{
				if($e->getReason() != UploadException::$messages['UPLOAD_ERR_NO_FILE'])
					throw $e;
			}
			$uploaded_file_size = $_FILES['message_img']['size'];
			$uploaded_file_path = $_FILES['message_img']['tmp_name'];
			$uploaded_file_name = $_FILES['message_img']['name'];
			$uploaded_file_ext = get_extension($uploaded_file_name);
			$upload_types = upload_types_get_board($board['id']);
			$found = false;
			$upload_type = null;
			foreach($upload_types as $ut)
				if($ut['extension'] == $uploaded_file_ext)
				{
					$found = true;
					$upload_type = $ut;
					break;
				}
			if(!$found)
				throw new UploadException(UploadException::$messages['UPLOAD_FILETYPE_NOT_SUPPORTED']);
			if($upload_type['is_image'])
				uploads_check_image_size($uploaded_file_size);
		}
	}
	posts_check_text_size($_POST['message_text']);
	posts_check_subject_size($_POST['message_theme']);
	$message_name = null;
	if(!$board['force_anonymous'])
	{
		posts_check_name_size($_POST['message_name']);
		$message_name = htmlspecialchars($_POST['message_name'], ENT_QUOTES);
		$message_name = stripslashes($message_name);
		posts_check_name_size($message_name);
		$message_name = calculate_tripcode($message_name);
		posts_check_name_size($message_name);
	}
	else
		// Подписывать сообщения запрещено на этой доске.
		$message_name = '';
	$message_text = htmlspecialchars($_POST['message_text'], ENT_QUOTES);
	$message_subject = htmlspecialchars($_POST['message_theme'], ENT_QUOTES);
	$message_text = stripslashes($message_text);
	$message_subject = stripslashes($message_subject);
	posts_check_text_size($message_text);
	posts_check_subject_size($message_subject);
	$is_sage = '0';
	if(isset($_POST['sage']) && $_POST['sage'] == 'sage')
		$is_sage = '1';
	if($board['with_files'] && $with_file)
	{
// 2. Подготовка файла к сохранению.
		$file_hash = calculate_file_hash($uploaded_file_path);
		$file_already_posted = false;
		$same_uploads = null;
		switch($board['same_upload'])
		{
			case 'no':
				$same_uploads = uploads_get_same($board['id'], $file_hash,
					$_SESSION['user']);
				if(count($same_uploads) > 0)
					// Terminate script!
					show_same_uploads($smarty, $board['name'], $same_uploads);
				break;
			case 'once':
				$same_uploads = uploads_get_same($board['id'], $file_hash,
					$_SESSION['user']);
				if(count($same_uploads) > 0)
					$file_already_posted = true;
				break;
			case 'yes':
			default:
				break;
		}
		if(!$file_already_posted)
		{
			if($upload_type['is_image'])
			{
				$img_dimensions = image_get_dimensions($upload_type,
					$uploaded_file_path);
				if($img_dimensions['x'] < Config::MIN_IMGWIDTH
					&& $img_dimensions['y'] < Config::MIN_IMGHEIGTH)
					throw new LimitException(LimitException::$messages['MIN_IMG_DIMENTIONS']);
			}
			$file_names = create_filenames($upload_type['store_extension']);
			// Directories of uploaded image and generated thumbnail.
			$abs_img_dir = Config::ABS_PATH . "/{$board['name']}/img";
			$virt_img_dir = Config::DIR_PATH . "/{$board['name']}/img";
			$abs_thumb_dir = Config::ABS_PATH . "/{$board['name']}/thumb";
			$virt_thumb_dir = Config::DIR_PATH . "/{$board['name']}/thumb";
			// Full path of uploaded image and generated thumbnail.
			$abs_img_path = "$abs_img_dir/{$file_names[0]}";
			$virt_img_path = "$virt_img_dir/{$file_names[0]}";
			if($upload_type['is_image'])
			{
				$abs_thumb_path = "$abs_thumb_dir/{$file_names[1]}";
				$virt_thumb_path = "$virt_thumb_dir/{$file_names[1]}";
			}
			else
				// TODO Actually it must be only a name not path.
				$virt_thumb_path = $upload_type['thumbnail_image'];
	// 3. Сохранение данных.
			move_uploded_file($uploaded_file_path, $abs_img_path);
			if($upload_type['is_image'])
			{
				$force = $upload_type['upload_handler_name'] === 'thumb_internal_png'
					? true : false;	// TODO Unhardcode handler name.
				$thumb_dimensions = create_thumbnail($abs_img_path,
					$abs_thumb_path, $img_dimensions, $upload_type, 200, 200,
					$force);
				$upload_id = uploads_add($board['id'], $file_hash,
					$upload_type['is_image'], $file_names[0],
					$img_dimensions['x'], $img_dimensions['y'],
					$uploaded_file_size, $file_names[1], $thumb_dimensions['x'],
					$thumb_dimensions['y']);
			}
			else
				// 200 x 200 is default thumb dimensions for non images.
				$upload_id = uploads_add($board['id'], $file_hash,
					$upload_type['is_image'], $file_names[0], null, null,
					$uploaded_file_size, $virt_thumb_path, 200, 200);

		}
		else
			// Первый попавшийся из одинаковых файлов.
			$upload_id = $same_uploads[0]['id'];
	}
	date_default_timezone_set(Config::DEFAULT_TIMEZONE);
	$post = posts_add($board['id'], $thread['id'], $_SESSION['user'],
		$message_pass, $message_name, ip2long($_SERVER['REMOTE_ADDR']),
		$message_subject, date("Y-m-d H:i:s"), $message_text, $is_sage);
	if($board['with_files'] && $with_file)
		posts_uploads_add($post['id'], $upload_id);
	DataExchange::releaseResources();
// 4. Перенаправление.
	if(isset($_POST['goto']) && $_POST['goto'] == 't')
	{
		header('Location: ' . Config::DIR_PATH . "/{$board['name']}/{$thread['original_post']}/");
		exit;
	}
	header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	if(isset($abs_img_path))	// Удаление загруженного файла.
		@unlink($abs_img_path);
	if(isset($abs_thumb_path))	// Удаление уменьшенной копии.
		@unlink($abs_thumb_path);
	die($smarty->fetch('error.tpl'));
}
?>