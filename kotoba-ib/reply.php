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

require 'config.php';
require_once('common.php');
require('error_processing.php');
if(KOTOBA_ENABLE_STAT === true)
	if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/reply.stat', 'a')) === false)
		kotoba_error("Ошибка. Не удалось открыть или создать файл статистики");

require 'events.php';

if(!isset($_POST['b']))
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_BOARD_NOT_SPECIFED);

	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

if(!isset($_POST['t']))
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_THREAD_NOT_SPECIFED);

	kotoba_error(ERR_THREAD_NOT_SPECIFED);
}

if(($BOARD_NAME = CheckFormat('board', $_POST['b'])) === false)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_BOARD_BAD_FORMAT);
		
	kotoba_error(ERR_BOARD_BAD_FORMAT);
}

if(($THREAD_NUM = CheckFormat('thread', $_POST['t'])) === false)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_THREAD_BAD_FORMAT);
		
	kotoba_error(ERR_THREAD_BAD_FORMAT);
}

require 'databaseconnect.php';
require_once 'post_processing.php';
$BOARD_NUM = -1;

// Проверка существования доски с именем $BOARD_NAME.
$error_message = "";
$BOARD_NUM = post_get_board_id($BOARD_NAME, "kotoba_stat", $error_message);

if($BOARD_NUM < 0) {
	kotoba_error($error_message);
}

// Проверка существования треда $THREAD_NUM на доске с именем $BOARD_NAME.
$sql = sprintf("select t.id, count(p.id) as count
	from threads t join posts p on t.id = p.thread and t.board = p.board
	where t.id = %d and t.board = %d group by t.id", $THREAD_NUM, $BOARD_NUM);
if(($result = mysql_query($sql)) !== false)
{
	if(mysql_num_rows($result) != 1)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
			
		mysql_free_result($result);
		kotoba_error(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
	}
	else
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$THREAD_POSTCOUNT = $row['count'];
		mysql_free_result($result);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_THREAD_EXIST_CHECK, $THREAD_NUM, $BOARD_NAME, mysql_error()));
	
	kotoba_error(sprintf(ERR_THREAD_EXIST_CHECK, $THREAD_NUM, $BOARD_NAME, mysql_error()));
}

if(!post_check_image_upload_error($_FILES['Message_img']['error'], true, "kotoba_stat", 
	$error_message)) 
{
	kotoba_error($error_message);
}

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
else { // image and text
	$with_image = true;
}

$uploaded_file_size = $_FILES['Message_img']['size'];

if(!post_check_sizes($uploaded_file_size, $with_image, $_POST['Message_text'],
	$_POST['Message_theme'], $_POST['Message_name'], "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}

$Message_text = htmlspecialchars($_POST['Message_text'], ENT_QUOTES);
$Message_theme = htmlspecialchars($_POST['Message_theme'], ENT_QUOTES);
$Message_name = htmlspecialchars($_POST['Message_name'], ENT_QUOTES);

if(!post_check_sizes($uploaded_file_size, $with_image, $Message_text,
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}

// mark fuction here
if(!post_mark($Message_text, 
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	kotoba_error($error_message);
}

// post have attached image
if($with_image) {
	$uploaded_file = $_FILES['Message_img']['tmp_name'];
	$uploaded_name = $_FILES['Message_img']['name'];
	$recived_ext = post_get_uploaded_extension($uploaded_name);

	require 'thumb_processing.php';
	$imageresult = array();
	if(!thumb_check_image_type($recived_ext, $uploaded_file, $imageresult)) {
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

	$saved_image_path = sprintf("%s/%s", $IMG_SRC_DIR, $saved_filename);
	$saved_thumbnail_path = sprintf("%s/%s", $IMG_THU_DIR, $saved_thumbname);

	if(!post_move_uploded_file($uploaded_file, $saved_image_path, "kotoba_stat",
		$error_message)) 
	{
		kotoba_error($error_message);
	}
	if(!KOTOBA_ALLOW_SAEMIMG)
	{
		if(($img_hash = hash_file('md5', $saved_image_path)) === false)
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(ERR_FILE_HASH);

			kotoba_error(ERR_FILE_HASH);
		}
		$error_message_array = array();
		if(!post_get_same_image($BOARD_NUM, $BOARD_NAME, $img_hash, "kotoba_stat",
			$error_message_array))
		{
			unlink($saved_image_path);
			if($error_message_array['sameimage']) {
				$link = sprintf("<a href=\"%s/%s/%d#%d\">тут</a>",
					KOTOBA_DIR_PATH, $BOARD_NAME, $error_message_array['thread'], 
					$error_message_array['post']);
				kotoba_error(sprintf("Ошибка. Картинка уже была запощена %s", $link));
			}
			else {
				kotoba_error($error_message_array['error_message']);
			}
		}
	}

	if($imageresult['x'] < KOTOBA_MIN_IMGWIDTH && $imageresult['y'] < KOTOBA_MIN_IMGHEIGTH)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_FILE_LOW_RESOLUTION);
		
		unlink($saved_image_path);
		kotoba_error(ERR_FILE_LOW_RESOLUTION);
	}

	$thumbnailresult = array();
	$thumb_res = create_thumbnail($saved_image_path, $saved_thumbnail_path,
		$original_ext, $imageresult['x'], $imageresult['y'], 200, 200,
		$imageresult['force_thumbnail'], $thumbnailresult);

	if($thumb_res != KOTOBA_THUMB_SUCCESS)
	{
		// TODO Сделать вывод причины неудачи создания тумбочки в лог.
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_THUMB_CREATION);

		unlink($saved_image_path);

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

		kotoba_error(sprintf("Ошибка. Не удалось создать уменьшенную копию изображения: %s", $message));
	}

	$Message_img_params = "IMGNAME:$raw_filename\n";
	$Message_img_params .= "IMGEXT:$recived_ext\n";
	$Message_img_params .= "ORIGIMGEXT:$original_ext\n";
	$Message_img_params .= "IMGTW:" . $thumbnailresult['x'] . "\n";
	$Message_img_params .= "IMGTH:" . $thumbnailresult['y'] . "\n";
	$Message_img_params .= "IMGSW:" . $imageresult['x'] . "\n";
	$Message_img_params .= "IMGSH:" . $imageresult['y'] . "\n";
	$Message_img_params .= 'IMGSIZE:' . $uploaded_file_size . "\n";
	if(! KOTOBA_ALLOW_SAEMIMG)
		$Message_img_params .= "HASH:$img_hash\n";
}


$Message_settings = "THEME:$Message_theme\n";
$Message_settings .= "NAME:$Message_name\n";
$Message_settings .= "IP:$_SERVER[REMOTE_ADDR]\n";

if(isset($_POST['Sage']) && $_POST['Sage'] == 'sage')
	$Message_settings .= "SAGE:Y\n";

if($THREAD_POSTCOUNT > KOTOBA_BUMPLIMIT)
	$Message_settings .= "BLIMIT:Y\n";

if($with_image)
	$Message_settings .= $Message_img_params;

if(isset($_POST['Message_pass']) && $_POST['Message_pass'] != '')
{
	if(($REPLY_PASS = CheckFormat('pass', $_POST['Message_pass'])) === false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_PASS_BAD_FORMAT);
		
		post_remove_files($saved_filename, $saved_thumbname);
		kotoba_error(ERR_PASS_BAD_FORMAT);
	}

	if(!isset($_COOKIE['rempass']) || $_COOKIE['rempass'] != $REPLY_PASS)
		setcookie("rempass", $REPLY_PASS);
		
	$Message_settings .= "REMPASS:$REPLY_PASS\n";
}

if(mysql_query('start transaction') == false)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_TRAN_FAILED, mysql_error()));
		
	if($with_image)
	{
		post_remove_files($saved_filename, $saved_thumbname);
	}

	kotoba_error(sprintf(ERR_TRAN_FAILED, mysql_error()));
}

// Вычисление числа постов доски (в не утонувших тредах).
$sql = sprintf("select count(p.id) as count
	from posts p
	join threads t on p.thread = t.id and p.board = t.board
	where p.board = %d and 
		(position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null)
	group by p.board", $BOARD_NUM);
if(($result = mysql_query($sql)) == false || mysql_num_rows($result) == 0)
{
	$sql_error = mysql_error();
	mysql_query('rollback');
	
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));

	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));
}
elseif (mysql_num_rows($result) == 0)   // Нельзя ответить в тред которого нет, если доска пуста.
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));

	mysql_query('rollback');
	post_remove_files($saved_filename, $saved_thumbname);
	kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));
}

$row = mysql_fetch_array($result, MYSQL_ASSOC);
$POST_COUNT = $row['count'];
mysql_free_result($result);

// Топим треды.
// TODO Косяк с тредами, в которых постов больше чем лимит постов на доске.
while($POST_COUNT >= KOTOBA_POST_LIMIT)
{
	// Выберем тред, ответ в который был наиболее ранним, и количество постов в нем.
	$sql = sprintf("select p.thread, count(p.id) as count
		from posts p 
		join threads t on p.thread = t.id and p.board = t.board 
		where t.board = %d and 
			(position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or
			t.`Thread Settings` is null) and (position('SAGE:Y' in p.`Post Settings`) = 0 or
			p.`Post Settings` is null) 
		group by p.thread 
		order by max(p.id) asc limit 1", $BOARD_NUM);
	if(($result = mysql_query($sql)) == false)
	{
		$sql_error = mysql_error();
		mysql_query('rollback');
		
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH, $sql_error));
		
		if($with_image === true)
		{
			post_remove_files($saved_filename, $saved_thumbname);
		}

		kotoba_error(sprintf(ERR_ARCH_THREAD_SEARCH, $sql_error));
	}
	elseif (mysql_num_rows($result) == 0)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH, 
				"Возможно не верный номер доски $BOARD_NUM"));
		
		if($with_image === true)
		{
			post_remove_files($saved_filename, $saved_thumbname);
		}

		mysql_query('rollback');
		kotoba_error(sprintf(ERR_ARCH_THREAD_SEARCH, 
			"Возможно не верный номер доски $BOARD_NUM"));
	}

	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$ARCH_THREAD_NUM = $row['thread'];
	$ARCH_THREAD_POSTCOUNT = $row['count'];
	mysql_free_result($result);
	$Thread_Settings = "ARCHIVE:YES\n";

	$sql = sprintf("update threads set `Thread Settings` = case
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
		
		if($with_image === true)
		{
			post_remove_files($saved_filename, $saved_thumbname);
		}

		kotoba_error(sprintf(ERR_ARCH_THREAD_MARK, $sql_error));
	}
	elseif (mysql_affected_rows() == 0)
	{
		if(KOTOBA_ENABLE_STAT) {
kotoba_stat(sprintf(ERR_ARCH_THREAD_MARK, 
	"Возможно не верный номер доски $BOARD_NUM или треда для архивирования $ARCH_THREAD_NUM"));
		}
		
		if($with_image === true)
		{
			post_remove_files($saved_filename, $saved_thumbname);
		}

		mysql_query('rollback');
kotoba_error(sprintf(ERR_ARCH_THREAD_MARK,
	"Возможно не верный номер доски $BOARD_NUM или треда для архивирования $ARCH_THREAD_NUM"));
	}

	$sql = sprintf("select count(p.id) as count
		from posts p
		join threads t on p.thread = t.id and p.board = t.board
		where p.board = %d and 
			(position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null)
		group by p.board", $BOARD_NUM);
	if(($result = mysql_query($sql)) == false)
	{
		$sql_error = mysql_error();
		mysql_query('rollback');
		
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));

		if($with_image === true)
		{
			post_remove_files($saved_filename, $saved_thumbname);
		}

		kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $sql_error));
	}
	elseif (mysql_num_rows($result) == 0)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));
			
		if($with_image === true)
		{
			post_remove_files($saved_filename, $saved_thumbname);
		}

		mysql_query('rollback');
		kotoba_error(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));
	}

	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$POST_COUNT = $row['count'];
	mysql_free_result($result);
	
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(INFO_THREAD_ARCHIVED,
			$ARCH_THREAD_NUM, $ARCH_THREAD_POSTCOUNT, $BOARD_NUM, $POST_COUNT), false);
}

// `MaxPostNum` не может быть NULL.
$sql = sprintf("select @post_num := MaxPostNum + 1 from boards where id = %d", $BOARD_NUM);
if(mysql_query($sql) == false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');
		
	if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_NEW_POSTNUM_CALC, $sql_error));
	
	if($with_image === true)
	{
		post_remove_files($saved_filename, $saved_thumbname);
	}

	kotoba_error(sprintf(ERR_NEW_POSTNUM_CALC, $sql_error));
}
/*
 * "insert into `posts` (`id`, `thread`, `board`, `Time`, `Text`, `Post Settings`)
 values (@post_num, $THREAD_NUM, $BOARD_NUM, '" . date("Y-m-d H:i:s") . "', '$Message_text', '$Message_settings')"
 */

$sql = sprintf("insert into posts 
	(id, thread, board, Time, Text, `Post Settings`)
	values (@post_num, %d, %d, '%s', '%s', '%s')",
	$THREAD_NUM, $BOARD_NUM, date("Y-m-d H:i:s"), $Message_text, $Message_settings);
if(mysql_query($sql) == false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');
	
	if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_NEW_POST_CREATE, $sql_error));

	if($with_image === true)
	{
		post_remove_files($saved_filename, $saved_thumbname);
	}

	kotoba_error(sprintf(ERR_NEW_POST_CREATE, $sql_error));
}

$sql = sprintf("update boards set MaxPostNum = MaxPostNum + 1 where id = %d", $BOARD_NUM);
if(mysql_query($sql) == false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');

	if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_SET_MAXPOST, $sql_error));
	
	if($with_image === true)
	{
		post_remove_files($saved_filename, $saved_thumbname);
	}
	
	kotoba_error(sprintf(ERR_SET_MAXPOST, $sql_error));
}
elseif (mysql_affected_rows() == 0)
{
	if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_SET_MAXPOST, "Возможно не верный номер доски: $BOARD_NUM"));

	if($with_image === true)
	{
		post_remove_files($saved_filename, $saved_thumbname);
	}

	mysql_query('rollback');
	kotoba_error(sprintf(ERR_SET_MAXPOST, "Возможно не верный номер доски: $BOARD_NUM"));
}

if(mysql_query('commit') == false)
{
	$sql_error = mysql_error();
	mysql_query('rollback');
	
	if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_TRAN_COMMIT_FAILED,  $sql_error));

	if($with_image === true)
	{
		unlink("$IMG_SRC_DIR/$saved_filename");
		unlink("$IMG_THU_DIR/$saved_thumbname");
	}
	
	kotoba_error(sprintf(ERR_TRAN_COMMIT_FAILED,  $sql_error));
}

if(isset($_POST['goto']) && $_POST['goto'] == 'b')
{
	header('Location: ' . KOTOBA_DIR_PATH . "/$BOARD_NAME/");
	exit;
}

header('Location: ' . KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/");
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
