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

// Заметки:
//
// Для каждого скрипта, при включенном сборе статистики, создаётся файл имя_скрипта.stat в котором будет хранится статистика.
// Такой файл называется Лог статистики.
//
// Как, куда и когда выводить статистику решает скрипт. Что выводить - решает events.php. Если вы ходите изменить
// выводимый текст в лог статистики, используйте константы в events.php.

error_reporting(E_ALL);
require 'config.php';
require 'common.php';
kotoba_setup();
require_once 'post_processing.php';
require 'error_processing.php';
require 'events.php';

if(KOTOBA_ENABLE_STAT)
{ // open stat file for appending
	if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/createthread.stat',
		'a')) === false)
	{ // opening failed
		kotoba_error("Ошибка. Не удалось открыть или создать файл статистики.");
	}
}

// Этап 1. Проверка имени доски, на которой создаётся тред.

if(!isset($_POST['b']))
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_BOARD_NOT_SPECIFED);
		
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

if(($BOARD_NAME = CheckFormat('board', $_POST['b'])) === false)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_BOARD_BAD_FORMAT);
		
	kotoba_error(ERR_BOARD_BAD_FORMAT);
}
if(!isset($_POST['t']))
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_THREAD_NOT_SPECIFED);

	kotoba_error(ERR_THREAD_NOT_SPECIFED);
}

if(($THREAD_NUM = CheckFormat('thread', $_POST['t'])) === false)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_THREAD_BAD_FORMAT);
		
	kotoba_error(ERR_THREAD_BAD_FORMAT);
}
require 'database_connect.php';
require 'database_common.php';

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
if($with_image) {
	if(($img_hash = hash_file('md5', $saved_image_path)) === false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_FILE_HASH);

		kotoba_error(ERR_FILE_HASH);
	}
}

$already_posted = false;
$same_uplodads = post_find_same_uploads($link, $BOARD_NUM, $img_hash);
$same_uplodads_qty = count($same_uplodads);
switch($BOARD['same_upload']) {
case 'no':
	if($same_uplodads_qty > 0) {
		@unlink($saved_image_path);
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

/*
if(! KOTOBA_ALLOW_SAEMIMG)
{
	$error_message_array = array();
	if(!post_get_same_image($BOARD_NUM, $BOARD_NAME, $img_hash, "kotoba_stat",
			$error_message_array))
	{
		unlink($saved_image_path);
		if($error_message_array['sameimage']) {
			$link = sprintf("<a href=\"%s/%s/%d#%d\">тут</a>", 
				KOTOBA_DIR_PATH, $BOARD_NAME,
				$error_message_array['thread'], $error_message_array['post']);
			kotoba_error(sprintf("Ошибка. Картинка уже была запощена %s", $link));
		}
		else {
			kotoba_error($error_message_array['error_message']);
		}
	}
}
 */
if(!$already_posted && $with_image && $imageresult['image'] == 1 && 
	$imageresult['x'] < KOTOBA_MIN_IMGWIDTH && $imageresult['y'] < KOTOBA_MIN_IMGHEIGTH)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_FILE_LOW_RESOLUTION);
	
	unlink($saved_image_path);
	kotoba_error(ERR_FILE_LOW_RESOLUTION);
}
if(!$already_posted && $with_image && $imageresult['image'] == 1) {
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
else {
	$saved_thumbname = $imageresult['thumbnail'];
}

if(!$already_posted && $with_image) {
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
$postid = post($link, $BOARD_NUM, $THREAD_NUM, $Message_name, '', $Message_theme, $OPPOST_PASS, session_id(),
	ip2long($_SERVER['REMOTE_ADDR']), $Message_text, date("Y-m-d H:i:s"), $sage);

if($postid < 0) {
	kotoba_error("Cannot store information about post");
}
if(!$already_posted && $with_image) {
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
exit;
?>
<?php
/*
 * Выводит сообщение $errmsg в файл статистики $stat_file.
 */
function kotoba_stat($errmsg, $close_file = true)
{
	global $stat_file;
	fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");

	if($close_file)
		fclose($stat_file);
}
?>
