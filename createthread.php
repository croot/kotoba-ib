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
require 'kwrapper.php';
@kotoba_setup($link, $smarty);

require_once('post_processing.php');

// echo "<pre>"; var_dump($_POST); echo "</pre>";
// echo "<pre>";var_dump($_FILES);echo "</pre>";

if(isset($_POST['b']))
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

// TODO: old code. Now we're operating by board name, not id
$BOARD = db_get_board($link, $smarty, $board_name);

// TODO: old code. Now file type checking does a filetype module
// $types = db_get_board_types($link, $BOARD_NUM);

$board_name = $_POST['b'];

if(!post_check_image_upload_error($_FILES['message_img']['error'], false, "kotoba_stat",
	$error_message))
{ // upload of image failed
	kotoba_error($error_message, $smarty, __FILE__);
}

$uploaded_file_size = $_FILES['message_img']['size'];

$uploaded_file = $_FILES['message_img']['tmp_name'];
$uploaded_name = $_FILES['message_img']['name'];
$recived_ext = post_get_uploaded_extension($uploaded_name);
/*
 * TODO: see previous TODO
if(!post_check_supported_type($recived_ext, $types)) {
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_WRONG_FILETYPE);

	kotoba_error(ERR_WRONG_FILETYPE);
}
 */
if(!post_check_sizes($uploaded_file_size, true, $_POST['message_text'],
	$_POST['message_theme'], $_POST['message_name'], "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}
$message_text = htmlspecialchars($_POST['message_text'], ENT_QUOTES);
$message_theme = htmlspecialchars($_POST['message_theme'], ENT_QUOTES);
$message_name = htmlspecialchars($_POST['message_name'], ENT_QUOTES);
$message_text = stripslashes($message_text);
$message_theme = stripslashes($message_theme);
$message_name = stripslashes($message_name);

if(!post_check_sizes($uploaded_file_size, true, $message_text,
	$message_theme, $message_name, "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}

// mark fuction here
/*
 * TODO: mark routine should be configured
if(!post_mark($link, $message_text, 
	$message_theme, $message_name, "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}
*/

// trip code
// TODO: double tripcode
$namecode = post_tripcode($message_name);
if(is_array($namecode)) {
	$message_name = $namecode[0];
	$tripcode = $namecode[1];
}
else {
	$tripcode = null;
	$message_name = $namecode;
}

require 'thumb_processing.php';
$imageresult = array();
if(!thumb_check_upload_type($link, $smarty, $recived_ext, $uploaded_file, $imageresult)) {
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

$IMG_SRC_DIR = Config::ABS_PATH . "/$board_name/img";
$IMG_THU_DIR = Config::ABS_PATH . "/$board_name/thumb";
$image_virtual_base = sprintf("%s/%s/img", Config::DIR_PATH, $board_name);
$thumbnail_virtual_base = sprintf("%s/%s/thumb", Config::DIR_PATH, $board_name);


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

if(!post_move_uploded_file($uploaded_file, $saved_image_path, "kotoba_stat", $error_message)) 
{
	echo $saved_image_path;
	kotoba_error($error_message, $smarty, __FILE__);
}


// calculate upload hash
// TODO: should be in module
if(($img_hash = hash_file('md5', $saved_image_path)) === false)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_FILE_HASH, $saved_image_path));

	kotoba_error(sprintf(ERR_FILE_HASH, $saved_image_path));
}
// TODO: saeming should be in module
$already_posted = false;
$same_uplodads = post_find_same_uploads($link, $smarty, $board_name, $img_hash);
$same_uplodads_qty = count($same_uplodads);
switch($BOARD['same_upload']) {
case 'no':
	if($same_uplodads_qty > 0) {
		unlink($saved_image_path);
		post_show_uploads_links($link, $board_name, $same_uplodads);
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
// TODO TODO TODO
if(!$already_posted && $imageresult['image'] == 1 && 
	$imageresult['x'] < Config::MIN_IMGWIDTH && $imageresult['y'] < Config::MIN_IMGHEIGTH)
{
	unlink($saved_image_path);
	kotoba_error(Errmsgs::$messages['UNKNOWN'], $smarty);
}
if(!$already_posted && $imageresult['image'] == 1) {
	$thumbnailresult = array();
	$thumb_res = create_thumbnail($link, $smarty, "$IMG_SRC_DIR/$saved_filename", "$IMG_THU_DIR/$saved_thumbname",
		$original_ext, $imageresult['x'], $imageresult['y'], 200, 200,
		$imageresult['force_thumbnail'], $thumbnailresult);


	if($thumb_res != Config::THUMB_SUCCESS)
	{
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
elseif(!$already_posted) {
	$saved_thumbname = $imageresult['thumbnail'];
}

if(!$already_posted) {
	$image = upload($link, $smarty, $board_name, $uploaded_file_size, $img_hash, 
		$imageresult['image'], $image_virtual_path, $imageresult['x'], $imageresult['y'],
		$thumbnail_virtual_path, $thumbnailresult['x'], $thumbnailresult['y']);
	if($image < 0) {
		kotoba_error("Cannot store information about upload");
	}
}


// password settings
$OPPOST_PASS = '';
if(isset($_POST['message_pass']) && $_POST['message_pass'] != '')
{ // password is set and not empty
	$OPPOST_PASS = $_POST['message_pass'];
	if(($OPPOST_PASS = checkformat('pass', $_POST['message_pass'])) === false)
	{ // password have wrong format
		// remove uploaded file/////
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(ERR_PASS_BAD_FORMAT, $smarty);
	}
	
	// save password in cookie
	if(!isset($_COOKIE['rempass']) || $_COOKIE['rempass'] != $OPPOST_PASS) 
		setcookie("rempass", $OPPOST_PASS);
}

// TODO: sage etc
$postid = post($link, $smarty, $board_name, 0, $message_name, $tripcode, $message_theme, $OPPOST_PASS, $_SESSION['user'],
	session_id(), ip2long($_SERVER['REMOTE_ADDR']), $message_text, date("Y-m-d H:i:s"), 0);

if($postid < 0) {
	kotoba_error("Cannot store information about post", $smarty);
}
if(!$already_posted) {
	if(link_post_upload($link, $smarty, $board_name, $image, $postid)) {
		kotoba_error("Cannot link information about post and upload", $smarty);
	}
}
else {
	if(link_post_upload($link, $smarty, $board_name, $same_uplodads[0]['id'], $postid)) {
		kotoba_error("Cannot link information about post and upload", $smarty);
	}
}

$THREAD_NUM = $postid;

if(isset($_POST['goto']) && $_POST['goto'] == 't')
{
	header('Location: ' . Config::DIR_PATH . "/$board_name/$postid/");
	exit;
}

header('Location: ' . Config::DIR_PATH . "/$board_name/");
exit;
?>
