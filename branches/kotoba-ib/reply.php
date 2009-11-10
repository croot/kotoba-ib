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
/*
 * Скрипт ответа в нить.
 */
// TODO переименовать все входные параметры с учётом того, что загружаться
// могут не только картинки.
require_once 'kwrapper.php';
require_once 'post_processing.php';
require_once 'thumb_processing.php';

kotoba_setup($link, $smarty);
try
{
	/*
	 * Проверка входных параметров, существования доски, нити и уровня
	 * доступа.
	 */
	$thread_id = threads_check_id($_POST['t']);
	$thread = db_threads_get_specifed_change($thread_id, $_SESSION['user'], $link);
	$board = db_boards_get_specifed_view($thread['board_name'], $_SESSION['user'], $link);
	if($thread['archived'])
	{
		throw new Exception(Errmsgs::$messages['THREAD_ARCHIVED']);
	}
	/*
	 * Проверка входных данных и подготовка к сохранению.
	 */
	$rempass = !isset($_SESSION['rempass']) || $_SESSION['rempass'] == null
		? '' : $_SESSION['rempass'];	// TODO Ограничения не длину пароля.
	$is_sage = 0;
	if(isset($_POST['sage']) && $_POST['sage'] == 'sage')
		$is_sage = 1;
	$types = db_upload_types_get($board['id'], $link);
	if($_FILES['message_img']['error'] == UPLOAD_ERR_NO_FILE
		&& (!isset($_POST['message_text']) || $_POST['message_text'] == ''))
	{
		throw new Exception(Errmsgs::$messages['EMPTY_MESSAGE']);
	}
	elseif($_FILES['message_img']['error'] == UPLOAD_ERR_NO_FILE)
	{
		$with_file = false;	// Сообщение без файла.
	}
	else
	{
		$with_file = true;	// Сообщение с файлом.
		$uploaded_file = $_FILES['message_img']['tmp_name'];
		$uploaded_name = $_FILES['message_img']['name'];
		$recived_ext = get_file_extension($uploaded_name);
		upload_types_valid_ext($recived_ext, $types);
	}
	if($with_file)
	{
		uploads_check_error($_FILES['message_img']['error']);
		if($_FILES['message_img']['size'] < Config::MIN_IMGSIZE)
		{
			throw new Exception(Errmsgs::$messages['UPLOAD_MIN_SIZE']);
		}
	}
	posts_check_data($_POST['message_name'], $_POST['message_theme'],
		$_POST['message_text']);
	$message_name = htmlspecialchars($_POST['message_name'], ENT_QUOTES);
	$message_theme = htmlspecialchars($_POST['message_theme'], ENT_QUOTES);
	$message_text = htmlspecialchars($_POST['message_text'], ENT_QUOTES);
	$message_name = stripslashes($message_name);
	$message_theme = stripslashes($message_theme);
	$message_text = stripslashes($message_text);
	posts_check_data($message_name, $message_theme, $message_text);
	posts_prepare_data($message_name, $message_theme, $message_text);
	$namecode = posts_tripcode($message_name);
	if(is_array($namecode))
	{
		$message_name = $namecode[0] . "!{$namecode[1]}";
	}
	else
	{
		$message_name = $namecode;
	}
	if($with_file)
	{
		$img_settings = thumb_get_img_settings($recived_ext, $uploaded_file,
			$types);
		// Расширение файла, с которым он был загружен.
		$original_ext = $img_settings['original_extension'];
		// Расширение файла, с которым он будет сохранён.
		$recived_ext = $img_settings['store_extension'];
		$filenames = posts_create_filenames($recived_ext, $original_ext);
		$saved_filename = $filenames[0];
		$saved_thumbname = $filenames[1];
		$raw_filename = $filenames[2];
		// Абсолютные и относительные пути к директориям, где хранятся файлы и
		// их уменьшенные копии.
		$IMG_SRC_DIR = sprintf("%s/%s/img", Config::ABS_PATH, $board['name']);
		$IMG_THU_DIR = sprintf("%s/%s/thumb", Config::ABS_PATH, $board['name']);
		$image_virtual_base = sprintf("%s/%s/img", Config::DIR_PATH, $board['name']);
		$thumbnail_virtual_base = sprintf("%s/%s/thumb", Config::DIR_PATH, $board['name']);
		// Полный абсолютный и относительный путь к загруженному файлу и
		// уменьшенной копии.
		$saved_image_path = sprintf("%s/%s", $IMG_SRC_DIR, $saved_filename);
		$image_virtual_path = sprintf("%s/%s", $image_virtual_base, $saved_filename);
		if($img_settings['is_image'])
		{
			$saved_thumbnail_path = sprintf("%s/%s", $IMG_THU_DIR, $saved_thumbname);
			$thumbnail_virtual_path = sprintf("%s/%s", $thumbnail_virtual_base, $saved_thumbname);
		}
		else
		{
			$thumbnail_virtual_path = $img_settings['thumbnail'];
		}
		posts_move_uploded_file($uploaded_file, $saved_image_path);
		if(($img_hash = hash_file('md5', $saved_image_path)) === false)
		{
			throw new Exception(Errmsgs::$messages['UPLOAD_HASH']);
		}
		$already_posted = false;
		switch($board['same_upload'])
		{
			case 'no':
				$same_uplodads = db_uploads_get_same($board['name'], $img_hash,
					$link);
				if($same_uplodads != null)
				{
					unlink($saved_image_path);
					display_same_uploads($same_uplodads, $board['name'], $smarty);
					exit;
				}
				break;
			case 'once':
				$same_uplodads = db_uploads_get_same($board['name'], $img_hash,
					$link);
				if($same_uplodads != null)
				{
					unlink($saved_image_path);
					$already_posted = true;
				}
				break;
			case 'yes':
			default:
				break;
		}
		if(!$already_posted)
		{
			if($img_settings['is_image'])
			{
				if($img_settings['x'] < Config::MIN_IMGWIDTH
					|| $img_settings['y'] < Config::MIN_IMGHEIGTH)
				{
					throw new Exception(Errmsgs::$messages['MIN_IMG_DIMENTIONS']);
				}
				$thumb_settings = create_thumbnail_new("$IMG_SRC_DIR/$saved_filename",
					"$IMG_THU_DIR/$saved_thumbname", $img_settings, $types, 200, 200,
					$img_settings['force_thumbnail']);
			}
			else	// Не картинка.
			{
				// TODO Не должно быть путей от корня документов.
				$saved_thumbname = $img_settings['thumbnail'];
			}
	/*
	 * Сохранение.
	 */
			// TODO Сохранение данных в базу должно быть атомарным.
			$upload_id = db_uploads_add($link, $board['id'], $img_hash,
				$img_settings['is_image'], "{$board['name']}/img/$saved_filename",
				$img_settings['x'], $img_settings['y'], $_FILES['message_img']['size'],
				"{$board['name']}/thumb/$saved_thumbname", $thumb_settings['x'],
				$thumb_settings['y']);
			if($upload_id < 0)
			{
				throw new Exception('dev: 1');
			}
		}
	}// with_file
	$post_id = db_posts_add_reply($link, $board['id'], $thread['id'],
		$_SESSION['user'], $rempass, $message_name,	ip2long($_SERVER['REMOTE_ADDR']),
		$message_theme, date("Y-m-d H:i:s"), $message_text, $is_sage);
	if($post_id < 0)
	{
		throw new Exception('dev: 2');
	}
	if($with_file)
	{
		if(!$already_posted)
		{
			db_posts_uploads_add($link, $post_id, $upload_id);
		}
		else
		{
			db_posts_uploads_add($link, $post_id, $same_uplodads[0]['id']);
		}
	}
	/*
	 * Перенаправление к доске или нити.
	 */
	if(isset($_POST['goto']) && $_POST['goto'] == 't')
	{
		header('Location: ' . Config::DIR_PATH . "/{$board['name']}/{$thread['original_post']}/");
		exit;
	}
	header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
	exit;
	var_dump($board);
	var_dump($thread);
	var_dump($types);
	var_dump($message_name);
	var_dump($message_theme);
	var_dump($message_text);
	var_dump($saved_image_path);
	var_dump($image_virtual_path);
	var_dump($saved_thumbnail_path);
	var_dump($thumbnail_virtual_path);
	var_dump($same_uplodads);
	var_dump($thumb_settings);
	var_dump($upload_id);
	var_dump($post_id);
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	if(isset($link) && $link instanceof MySQLi)
		mysqli_close($link);
	if(isset($saved_image_path))	// Удаление загруженного файла.
		@unlink($saved_image_path);
	if(isset($saved_thumbnail_path))	// Удаление уменьшенной копии.
		@unlink($saved_thumbnail_path);
	die($smarty->fetch('error.tpl'));
}

/*if(isset($_POST['b']))
{
    if(($board_name = check_format('board', $_POST['b'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BOARD_NAME'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
}
else
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['BOARD_NOT_SPECIFED'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
}
if(isset($_POST['t']))
{
    if(($thread_id = check_format('id', intval($_POST['t']))) === false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['THREAD_ID'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
}
else
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['THREAD_NOT_SPECIFED'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
}

if(isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] > 0) {
	$userid = sess_get_user_id();
}
else {
	$userid = 0;
}

$link = dbconn();

$error_message = "default error";
$BOARD = db_get_board($link, $BOARD_NAME);

if(count($BOARD) == 0) {
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

$BOARD_NUM = $BOARD['id'];

$types = db_get_board_types($link, $BOARD_NUM);

if($BOARD_NUM < 0) {
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

$flags = db_thread_flags($link, $BOARD_NUM, $THREAD_NUM);
if(count($flags) > 0 && ($flags['deleted'] > 0 || $flags['archived'] > 0)) {
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
	kotoba_error(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
}
// Этап 2. Обработка данных ОП поста.



if($_FILES['Message_img']['error'] == UPLOAD_ERR_NO_FILE &&
   	(!isset($_POST['Message_text']) || $_POST['Message_text'] == ''))
{ // no text no image
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_NO_FILE_AND_TEXT);
		
	kotoba_error(ERR_NO_FILE_AND_TEXT);
}
elseif($_FILES['Message_img']['error'] == UPLOAD_ERR_NO_FILE) { // no image
	$with_image = false;
}
else { // image and may be text
	$with_image = true;
	$uploaded_file = $_FILES['Message_img']['tmp_name'];
	$uploaded_name = $_FILES['Message_img']['name'];
	$recived_ext = post_get_uploaded_extension($uploaded_name);
	if(!post_check_supported_type($recived_ext, $types)) {
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_WRONG_FILETYPE);
			
		kotoba_error(ERR_WRONG_FILETYPE);
	}
}
if($with_image && !post_check_image_upload_error($_FILES['Message_img']['error'], false, "kotoba_stat",
	$error_message))
{ // upload of image failed
	kotoba_error($error_message);
}

$uploaded_file_size = $_FILES['Message_img']['size'];

if(!post_check_sizes($uploaded_file_size, true, $_POST['Message_text'],
	$_POST['Message_theme'], $_POST['Message_name'], "kotoba_stat", $error_message, $with_image)) {
	kotoba_error($error_message);
}
$Message_text = htmlspecialchars($_POST['Message_text'], ENT_QUOTES);
$Message_theme = htmlspecialchars($_POST['Message_theme'], ENT_QUOTES);
$Message_name = htmlspecialchars($_POST['Message_name'], ENT_QUOTES);
$Message_text = stripslashes($Message_text);
$Message_theme = stripslashes($Message_theme);
$Message_name = stripslashes($Message_name);

if(!post_check_sizes($uploaded_file_size, true, $Message_text,
	$Message_theme, $Message_name, "kotoba_stat", $error_message, $with_image)) {
	kotoba_error($error_message);
}

// mark fuction here
if(!post_mark($link, $Message_text, 
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) 
{
	kotoba_error($error_message);
}

// trip code
$namecode = post_tripcode($Message_name);
if(is_array($namecode)) {
	$Message_name = $namecode[0];
	$tripcode = $namecode[1];
}
else {
	$tripcode = null;
	$Message_name = $namecode;
}

if($with_image) {

require 'thumb_processing.php';
	$imageresult = array();
	if(!thumb_check_image_type($link, $recived_ext, $uploaded_file, $imageresult)) {
		// not supported file name
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_WRONG_FILETYPE);
		
		kotoba_error(ERR_WRONG_FILETYPE);
	}

	$original_ext = $imageresult['orig_extension'];
	$recived_ext = $imageresult['extension'];

	$filenames = post_create_filenames($recived_ext, $original_ext);
	$saved_filename = $filenames[0];
	$saved_thumbname = $filenames[1];
	$raw_filename = $filenames[2];

	$IMG_SRC_DIR = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$BOARD_NAME/img";
	$IMG_THU_DIR = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$BOARD_NAME/thumb";
	$image_virtual_base = sprintf("%s/%s/img", KOTOBA_DIR_PATH, $BOARD_NAME);
	$thumbnail_virtual_base = sprintf("%s/%s/thumb", KOTOBA_DIR_PATH, $BOARD_NAME);


	// full path of uploaded image and generated thumbnail
	$saved_image_path = sprintf("%s/%s", $IMG_SRC_DIR, $saved_filename);
	$image_virtual_path = sprintf("%s/%s", $image_virtual_base, $saved_filename);
	if($imageresult['image'] == 1) {
		$saved_thumbnail_path = sprintf("%s/%s", $IMG_THU_DIR, $saved_thumbname);
		$thumbnail_virtual_path = sprintf("%s/%s", $thumbnail_virtual_base, $saved_thumbname);
	}
	else {
		$thumbnail_virtual_path = $imageresult['thumbnail'];
	}

	if(!post_move_uploded_file($uploaded_file, $saved_image_path, "kotoba_stat", $error_message)) {
		kotoba_error($error_message);
	}
}

// calculate upload hash
if($with_file) {
	if(($img_hash = hash_file('md5', $saved_image_path)) === false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_FILE_HASH);

		kotoba_error(ERR_FILE_HASH);
	}
}

$already_posted = false;
if($with_file) {
	$same_uplodads = post_find_same_uploads($link, $BOARD_NUM, $img_hash);
	$same_uplodads_qty = count($same_uplodads);
	switch($BOARD['same_upload']) {
	case 'no':
		if($same_uplodads_qty > 0) {
			unlink($saved_image_path);
			post_show_uploads_links($link, $BOARD_NUM, $same_uplodads);
		}
		break;
	case 'once':
		if($same_uplodads_qty > 0) {
			unlink($saved_image_path);
			$already_posted = true;
		}
		break;
	case 'yes':
	default:
	break;
	}
}


//if(! KOTOBA_ALLOW_SAEMIMG)
//{
//	$error_message_array = array();
//	if(!post_get_same_image($BOARD_NUM, $BOARD_NAME, $img_hash, "kotoba_stat",
//			$error_message_array))
//	{
//		unlink($saved_image_path);
//		if($error_message_array['sameimage']) {
//			$link = sprintf("<a href=\"%s/%s/%d#%d\">тут</a>",
//				KOTOBA_DIR_PATH, $BOARD_NAME,
//				$error_message_array['thread'], $error_message_array['post']);
//			kotoba_error(sprintf("Ошибка. Картинка уже была запощена %s", $link));
//		}
//		else {
//			kotoba_error($error_message_array['error_message']);
//		}
//	}
//}

if(!$already_posted && $with_file && $imageresult['image'] == 1 &&
	$imageresult['x'] < KOTOBA_MIN_IMGWIDTH && $imageresult['y'] < KOTOBA_MIN_IMGHEIGTH)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_FILE_LOW_RESOLUTION);
	
	unlink($saved_image_path);
	kotoba_error(ERR_FILE_LOW_RESOLUTION);
}
if(!$already_posted && $with_file && $imageresult['image'] == 1) {
	$thumbnailresult = array();
	$thumb_res = create_thumbnail($link, "$IMG_SRC_DIR/$saved_filename", "$IMG_THU_DIR/$saved_thumbname",
		$original_ext, $imageresult['x'], $imageresult['y'], 200, 200,
		$imageresult['force_thumbnail'], $thumbnailresult);


	if($thumb_res != KOTOBA_THUMB_SUCCESS)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_THUMB_CREATION);

		unlink($saved_filename);

		switch($thumb_res)
		{
			case KOTOBA_THUMB_UNSUPPORTED:	// unsupported format
				$message = "usupported file format";
				break;
			case KOTOBA_THUMB_NOLIBRARY:	// no suitable library
				$message = "no suitable library for image processing";
				break;
			case KOTOBA_THUMB_TOOBIG	:	// file too big
				$message = "image file too big";
				break;
			case KOTOBA_THUMB_UNKNOWN:	// unknown error
				$message = "unknown error";
				break;
			default:
				$message = "...";
				break;
		}

		kotoba_error(sprintf("Ошибка. Не удалось создать уменьшенную копию изображения: %s",
			$message));
	}
}
elseif(!$already_posted && $with_file) {
	$saved_thumbname = $imageresult['thumbnail'];
}

if(!$already_posted && $with_file) {
	$image = upload($link, $BOARD_NUM, $saved_filename, $uploaded_file_size, $img_hash, 
		$imageresult['image'], $image_virtual_path, $imageresult['x'], $imageresult['y'],
		$thumbnail_virtual_path, $thumbnailresult['x'], $thumbnailresult['y']);

	if($image < 0) {
		kotoba_error("Cannot store information about upload");
	}
}

// password settings
$OPPOST_PASS = '';
if(isset($_POST['Message_pass']) && $_POST['Message_pass'] != '')
{ // password is set and not empty
	$OPPOST_PASS = $_POST['Message_pass'];
	if(($OPPOST_PASS = CheckFormat('pass', $_POST['Message_pass'])) === false)
	{ // password have wrong format
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_PASS_BAD_FORMAT);
		// remove uploaded file/////
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(ERR_PASS_BAD_FORMAT);
	}
	
	// save password in cookie
	if(!isset($_COOKIE['rempass']) || $_COOKIE['rempass'] != $OPPOST_PASS) 
		setcookie("rempass", $OPPOST_PASS);
}

$sage = 0;
if(array_key_exists('Sage', $_POST) && $_POST['Sage'] == 'sage') {
	$sage = 1;
}

//echo "$BOARD_NUM, $THREAD_NUM";
$postid = post($link, $BOARD_NUM, $THREAD_NUM, $Message_name, $tripcode, '', $Message_theme, $OPPOST_PASS, $userid, 
	session_id(), ip2long($_SERVER['REMOTE_ADDR']), $Message_text, date("Y-m-d H:i:s"), $sage);

if($postid < 0) {
	kotoba_error("Cannot store information about post");
}
if(!$already_posted && $with_file) {
	if(link_post_upload($link, $BOARD_NUM, $image, $postid)) {
		kotoba_error("Cannot link information about post and upload");
	}
}
elseif($already_posted) {
	if(link_post_upload($link, $BOARD_NUM, $same_uplodads[0]['id'], $postid)) {
		kotoba_error("Cannot link information about post and upload");
	}
}

if(isset($_POST['goto']) && $_POST['goto'] == 't')
{
	header('Location: ' . KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/");
	exit;
}

header('Location: ' . KOTOBA_DIR_PATH . "/$BOARD_NAME/");
exit;*/
?>