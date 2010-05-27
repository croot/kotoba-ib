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
require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/upload_handlers.php';
require_once Config::ABS_PATH . '/lib/mark.php';
include Config::ABS_PATH . '/securimage/securimage.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.

    $thread = threads_get_changeable_by_id(threads_check_id($_POST['t']),
        $_SESSION['user']);
    if ($thread['archived']) {
        throw new CommonException(CommonException::$messages['THREAD_ARCHIVED']);
    }

    $board = boards_get_by_id($thread['board']);
    if (!is_admin()
            && (($board['enable_captcha'] === null && Config::ENABLE_CAPTCHA)
            || $board['enable_captcha'])) {
        $securimage = new Securimage();
        if (!isset($_POST['captcha_code'])
                || $securimage->check($_POST['captcha_code']) == false) {
            throw new CommonException(CommonException::$messages['CAPTCHA']);
        }
    }
	$password = null;
	$update_password = false;
	if(isset($_POST['password']) && $_POST['password'] != '')
	{
		$password = posts_check_password($_POST['password']);
		if(!isset($_SESSION['password']) || $_SESSION['password'] != $password)
		{
			$_SESSION['password'] = $password;
			$update_password = true;
		}
	}
	$with_attachment = false;	// Сообщение с вложением.
	$attachment_type = null;	// Тип вложения.
	if($thread['with_attachments']
		|| ($thread['with_attachments'] === null && $board['with_attachments']))	// Можно отвечать с вложением.
	{
		if($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE	// Ни файл ни изображение не были загржены.
			&& ((($board['enable_macro'] === null && Config::ENABLE_MACRO) || $board['enable_macro'])
				&& (!isset($_POST['macrochan_tag']) || $_POST['macrochan_tag'] == ''))	// Включена интеграция с макрочаном, но тег не был выбран.
			&& ((($board['enable_youtube'] === null && Config::ENABLE_YOUTUBE) || $board['enable_youtube'])
				&& (!isset($_POST['youtube_video_code']) || $_POST['youtube_video_code'] == ''))	// Включено вложение видео с ютуба, но код видео не был введён.
			&& (!isset($_POST['text']) || $_POST['text'] == ''))	// Текст сообщения отсутствует.
		{
			throw new NodataException(NodataException::$messages['EMPTY_MESSAGE']);
		}
		if($_FILES['file']['error'] != UPLOAD_ERR_NO_FILE)
		{
			$with_attachment = true;
			check_upload_error($_FILES['file']['error']);
			$uploaded_file_size = $_FILES['file']['size'];
			$uploaded_file_path = $_FILES['file']['tmp_name'];
			$uploaded_file_name = $_FILES['file']['name'];
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
			{
				$attachment_type = Config::ATTACHMENT_TYPE_IMAGE;
				uploads_check_image_size($uploaded_file_size);
			}
			else
				$attachment_type = Config::ATTACHMENT_TYPE_FILE;
		}
		elseif((($board['enable_macro'] === null && Config::ENABLE_MACRO) || $board['enable_macro'])
			&& isset($_POST['macrochan_tag']) && $_POST['macrochan_tag'] != '')
		{
			$with_attachment = true;
			$attachment_type = Config::ATTACHMENT_TYPE_LINK;
		}
		elseif((($board['enable_youtube'] === null && Config::ENABLE_YOUTUBE) || $board['enable_youtube'])
			&& isset($_POST['youtube_video_code']) && $_POST['youtube_video_code'] != '')
		{
			$youtube_video_code = check_youtube_video_code($_POST['youtube_video_code']);
			$with_attachment = true;
			$attachment_type = Config::ATTACHMENT_TYPE_VIDEO;
		}
		else
		{
			throw new UploadException(UploadException::$messages['UNKNOWN']);
		}
	}
	posts_check_text_size($_POST['text']);
	posts_check_subject_size($_POST['subject']);
	$name = null;
	if(!$board['force_anonymous'])
	{
		posts_check_name_size($_POST['name']);
		$name = htmlentities($_POST['name'], ENT_QUOTES, Config::MB_ENCODING);
		$name = str_replace('\\', '\\\\', $name);
		$name = str_replace("\n", '', $name);
		$name = str_replace("\r", '', $name);
		posts_check_name_size($name);
		$name_tripcode = calculate_tripcode($name);
		$name = $name_tripcode[0];
		$tripcode = $name_tripcode[1];
	}
	else
		// Подписывать сообщения запрещено на этой доске.
		$name = '';
	$text = htmlentities($_POST['text'], ENT_QUOTES, Config::MB_ENCODING);
	$text = str_replace('\\', '\\\\', $text);
	$subject = htmlentities($_POST['subject'], ENT_QUOTES, Config::MB_ENCODING);
	$subject = str_replace('\\', '\\\\', $subject);
	posts_check_text_size($text);
	posts_check_subject_size($subject);
	posts_check_text($text);
	posts_prepare_text($text, $board);
	posts_check_text_size($text);
	$subject = str_replace("\n", '', $subject);
	$subject = str_replace("\r", '', $subject);
	$sage = null;	// Наследует от нити.
	if(isset($_POST['sage']) && $_POST['sage'] == 'sage')
		$sage = '1';
	if(isset($_POST['goto'])
		&& ($_POST['goto'] == 't' || $_POST['goto'] == 'b')
		&& $_POST['goto'] != $_SESSION['goto'])
			$_SESSION['goto'] = $_POST['goto'];
// 2. Подготовка и сохранение файла.
	if($with_attachment)
	{
		switch($attachment_type)
		{
			case Config::ATTACHMENT_TYPE_FILE:
				$file_hash = calculate_file_hash($uploaded_file_path);
				$file_already_posted = false;
				$same_files = null;
				switch($board['same_upload'])
				{
					case 'no':
						$same_files = files_get_same($board['id'], $file_hash,
							$_SESSION['user']);
						if(count($same_files) > 0)
						{
							show_same_files($smarty, $board['name'],
								$same_files);
							DataExchange::releaseResources();
							exit;
						}
						break;
					case 'once':
						$same_files = files_get_same($board['id'], $file_hash,
							$_SESSION['user']);
						if(count($same_files) > 0)
							$file_already_posted = true;
						break;
					case 'yes':
					default:
						break;
				}
				if(!$file_already_posted)
				{
					$file_names = create_filenames($upload_type['store_extension']);
					$abs_file_path = Config::ABS_PATH
						. "/{$board['name']}/other/{$file_names[0]}";
					move_uploded_file($uploaded_file_path, $abs_file_path);
					$file_id = files_add($file_hash, $file_names[0],
						$uploaded_file_size, $upload_type['thumbnail_image'],
						Config::THUMBNAIL_WIDTH, Config::THUMBNAIL_HEIGHT, 0);
				}
				else
					$file_id = $same_files[0]['id'];	// Первый попавшийся из одинаковых файлов.
				break;
			case Config::ATTACHMENT_TYPE_IMAGE:
				$image_hash = calculate_file_hash($uploaded_file_path);
				$image_already_posted = false;
				$same_images = null;
				switch($board['same_upload'])
				{
					case 'no':
						$same_images = images_get_same($board['id'],
							$image_hash, $_SESSION['user']);
						if(count($same_images) > 0)
						{
							show_same_images($smarty, $board['name'],
								$same_images);
							DataExchange::releaseResources();
							exit;
						}
						break;
					case 'once':
						$same_images = images_get_same($board['id'],
							$image_hash, $_SESSION['user']);
						if(count($same_images) > 0)
							$image_already_posted = true;
						break;
					case 'yes':
					default:
						break;
				}
				if(!$image_already_posted)
				{
					$img_dimensions = image_get_dimensions($upload_type,
						$uploaded_file_path);
					if($img_dimensions['x'] < Config::MIN_IMGWIDTH
						&& $img_dimensions['y'] < Config::MIN_IMGHEIGHT)
					{
						throw new LimitException(LimitException::$messages['MIN_IMG_DIMENTIONS']);
					}
					$file_names = create_filenames($upload_type['store_extension']);
					$abs_img_path = Config::ABS_PATH
						. "/{$board['name']}/img/{$file_names[0]}";
					$abs_thumb_path = Config::ABS_PATH
						. "/{$board['name']}/thumb/{$file_names[1]}";
					move_uploded_file($uploaded_file_path, $abs_img_path);
					$thumb_dimensions = create_thumbnail($abs_img_path,
						$abs_thumb_path, $img_dimensions, $upload_type,
						Config::THUMBNAIL_WIDTH, Config::THUMBNAIL_HEIGHT,
						$upload_type['upload_handler_name'] === 'thumb_internal_png');	// TODO Unhardcode handler name.
					$image_id = images_add($file_hash, $file_names[0],
						$img_dimensions['x'], $img_dimensions['y'],
						$uploaded_file_size, $file_names[1],
						$thumb_dimensions['x'], $thumb_dimensions['y'], 0);
				}
				else
					$image_id = $same_images[0]['id'];	// Первая попавшаяся из одинаковых картинок.
				break;
			case Config::ATTACHMENT_TYPE_LINK:
				$link_id = links_add('http://12ch.ru/macro/index.php/image/3478.jpg',
					'640', '480', 63290,
					'http://12ch.ru/macro/index.php/thumb/3478.jpg',
					'192', '144', 0);
				break;
			case Config::ATTACHMENT_TYPE_VIDEO:
				$video_id = videos_add($youtube_video_code, null, null, 0);
				break;
			default:
				throw new CommonException('Not supported.');
				break;
		}
	}
	date_default_timezone_set(Config::DEFAULT_TIMEZONE);
	$post = posts_add($board['id'], $thread['id'], $_SESSION['user'],
		$password, $name, $tripcode, ip2long($_SERVER['REMOTE_ADDR']),
		$subject, date(Config::DATETIME_FORMAT), $text, $sage);
	if(($board['with_files'] || $thread['with_files']) && $with_attachment)
		switch($attachment_type)
		{
			case Config::ATTACHMENT_TYPE_FILE:
				posts_files_add($post['id'], $file_id);
				break;
			case Config::ATTACHMENT_TYPE_IMAGE:
				posts_images_add($post['id'], $image_id);
				break;
			case Config::ATTACHMENT_TYPE_LINK:
				posts_links_add($post['id'], $link_id);
				break;
			case Config::ATTACHMENT_TYPE_VIDEO:
				posts_videos_add($post['id'], $video_id);
				break;
			default:
				throw new CommonException('Not supported.');
				break;
		}
	if($_SESSION['user'] != Config::GUEST_ID && $update_password)
		users_set_password($_SESSION['user'], $password);
// 4. Запуск обработчика автоматического удаления нитей.
	foreach(popdown_handlers_get_all() as $popdown_handler)
		if($board['popdown_handler'] == $popdown_handler['id'])
		{
			$popdown_handler['name']($board['id']);
			break;
		}
	DataExchange::releaseResources();
// 5. Перенаправление.
	if($_SESSION['goto'] == 't')
		header('Location: ' . Config::DIR_PATH
			. "/{$board['name']}/{$thread['original_post']}/");
	else
		header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	if(isset($abs_file_path))	// Удаление загруженного файла.
		@unlink($abs_file_path);
	if(isset($abs_img_path))	// Удаление загруженного изображения.
		@unlink($abs_img_path);
	if(isset($abs_thumb_path))	// Удаление уменьшенной копии.
		@unlink($abs_thumb_path);
	die($smarty->fetch('error.tpl'));
}
?>