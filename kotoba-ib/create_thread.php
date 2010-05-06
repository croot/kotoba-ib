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
// Скрипт создания нитей.
require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/upload_handlers.php';
require_once Config::ABS_PATH . '/lib/mark.php';
include Config::ABS_PATH . '/securimage/securimage.php';
try
{
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
    bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
    if(!is_admin())
    {
        $securimage = new Securimage();
        if(!isset($_POST['captcha_code'])
            || $securimage->check($_POST['captcha_code']) == false)
        {
            throw new CommonException(CommonException::$messages['CAPTCHA']);
        }
    }
    $board = boards_get_changeable_by_id(boards_check_id($_POST['board']),
        $_SESSION['user']);
    $goto = null;
    $should_update_goto = false;
    if(isset($_POST['goto']))
    {
        $goto = users_check_goto($_POST['goto']);
        // $_SESSION['goto'] = $_POST['goto'];
        if(!isset($_SESSION['goto']) || $_SESSION['goto'] != $goto)
        {
            $_SESSION['goto'] = $goto;
            $should_update_goto = true;
        }
    }
    else
		throw new FormatException(FormatException::$messages['GOTO']);
    $password = null;
    $should_update_password = false;
    if(isset($_POST['password']) && $_POST['password'] != '')
    {
        $password = posts_check_password($_POST['password']);
        if(!isset($_SESSION['password']) || $_SESSION['password'] != $password)
        {
            $_SESSION['password'] = $password;
            $should_update_password = true;
        }
    }
    $attachment_type = null;  // Тип ссылки на файл.
    if($board['with_attachments'])
    {
        if($_FILES['file']['error'] != UPLOAD_ERR_NO_FILE)
        {
            $attachment_type = Config::ATTACHMENT_TYPE_FILE;
            check_upload_error($_FILES['file']['error']);
            $uploaded_file_size = $_FILES['file']['size'];
            $uploaded_file_path = $_FILES['file']['tmp_name'];
            $uploaded_file_name = $_FILES['file']['name'];
            $uploaded_file_ext = get_extension($uploaded_file_name);
            $upload_types = upload_types_get_by_board($board['id']);
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
        }
        elseif(Config::ENABLE_MACRO && isset($_POST['macrochan_tag'])
            && $_POST['macrochan_tag'] != '')
        {
            $attachment_type = Config::ATTACHMENT_TYPE_LINK;
        }
        elseif(Config::ENABLE_YOUTUBE && isset($_POST['youtube_video_code'])
            && $_POST['youtube_video_code'] != '')
        {
            $youtube_video_code = check_youtube_video_code($_POST['youtube_video_code']);
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
    $tripcode = null;
    if($board['force_anonymous'])
        $name = '';
    else
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
    if($board['with_attachments'])
    {
        if($attachment_type == Config::ATTACHMENT_TYPE_FILE
            || $attachment_type == Config::ATTACHMENT_TYPE_IMAGE)
        {
            $file_hash = calculate_file_hash($uploaded_file_path);
            $file_already_posted = false;
            $same_uploads = null;
            switch($board['same_upload'])
            {
                case 'no':
                    $same_uploads = uploads_get_same($board['id'], $file_hash,
                        $_SESSION['user']);
                    if(count($same_uploads) > 0)
                    {
                        show_same_uploads($smarty, $board['name'],
                            $same_uploads);
                        DataExchange::releaseResources();
                        exit;
                    }
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
                $file_names = create_filenames($upload_type['store_extension']);
                if($attachment_type == Config::ATTACHMENT_TYPE_IMAGE)
                {
                    $img_dimensions = image_get_dimensions($upload_type,
                        $uploaded_file_path);
                    if($img_dimensions['x'] < Config::MIN_IMGWIDTH
                        && $img_dimensions['y'] < Config::MIN_IMGHEIGHT)
                    {
                        throw new LimitException(LimitException::$messages['MIN_IMG_DIMENTIONS']);
                    }
                    $abs_img_path = Config::ABS_PATH
                        . "/{$board['name']}/img/{$file_names[0]}";
                    $abs_thumb_path = Config::ABS_PATH
                        . "/{$board['name']}/thumb/{$file_names[1]}";
                    $virt_thumb_path = Config::DIR_PATH
                        . "/{$board['name']}/thumb/{$file_names[1]}";
                    move_uploded_file($uploaded_file_path, $abs_img_path);
                    $force = $upload_type['upload_handler_name'] === 'thumb_internal_png'
                        ? true : false;	// TODO Unhardcode handler name.
                    $thumb_dimensions = create_thumbnail($abs_img_path,
                        $abs_thumb_path, $img_dimensions, $upload_type,
                        Config::THUMBNAIL_WIDTH, Config::THUMBNAIL_HEIGHT,
                        $force);
                }
                else
                {
                    $img_dimensions['x'] = null;
                    $img_dimensions['y'] = null;
                    $abs_file_path = Config::ABS_PATH
                        . "/{$board['name']}/other/{$file_names[0]}";
                    $virt_thumb_path = $upload_type['thumbnail_image'];
                    move_uploded_file($uploaded_file_path, $abs_file_path);
                    $file_names[1] = $virt_thumb_path;
                    $thumb_dimensions['x'] = Config::THUMBNAIL_WIDTH;
                    $thumb_dimensions['y'] = Config::THUMBNAIL_HEIGHT;
                }

            }// Not already posted.
        }// Virtual link type.
        elseif($attachment_type == Config::ATTACHMENT_TYPE_LINK)
        {
            $file_hash = null;
            $file_names[0] = 'http://12ch.ru/macro/index.php/image/3478.jpg';
            $img_dimensions['x'] = '640';
            $img_dimensions['y'] = '480';
            $uploaded_file_size = 63290;
            $file_names[1] = 'http://12ch.ru/macro/index.php/thumb/3478.jpg';
            $thumb_dimensions['x'] = '192';
            $thumb_dimensions['y'] = '144';
        }
        elseif($attachment_type == Config::ATTACHMENT_TYPE_VIDEO)
        {
            $file_hash = null;
            $file_names[0] = $youtube_video_code;
            $img_dimensions['x'] = null;
            $img_dimensions['y'] = null;
            $uploaded_file_size = 0;
            $file_names[1] = null;
            $thumb_dimensions['x'] = null;
            $thumb_dimensions['y'] = null;
        }
        else
        {
            throw new UploadException(UploadException::$messages['UNKNOWN']);
        }
        if(!$file_already_posted)
        {
            $upload_id = uploads_add($file_hash, $upload_type['is_image'],
                $attachment_type, $file_names[0], $img_dimensions['x'],
                $img_dimensions['y'], $uploaded_file_size, $file_names[1],
                $thumb_dimensions['x'], $thumb_dimensions['y']);
        }
        else
            // Первый попавшийся из одинаковых файлов.
            $upload_id = $same_uploads[0]['id'];
	}// With files.
	// Create empty thread.
	$thread = threads_add($board['id'], null, null, 0, null);
	date_default_timezone_set(Config::DEFAULT_TIMEZONE);
	$post = posts_add($board['id'], $thread['id'], $_SESSION['user'], $password,
		$name, $tripcode, ip2long($_SERVER['REMOTE_ADDR']), $subject,
		date(Config::DATETIME_FORMAT), $text, null);
	// Закрепляем сообщение как оригинальное сообщение созданной пустой нити.
	threads_edit_originalpost($thread['id'], $post['number']);
	if($board['with_files'])
	{
		posts_uploads_add($post['id'], $upload_id);
	}
	if($_SESSION['user'] != Config::GUEST_ID && $should_update_password)
	{
		users_set_password($_SESSION['user'], $password);
	}
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
			. "/{$board['name']}/{$post['number']}/");
	else
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