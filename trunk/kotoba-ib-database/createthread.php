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

require 'config.php';
require 'common.php';
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

require 'database_connect.php';
require 'database_common.php';

$link = dbconn();

$error_message = "default error";
$BOARD = db_get_board($link, $BOARD_NAME);

if(count($BOARD) == 0) {
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

$BOARD_NUM = $BOARD['id'];

if($BOARD_NUM < 0) {
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

// Этап 2. Обработка данных ОП поста.


if(!post_check_image_upload_error($_FILES['Message_img']['error'], false, "kotoba_stat",
	$error_message))
{ // upload of image failed
	kotoba_error($error_message);
}

$uploaded_file_size = $_FILES['Message_img']['size'];

if(!post_check_sizes($uploaded_file_size, true, $_POST['Message_text'],
	$_POST['Message_theme'], $_POST['Message_name'], "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}

$Message_text = htmlspecialchars($_POST['Message_text'], ENT_QUOTES);
$Message_theme = htmlspecialchars($_POST['Message_theme'], ENT_QUOTES);
$Message_name = htmlspecialchars($_POST['Message_name'], ENT_QUOTES);

if(!post_check_sizes($uploaded_file_size, true, $Message_text,
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}

// mark fuction here
if(!post_mark($Message_text, 
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}
$uploaded_file = $_FILES['Message_img']['tmp_name'];
$uploaded_name = $_FILES['Message_img']['name'];
$recived_ext = post_get_uploaded_extension($uploaded_name);

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


// full path of uploaded image and generated thumbnail
$saved_image_path = sprintf("%s/%s", $IMG_SRC_DIR, $saved_filename);
if($imageresult['image'] == 1) {
	$saved_thumbnail_path = sprintf("%s/%s", $IMG_THU_DIR, $saved_thumbname);
}
else {
	$saved_thumbnail_path = $imageresult['thumbnail'];
}

if(!post_move_uploded_file($uploaded_file, $saved_image_path, "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}

// calculate upload hash
if(($img_hash = hash_file('md5', $saved_image_path)) === false)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_FILE_HASH);

	kotoba_error(ERR_FILE_HASH);
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
if($imageresult['image'] == 1 && 
	$imageresult['x'] < KOTOBA_MIN_IMGWIDTH && $imageresult['y'] < KOTOBA_MIN_IMGHEIGTH)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_FILE_LOW_RESOLUTION);
	
	unlink($saved_image_path);
	kotoba_error(ERR_FILE_LOW_RESOLUTION);
}
if($imageresult['image'] == 1) {
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

header("Content-type: text/plain");

echo 
"IMGNAME:$raw_filename\n".
"IMGEXT:$recived_ext\n".
"ORIGIMGEXT:$original_ext\n".
"IMGTW:" . $thumbnailresult['x'] .
"IMGTH:" . $thumbnailresult['y'] .
"IMGSW:" . $imageresult['x'] .
"IMGSH:" . $imageresult['y'] .
'IMGSIZE:' . $uploaded_file_size .
"HASH:$img_hash\n";

exit();

// password settings
if(isset($_POST['Message_pass']) && $_POST['Message_pass'] != '')
{ // password is set and not empty
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

// Этап 3. Сохранение ОП поста в БД.

if(mysql_query('start transaction') === false)
{ // transaction failed. why?
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_TRAN_FAILED, mysql_error()));
	// remove uploaded files
	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_TRAN_FAILED, mysql_error()));
}

// Вычисление числа постов доски (в не утонувших тредах).
//
// 
$sql = sprintf("select count(p.id) as count
	from posts p
	join threads t on p.thread = t.id and p.board = t.board 
	where p.board = %d and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or
		t.`Thread Settings` is null) 
	group by p.board", $BOARD_NUM);
if(($result = mysql_query($sql)) === false)
{ // error getting posts in visible threads
	$sql_error = mysql_error();
	mysql_query('rollback');
		
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));

	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));
}
elseif (mysql_num_rows($result) == 0)
{ // У вновь созданной доски может и не быть ни постов ни тредов.
	mysql_free_result($result);
	// get count of posts on board
	$sql = sprintf("select count(id) as count from posts
		where board = %d", $BOARD_NUM);
	if(($result = mysql_query($sql)) === false)
	{ // get count faild
		$sql_error = mysql_error();
		mysql_query('rollback');

		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));

		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));
	}
	elseif(mysql_num_rows($result) == 0)
	{ // nothing counted
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_POST_COUNT_CALC,
				$BOARD_NAME, 'Возможно не верное имя доски'));

		mysql_query('rollback');
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(sprintf(ERR_POST_COUNT_CALC,
			$BOARD_NAME, 'Возможно не верное имя доски'));
	}
}

// have posts in non-drowned threads
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$POST_COUNT = $row['count'];
mysql_free_result($result);

// Топим треды.
while($POST_COUNT >= KOTOBA_POST_LIMIT)
{
	// Выберем тред, ответ в который был наиболее ранним, и количество постов в нем.
	$sql = sprintf("select p.thread as thread, count(p.id) as count
		from posts p 
		join threads t on p.thread = t.id and p.board = t.board 
		where t.board = %d and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0
			or t.`Thread Settings` is null)
		and (position('SAGE:Y' in p.`Post Settings`) = 0 or p.`Post Settings` is null) 
		group by p.thread 
		order by max(p.id) asc limit 1", $BOARD_NUM);
	if(($result = mysql_query($sql)) === false)
	{ //error executing error
		$sql_error = mysql_error();
		mysql_query('rollback');
		
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH, $sql_error));
			
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(sprintf(ERR_ARCH_THREAD_SEARCH, $sql_error));
	}
	elseif (mysql_num_rows($result) == 0)
	{ // nothing found
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH,
				"Возможно не верный номер доски $BOARD_NUM"));

		mysql_query('rollback');
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(sprintf(ERR_ARCH_THREAD_SEARCH,
			"Возможно не верный номер доски $BOARD_NUM"));
	}
	// have earlier posts
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$ARCH_THREAD_NUM = $row['thread'];
	$ARCH_THREAD_POSTCOUNT = $row['count'];
	mysql_free_result($result);
	$Thread_Settings = "ARCHIVE:YES\n";

	$sql = sprintf("
		update threads set 
		`Thread Settings` = case 
			when `Thread Settings` is null then concat('', '%s')
			else concat(`Thread Settings`, '%s') 
		end 
		where id = %d and board = %d",
		$Thread_Settings, $Thread_Settings, $ARCH_THREAD_NUM, $BOARD_NUM);
	if(mysql_query($sql) === false)
	{
		$sql_error = mysql_error();
		mysql_query('rollback');
		
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_ARCH_THREAD_MARK, $sql_error));
			
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(sprintf(ERR_ARCH_THREAD_MARK, $sql_error));
	}
	elseif (mysql_affected_rows() == 0)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_ARCH_THREAD_MARK, 
				"Возможно не верный номер доски $BOARD_NUM или
				треда для архивирования $ARCH_THREAD_NUM"));

		mysql_query('rollback');
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(sprintf(ERR_ARCH_THREAD_MARK,
			"Возможно не верный номер доски $BOARD_NUM или
				треда для архивирования $ARCH_THREAD_NUM"));
	}
	$sql = sprintf("select count(p.id) as count
		from posts p
		join threads t on p.thread = t.id and p.board = t.board
		where p.board =  %d  and 
		(position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null)
		group by p.board", $BOARD_NUM);
	if(($result = mysql_query($sql)) == false)
	{
		$sql_error = mysql_error();
		mysql_query('rollback');
		
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));

		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));
	}
	elseif (mysql_num_rows($result) == 0)
	{ // У вновь созданной доски может и не быть ни постов ни тредов.
		mysql_free_result($result);
		$sql = sprintf("select count(id) as count from posts where board = %d", $BOARD_NUM);
		if(($result = mysql_query($sql)) === false)
		{
			$sql_error = mysql_error();
			mysql_query('rollback');

			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));

			post_remove_files($saved_filename, $saved_thumbname);
			kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));
		}
		elseif(mysql_num_rows($result) == 0)
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(sprintf(ERR_POST_COUNT_CALC,
					$BOARD_NAME, 'Возможно не верное имя доски'));

			mysql_query('rollback');
			post_remove_files($saved_filename, $saved_thumbname);
			kotoba_error(sprintf(ERR_POST_COUNT_CALC, 
				$BOARD_NAME, 'Возможно не верное имя доски'));
		}
	}

	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$POST_COUNT = $row['count'];
	mysql_free_result($result);
	
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(INFO_THREAD_ARCHIVED,
			$ARCH_THREAD_NUM, $ARCH_THREAD_POSTCOUNT, $BOARD_NUM, $POST_COUNT), false);
}

// `MaxPostNum` не может быть NULL.
$sql = sprintf("select @op_post_num := MaxPostNum + 1 from boards where id = %d", $BOARD_NUM);
if(($result = mysql_query($sql)) == false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');
		
	if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_NEW_OPPOSTNUM_CALC, $sql_error));

	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_NEW_OPPOSTNUM_CALC, $sql_error));
}

$row = mysql_fetch_array($result, MYSQL_NUM);
$THREAD_NUM = ($row[0]) ? $row[0] : 1;		  // Номер оп поста и номер треда одно и тоже.
mysql_free_result($result);

$Message_settings  = "THEME:$Message_theme\n";
$Message_settings .= "NAME:$Message_name\n";
$Message_settings .= "IP:$_SERVER[REMOTE_ADDR]\n";
$Message_settings .= $Message_img_params;

if(isset($OPPOST_PASS))
	$Message_settings .= "REMPASS:$OPPOST_PASS\n";

$sql = sprintf("insert into threads (id, board) values (@op_post_num, %d)", $BOARD_NUM);
if(mysql_query($sql) == false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');
	
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_NEW_THREAD_CREATE, $sql_error));
	
	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_NEW_THREAD_CREATE, $sql_error));
}
// add post to database
// Не будем пока проверять, добавила ли вставка строку в таблицу.
$sql = sprintf("insert into posts 
	(id, thread, board, Time, Text, `Post Settings`) 
	values (@op_post_num, @op_post_num, %d, '%s', '%s','%s')",
		$BOARD_NUM, date("Y-m-d H:i:s"), $Message_text, $Message_settings);
if(mysql_query($sql) == false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');
	
	if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_NEW_OPPOST_CREATE, $sql_error));
	
	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_NEW_OPPOST_CREATE, $sql_error));
}
// count posts(threads) on board
$sql = sprintf("update boards set MaxPostNum = MaxPostNum + 1 where id = %d", $BOARD_NUM);
if(mysql_query($sql) === false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');

	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_SET_MAXPOST, $sql_error));

	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_SET_MAXPOST, $sql_error));
}
elseif (mysql_affected_rows() == 0)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_SET_MAXPOST, "Возможно не верный номер доски: $BOARD_NUM"));

	mysql_query('rollback');
	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_SET_MAXPOST, "Возможно не верный номер доски: $BOARD_NUM"));
}

//if(mysql_query('commit') == false)
//{ // unable commit transaction, why?
//	$sql_error = mysql_error();
//	mysql_query('rollback');
//	
//	if(KOTOBA_ENABLE_STAT)
//		kotoba_stat(sprintf(ERR_TRAN_COMMIT_FAILED,  $sql_error));
//
//	post_remove_files($saved_filename, $saved_thumbname);
//	kotoba_error(sprintf(ERR_TRAN_COMMIT_FAILED,  $sql_error));
//}

mysql_close($link);

// Этап 4. Перенаправление.

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
