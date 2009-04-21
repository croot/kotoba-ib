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

// Заметки:
//
// Для каждого скрипта, при включенном сборе статистики, создаётся файл имя_скрипта.stat в котором будет хранится статистика.
// Такой файл называется Лог статистики.
//
// Как, куда и когда выводить статистику решает скрипт. Что выводить - решает events.php. Если вы ходите изменить
// выводимый текст в лог статистики, используйте константы в events.php.

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

if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/createthread.stat', 'a')) === false)
        die($HEAD . '<span class="error">Ошибка. Не удалось открыть или создать файл статистики.</span>' . $FOOTER);

require 'events.php';

// Этап 1. Проверка имени доски, на которой создаётся тред.

if(!isset($_POST['b']))
{
	if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_BOARD_NOT_SPECIFED);
        
    die($HEAD . '<span class="error">Ошибка. Не задано имя доски.</span>' . $FOOTER);
}

if(($BOARD_NAME = CheckFormat('board', $_POST['b'])) === false)
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_BOARD_BAD_FORMAT);
        
    die($HEAD . '<span class="error">Ошибка. Имя доски имеет не верный формат.</span>' . $FOOTER);
}

require 'databaseconnect.php';
require_once 'post_processing.php';

$error_message = "";
$BOARD_NUM = post_get_board_id($BOARD_NAME, "kotoba_stat", $error_message);

if($BOARD_NUM < 0) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

// Этап 2. Обработка данных ОП поста.


if(!post_check_image_upload_error($_FILES['Message_img']['error'], false, "kotoba_stat", $error_message)) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

$uploaded_file_size = $_FILES['Message_img']['size'];

if(!post_check_sizes($uploaded_file_size, true, $_POST['Message_text'],
	$_POST['Message_theme'], $_POST['Message_name'], "kotoba_stat", $error_message)) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

$Message_text = htmlspecialchars($_POST['Message_text'], ENT_QUOTES);
$Message_theme = htmlspecialchars($_POST['Message_theme'], ENT_QUOTES);
$Message_name = htmlspecialchars($_POST['Message_name'], ENT_QUOTES);

if(!post_check_sizes($uploaded_file_size, true, $Message_text,
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}

// mark fuction here
if(!post_mark($Message_text, 
	$Message_theme, $Message_name, "kotoba_stat", $error_message)) {
	die($HEAD . '<span class="error">' . $error_message . '</span>' . $FOOTER);
}
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

if(! KOTOBA_ALLOW_SAEMIMG)
{
    if(($img_hash = hash_file('md5', $saved_image_path)) === false)
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_FILE_HASH);

        die ($HEAD . "<span class=\"error\">Ошибка. Не удалось вычислить хеш файла $saved_image_path.</span>" . $FOOTER);
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

if(!KOTOBA_ALLOW_SAEMIMG)
    $Message_img_params .= "HASH:$img_hash\n";


// password settings
if(isset($_POST['Message_pass']) && $_POST['Message_pass'] != '')
{ // password is set and not empty
	if(($OPPOST_PASS = CheckFormat('pass', $_POST['Message_pass'])) === false)
	{ // password have wrong format
		if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_PASS_BAD_FORMAT);
        // remove uploaded file
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
	}

	if(!isset($_COOKIE['rempass']) || $_COOKIE['rempass'] != $OPPOST_PASS) // save password in cookie
		setcookie("rempass", $OPPOST_PASS);
}

// Этап 3. Сохранение ОП поста в БД.

if(mysql_query('start transaction') === false)
{ // transaction failed. why?
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_TRAN_FAILED, mysql_error()));
    // remove uploaded files
    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . '<span class="error">Ошибка. Невозможно начать транзакцию. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

// Вычисление числа постов доски (в не утонувших тредах).
if(($result = mysql_query(
    "select count(p.`id`) `count`
    from `posts` p 
    join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` 
    where p.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) 
    group by p.`board`")) === false)
{ // error getting posts in visible threads
	$temp = mysql_error();
	mysql_query('rollback');
		
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $temp));

    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: $temp.</span>" . $FOOTER);
}
elseif (mysql_num_rows($result) == 0)   // У вновь созданной доски может и не быть ни постов ни тредов.
{
    mysql_free_result($result);
    // get count of posts on board
    if(($result = mysql_query("select count(`id`) `count` from `posts` where `board` = $BOARD_NUM")) === false)
    { // get count faild
        $temp = mysql_error();
        mysql_query('rollback');

        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $temp));

        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: $temp.</span>" . $FOOTER);
    }
    elseif(mysql_num_rows($result) == 0)
    { // nothing counted
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));

        mysql_query('rollback');
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: Возможно не верное имя доски.</span>" . $FOOTER);
    }
}

$row = mysql_fetch_array($result, MYSQL_ASSOC);
$POST_COUNT = $row['count'];
mysql_free_result($result);

// Топим треды.
while($POST_COUNT >= KOTOBA_POST_LIMIT)
{
    // Выберем тред, ответ в который был наиболее ранним, и количество постов в нем.
	if(($result = mysql_query(
        "select p.`thread`, count(p.`id`) `count`
        from `posts` p 
        join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` 
        where t.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) and (position('SAGE:Y' in p.`Post Settings`) = 0 or p.`Post Settings` is null) 
        group by p.`thread` 
        order by max(p.`id`) asc limit 1")) === false)
    {
        $temp = mysql_error();
        mysql_query('rollback');
        
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH, $temp));
            
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно найти тред для сброса в архив. Причина: $temp.</span>" . $FOOTER);
    }
    elseif (mysql_num_rows($result) == 0)
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_ARCH_THREAD_SEARCH, "Возможно не верный номер доски $BOARD_NUM"));
        
        mysql_query('rollback');
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
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
            
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно пометить тред для архивирования. Причина: $temp.</span>" . $FOOTER);
    }elseif (mysql_affected_rows() == 0)
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_ARCH_THREAD_MARK, "Возможно не верный номер доски $BOARD_NUM или треда для архивирования $ARCH_THREAD_NUM"));
        
        mysql_query('rollback');
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
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

        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать  количество постов доски $BOARD_NAME. Причина: $temp.</span>" . $FOOTER);
    }
    elseif (mysql_num_rows($result) == 0)   // У вновь созданной доски может и не быть ни постов ни тредов.
    {
        mysql_free_result($result);

        if(($result = mysql_query("select count(`id`) `count` from `posts` where `board` = $BOARD_NUM")) === false)
        {
            $temp = mysql_error();
            mysql_query('rollback');

            if(KOTOBA_ENABLE_STAT)
                kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, $temp));

            unlink("$IMG_SRC_DIR/$saved_filename");
            unlink("$IMG_THU_DIR/$saved_thumbname");
            die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: $temp.</span>" . $FOOTER);
        }
        elseif(mysql_num_rows($result) == 0)
        {
            if(KOTOBA_ENABLE_STAT)
                kotoba_stat(sprintf(ERR_POST_COUNT_CALC, $BOARD_NAME, 'Возможно не верное имя доски'));

            mysql_query('rollback');
            unlink("$IMG_SRC_DIR/$saved_filename");
            unlink("$IMG_THU_DIR/$saved_thumbname");
            die ($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов доски $BOARD_NAME. Причина: Возможно не верное имя доски.</span>" . $FOOTER);
        }
    }

    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $POST_COUNT = $row['count'];
    mysql_free_result($result);
	
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(INFO_THREAD_ARCHIVED, $ARCH_THREAD_NUM, $ARCH_THREAD_POSTCOUNT, $BOARD_NUM, $POST_COUNT), false);
}

// `MaxPostNum` не может быть NULL.
if(($result = mysql_query("select @op_post_num := `MaxPostNum` + 1 from `boards` where `id` = $BOARD_NUM")) == false)
{
    $temp = mysql_error();
    mysql_query('rollback');
        
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_NEW_OPPOSTNUM_CALC, $temp));

    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно вычислить номер нового оп поста. Причина: $temp.</span>" . $FOOTER);
}

$row = mysql_fetch_array($result, MYSQL_NUM);
$THREAD_NUM = ($row[0]) ? $row[0] : 1;          // Номер оп поста и номер треда одно и тоже.
mysql_free_result($result);

$Message_settings  = "THEME:$Message_theme\n";
$Message_settings .= "NAME:$Message_name\n";
$Message_settings .= "IP:$_SERVER[REMOTE_ADDR]\n";
$Message_settings .= $Message_img_params;

if(isset($OPPOST_PASS))
	$Message_settings .= "REMPASS:$OPPOST_PASS\n";

if(mysql_query("insert into `threads` (`id`, `board`) values (@op_post_num, $BOARD_NUM)") == false)
{
    $temp = mysql_error();
    mysql_query('rollback');
    
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_NEW_THREAD_CREATE, $temp));
    
    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно создать новый тред. Причина: $temp.</span>" . $FOOTER);
}

// Не будем пока проверять, добавила ли вставка строку в таблицу.
if(mysql_query(
    "insert into `posts` (`id`, `thread`, `board`, `Time`, `Text`, `Post Settings`) 
    values (@op_post_num, @op_post_num, $BOARD_NUM, '" . date("Y-m-d H:i:s") . "', '$Message_text','$Message_settings')") == false)
{
    $temp = mysql_error();
    mysql_query('rollback');
    
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_NEW_OPPOST_CREATE, $temp));
    
    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно создать новый оп пост. Причина: $temp.</span>" . $FOOTER);
}

if(mysql_query("update `boards` set `MaxPostNum` = `MaxPostNum` + 1 where `id` = $BOARD_NUM") === false)
{
    $temp = mysql_error();
    mysql_query('rollback');

    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_SET_MAXPOST, $temp));
    
    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно установить наибольший номер поста доски. Причина: $temp.</span>" . $FOOTER);
}
elseif (mysql_affected_rows() == 0)
{
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_SET_MAXPOST, "Возможно не верный номер доски: $BOARD_NUM"));

    mysql_query('rollback');
    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно установить наибольший номер поста доски. Причина: $temp.</span>" . $FOOTER);
}

if(mysql_query('commit') == false)
{
    $temp = mysql_error();
    mysql_query('rollback');
    
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_TRAN_COMMIT_FAILED,  $temp));

    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно завершить транзакцию. Причина: $temp.</span>" . $FOOTER);
}

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
