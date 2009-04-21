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

require 'common.php';

$HEAD = 
'<html>
<head>
	<title>Error page</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="' . KOTOBA_DIR_PATH . '/kotoba.css">
</head>
<body>
';

$FOOTER = 
'
</body>
</html>';

if(KOTOBA_ENABLE_STAT === true)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/reply.stat', 'a')) === false)
        die($HEAD . '<span class="error">Ошибка. Не удалось открыть или создать файл статистики.</span>' . $FOOTER);

require 'events.php';

if(!isset($_POST['b']))
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_BOARD_NOT_SPECIFED);

	die($HEAD . '<span class="error">Ошибка. Не задано имя доски.</span>' . $FOOTER);
}

if(!isset($_POST['t']))
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_THREAD_NOT_SPECIFED);

	die($HEAD . '<span class="error">Ошибка. Не задан номер треда.</span>' . $FOOTER);
}

if(($BOARD_NAME = CheckFormat('board', $_POST['b'])) === false)
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_BOARD_BAD_FORMAT);
        
    die($HEAD . '<span class="error">Ошибка. Имя доски имеет не верный формат.</span>' . $FOOTER);
}

if(($THREAD_NUM = CheckFormat('thread', $_POST['t'])) === false)
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_THREAD_BAD_FORMAT);
        
    die($HEAD . '<span class="error">Ошибка. Номер треда имеет не верный формат.</span>' . $FOOTER);
}

require 'databaseconnect.php';
require_once 'post_processing.php';
$BOARD_NUM = -1;

// Проверка существования доски с именем $BOARD_NAME.
$error_message = "";
$BOARD_NUM = post_get_board_id($BOARD_NAME, "kotoba_stat", $error_message);

if($BOARD_NUM < 0) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

// Проверка существования треда $THREAD_NUM на доске с именем $BOARD_NAME.
if(($result = mysql_query("select t.`id`, count(p.`id`) `count`
	from `threads` t join `posts` p on t.`id` = p.`thread` and t.`board` = p.`board`
	where t.`id` = $THREAD_NUM and t.`board` = $BOARD_NUM group by t.`id`")) !== false)
{
	if(mysql_num_rows($result) != 1)
	{
        if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
			
		mysql_free_result($result);
		die($HEAD . "<span class=\"error\">Ошибка. Треда с номером $THREAD_NUM на доске $BOARD_NAME не найдено.</span>" . $FOOTER);
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
    
	die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существание треда с номером $THREAD_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . "</error>" . $FOOTER);
}

if(!post_check_image_upload_error($_FILES['Message_img']['error'], true, "kotoba_stat", $error_message)) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

if($_FILES['Message_img']['error'] == UPLOAD_ERR_NO_FILE && (!isset($_POST['Message_text']) || $_POST['Message_text'] == ''))
{ // no text no image
	if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_NO_FILE_AND_TEXT);
		
    die($HEAD . '<span class="error">Ошибка. Файл не был загружен и пустой текст сообщения.</error>' . $FOOTER);
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
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

$Message_text = htmlspecialchars($_POST['Message_text'], ENT_QUOTES);
$Message_theme = htmlspecialchars($_POST['Message_theme'], ENT_QUOTES);
$Message_name = htmlspecialchars($_POST['Message_name'], ENT_QUOTES);

if(!post_check_sizes($uploaded_file_size, $with_image, $Message_text,
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

// mark fuction here
if(!post_mark($Message_text, 
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
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
		
		die ($HEAD . '<span class="error">Ошибка. Недопустимый тип файла...</span>' . $FOOTER);
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

	if(!post_move_uploded_file($uploaded_file, $saved_image_path, "kotoba_stat", $error_message)) {
		die($error_message);
	}
	if(!KOTOBA_ALLOW_SAEMIMG)
	{
		if(($img_hash = hash_file('md5', "$IMG_SRC_DIR/$saved_filename")) === false)
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(ERR_FILE_HASH);

			die ($HEAD . "<span class=\"error\">Ошибка. Не удалось вычислить хеш файла $IMG_SRC_DIR/$saved_filename.</span>" . $FOOTER);
		}
		$error_message_array = array();
		if(!post_get_same_image($BOARD_NUM, $BOARD_NAME, $img_hash, "kotoba_stat", $error_message_array)) {
			unlink($saved_image_path);
			if($error_message_array['sameimage']) {
				$link = sprintf("<a href=\"%s/%s/%d#%d\">тут</a>", 
					KOTOBA_DIR_PATH, $BOARD_NAME, $error_message_array['thread'], $error_message_array['post']);
				die ($HEAD . '<span class="error">Ошибка. Картинка уже была запощена ' . $link . '</span>' . $FOOTER);
			}
			else {
				die ($HEAD . '<span class="error">' . $error_message_array['error_message'] . '</span>' . $FOOTER);
			}
		}
	}


	if($imageresult['x'] < KOTOBA_MIN_IMGWIDTH && $imageresult['y'] < KOTOBA_MIN_IMGHEIGTH)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_FILE_LOW_RESOLUTION);
		
		unlink("$IMG_SRC_DIR/$saved_filename");
		die($HEAD . '<span class="error">Ошибка. Разрешение загружаемого изображения слишком маленькое.</span>' . $FOOTER);
	}

	$thumbnailresult = array();
	$thumb_res = create_thumbnail("$IMG_SRC_DIR/$saved_filename", "$IMG_THU_DIR/$saved_thumbname",
		$original_ext, $imageresult['x'], $imageresult['y'], 200, 200,
		$imageresult['force_thumbnail'], $thumbnailresult);

	if($thumb_res != KOTOBA_THUMB_SUCCESS)
	{
		// TODO Сделать вывод причины неудачи создания тумбочки в лог.
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_THUMB_CREATION);

		unlink("$IMG_SRC_DIR/$saved_filename");

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

		die ($HEAD . '<span class="error">Ошибка. Не удалось создать уменьшенную копию изображения: ' . $message .'</span>' .  $FOOTER);
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
        
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
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
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    die ($HEAD . '<span class="error">Ошибка. Невозможно начать транзакцию. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

// Вычисление числа постов доски (в не утонувших тредах).
if(($result = mysql_query(
	"select count(p.`id`) `count`
	from `posts` p
	join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board`
	where p.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null)
	group by p.`board`")) == false || mysql_num_rows($result) == 0)
{
	$temp = mysql_error();
	mysql_query('rollback');
	
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $temp));

	unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: $temp.</span>" . $FOOTER);
}
elseif (mysql_num_rows($result) == 0)   // Нельзя ответить в тред которого нет, если доска пуста.
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));

	mysql_query('rollback');
    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: Возможно не верное имя доски.</span>" . $FOOTER);
}

$row = mysql_fetch_array($result, MYSQL_ASSOC);
$POST_COUNT = $row['count'];
mysql_free_result($result);

// Топим треды.
// TODO Косяк с тредами, в которых постов больше чем лимит постов на доске.
while($POST_COUNT >= KOTOBA_POST_LIMIT)
{
	// Выберем тред, ответ в который был наиболее ранним, и количество постов в нем.
	if(($result = mysql_query(
        "select p.`thread`, count(p.`id`) `count`
        from `posts` p 
        join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` 
        where t.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) and (position('SAGE:Y' in p.`Post Settings`) = 0 or p.`Post Settings` is null) 
        group by p.`thread` 
        order by max(p.`id`) asc limit 1")) == false)
    {
        $temp = mysql_error();
        mysql_query('rollback');
        
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH, $temp));
        
		if($with_image === true)
		{
			unlink("$IMG_SRC_DIR/$saved_filename");
			unlink("$IMG_THU_DIR/$saved_thumbname");
		}

		die ($HEAD . "<span class=\"error\">Ошибка. Невозможно найти тред для сброса в архив. Причина: $temp.</span>" . $FOOTER);
    }
    elseif (mysql_num_rows($result) == 0)
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH, "Возможно не верный номер доски $BOARD_NUM"));
        
		if($with_image === true)
		{
			unlink("$IMG_SRC_DIR/$saved_filename");
			unlink("$IMG_THU_DIR/$saved_thumbname");
		}

        mysql_query('rollback');
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно найти тред для сброса в архив. Причина: Возможно не верный номер доски $BOARD_NUM.</span>" . $FOOTER);
    }

    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $ARCH_THREAD_NUM = $row['thread'];
    $ARCH_THREAD_POSTCOUNT = $row['count'];
    mysql_free_result($result);
    $Thread_Settings = "ARCHIVE:YES\n";

    if(mysql_query("update `threads` set `Thread Settings` = case when `Thread Settings` is null then concat('', '$Thread_Settings') else concat(`Thread Settings`, '$Thread_Settings') end where `id` = $ARCH_THREAD_NUM and `board` = $BOARD_NUM") === false)
    {
        $temp = mysql_error();
        mysql_query('rollback');
        
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_ARCH_THREAD_MARK, $temp));
        
		if($with_image === true)
		{
			unlink("$IMG_SRC_DIR/$saved_filename");
			unlink("$IMG_THU_DIR/$saved_thumbname");
		}

		die ($HEAD . "<span class=\"error\">Ошибка. Невозможно пометить тред для архивирования. Причина: $temp.</span>" . $FOOTER);
    }elseif (mysql_affected_rows() == 0)
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_ARCH_THREAD_MARK, "Возможно не верный номер доски $BOARD_NUM или треда для архивирования $ARCH_THREAD_NUM"));
        
		if($with_image === true)
		{
			unlink("$IMG_SRC_DIR/$saved_filename");
			unlink("$IMG_THU_DIR/$saved_thumbname");
		}

        mysql_query('rollback');
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно пометить тред на архивирование. Причина: Возможно не верный номер доски $BOARD_NUM или треда для архивирования $ARCH_THREAD_NUM.</span>" . $FOOTER);
    }
    
    if(($result = mysql_query(
        'select count(p.`id`) `count` ' .
        'from `posts` p ' .
        'join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` ' .
        'where p.`board` = ' . $BOARD_NUM . ' and (position(\'ARCHIVE:YES\' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) ' .
        'group by p.`board`')) == false)
    {
        $temp = mysql_error();
        mysql_query('rollback');
        
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $temp));

        if($with_image === true)
		{
			unlink("$IMG_SRC_DIR/$saved_filename");
			unlink("$IMG_THU_DIR/$saved_thumbname");
		}

		die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать  количество постов доски $BOARD_NAME. Причина: $temp.</span>" . $FOOTER);
    }
    elseif (mysql_num_rows($result) == 0)
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));
			
		if($with_image === true)
		{
			unlink("$IMG_SRC_DIR/$saved_filename");
			unlink("$IMG_THU_DIR/$saved_thumbname");
		}

        mysql_query('rollback');
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: Возможно не верное имя доски.</span>" . $FOOTER);
    }

    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $POST_COUNT = $row['count'];
    mysql_free_result($result);
	
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(INFO_THREAD_ARCHIVED, $ARCH_THREAD_NUM, $ARCH_THREAD_POSTCOUNT, $BOARD_NUM, $POST_COUNT), false);
}

// `MaxPostNum` не может быть NULL.
if(mysql_query("select @post_num := `MaxPostNum` + 1 from `boards` where `id` = $BOARD_NUM") == false)
{
	$temp = mysql_error();
    mysql_query('rollback');
        
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_NEW_POSTNUM_CALC, $temp));
	
    if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    die ($HEAD . "<span class=\"error\"> Ошибка. Невозможно вычислить номер нового поста. Причина: $temp.</span>" . $FOOTER);
}

if(mysql_query(
	"insert into `posts` (`id`, `thread`, `board`, `Time`, `Text`, `Post Settings`)
	values (@post_num, $THREAD_NUM, $BOARD_NUM, '" . date("Y-m-d H:i:s") . "', '$Message_text', '$Message_settings')") == false)
{
    $temp = mysql_error();
    mysql_query('rollback');
    
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_NEW_POST_CREATE, $temp));

    if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    die ($HEAD . "<span class=\"error\">Ошибка. Не удалось сохранить пост. Причина: $temp.</span>" . $FOOTER);
}

if(mysql_query("update `boards` set `MaxPostNum` = `MaxPostNum` + 1 where `id` = $BOARD_NUM") == false)
{
    $temp = mysql_error();
    mysql_query('rollback');

    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_SET_MAXPOST, $temp));
    
    if($with_image === true)
    {
		unlink("$IMG_SRC_DIR/$saved_filename");
		unlink("$IMG_THU_DIR/$saved_thumbname");
	}
	
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно установить наибольший номер поста доски. Причина: $temp.</span>" . $FOOTER);
}
elseif (mysql_affected_rows() == 0)
{
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_SET_MAXPOST, "Возможно не верный номер доски: $BOARD_NUM"));

	if($with_image === true)
    {
		unlink("$IMG_SRC_DIR/$saved_filename");
		unlink("$IMG_THU_DIR/$saved_thumbname");
	}

    mysql_query('rollback');
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно установить наибольший номер поста доски. Причина: $temp.</span>" . $FOOTER);
}

if(mysql_query('commit') == false)
{
    $temp = mysql_error();
    mysql_query('rollback');
    
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_TRAN_COMMIT_FAILED,  $temp));

	if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }
    
	die ($HEAD . "<span class=\"error\">Ошибка. Невозможно завершить транзакцию. Причина: $temp.</span>" . $FOOTER);
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
