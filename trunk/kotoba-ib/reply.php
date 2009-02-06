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

require "common.php";

$HEAD = 
"<html>
<head>
	<title>Error page.</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"$KOTOBA_DIR_PATH/kotoba.css\">
</head>
<body>
";

$FOOTER = 
'
</body>
</html>';

if($KOTOBA_ENABLE_STAT === true)
{
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . $KOTOBA_DIR_PATH . '/reply.stat', 'a')) === false)
    {
        die($HEAD . '<span class="error">Ошибка. Не удалось открыть или создать файл статистики.</span>' . $FOOTER);
    }
}

if(isset($_POST['b']))
{
    $BOARD_NAME = $_POST['b'];
}
else
{
    kotoba_stat("(0001) Ошибка. Для ответа необходимо передать скрипту имя доски.");
    die($HEAD . '<span class="error">Ошибка. Для ответа необходимо передать скрипту имя доски.</span>' . $FOOTER);
}

if(isset($_POST['t']))
{
    $THREAD_NUM = $_POST['t'];
}
else
{
    kotoba_stat("(0028) Ошибка. Для ответа необходимо передать скрипту номер треда.");
    die($HEAD . '<span class="error">Ошибка. Для ответа необходимо передать скрипту номер треда.</span>' . $FOOTER);
}

if(($BOARD_NAME = CheckFormat('board', $BOARD_NAME)) === false)
{
    kotoba_stat("(0002) Ошибка. Имя доски имеет не верный формат.");
    die($HEAD . '<span class="error">Ошибка. Имя доски имеет не верный формат.</span>' . $FOOTER);
}

if(($THREAD_NUM = CheckFormat('thread', $THREAD_NUM)) === false)
{
    kotoba_stat("(0029) Ошибка. Номер треда имеет не верный формат.");
	die($HEAD . '<span class="error">Ошибка. Номер треда имеет не верный формат.</span>' . $FOOTER);
}

require 'database_connect.php';

if(($result = mysql_query("select `id` from `boards` where `Name` = \"$BOARD_NAME\"")) !== false)
{
	if(mysql_num_rows($result) == 0)
	{
        mysql_free_result($result);
        kotoba_stat("(0003) Ошибка. Доски с именем $BOARD_NAME не существует.");
		die($HEAD . "<span class=\"error\">Ошибка. Доски с именем $BOARD_NAME не существует.</span>" . $FOOTER);
	}
	else
	{
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$BOARD_NUM = $row[0];
        mysql_free_result($result);
    }
}
else
{
    kotoba_stat("(0004) Ошибка. Не удалось проверить существание доски с именем $BOARD_NAME. Прична: " . mysql_error());
	die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существание доски с именем $BOARD_NAME. Прична: " .  mysql_error() . "</error>" . $FOOTER);
}

if(($result = mysql_query("select `id` from `threads` where `id` = $THREAD_NUM and `board` = $BOARD_NUM")) != false)
{
	if(mysql_num_rows($result) == 0)
	{
        mysql_free_result($result);
        kotoba_stat("(0031) Ошибка. Треда с номером $THREAD_NUM на доске $BOARD_NAME не существует.");
		die($HEAD . "<span class=\"error\">Ошибка. Треда с номером $THREAD_NUM на доске $BOARD_NAME не существует.</span>" . $FOOTER);
	}
    
    mysql_free_result($result);
}
else
{
    kotoba_stat("(0032) Ошибка. Не удалось проверить существание треда с номером $THREAD_NUM на доске $BOARD_NAME. Прична: " .  mysql_error());
	die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существание треда с номером $THREAD_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . "</error>" . $FOOTER);
}

switch($_FILES['Message_img']['error'])
{
    case UPLOAD_ERR_INI_SIZE:
    kotoba_stat("(0005) Error. The uploaded file exceeds the upload_max_filesize directive in php.ini.");
    die($HEAD . "<span class=\"error\">The uploaded file exceeds the upload_max_filesize directive in php.ini.</span>" . $FOOTER);
    break;

    case UPLOAD_ERR_FORM_SIZE:
    kotoba_stat("(0006) Error. The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
    die($HEAD . "<span class=\"error\">The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.</span>" . $FOOTER);
    break;
    
    case UPLOAD_ERR_PARTIAL:
    kotoba_stat("(0007) Error. The uploaded file was only partially uploaded.");
    die($HEAD . "<span class=\"error\">The uploaded file was only partially uploaded.</span>" . $FOOTER);
    break;
    
    case UPLOAD_ERR_NO_TMP_DIR:
    kotoba_stat("(0009) Error. Missing a temporary folder.");
    die($HEAD . "<span class=\"error\">Missing a temporary folder.</span>" . $FOOTER);
    break;
    
    case UPLOAD_ERR_CANT_WRITE:
    kotoba_stat("(0010) Error. Failed to write file to disk.");
    die($HEAD . "<span class=\"error\">Failed to write file to disk.</span>" . $FOOTER);
    break;
    
    case UPLOAD_ERR_EXTENSION:
    kotoba_stat("(0011) Error. File upload stopped by extension.");
    die($HEAD . "<span class=\"error\">File upload stopped by extension.</span>" . $FOOTER);
    break;
}

if($_FILES['Message_img']['error'] == UPLOAD_ERR_NO_FILE && (!isset($_POST['Message_text']) || $_POST['Message_text'] == ''))
{
    kotoba_stat("(0033) Error. Пост должен содержать по крайней мере картинку или текст.");
    die($HEAD . "<span class=\"error\">Ошибка. Пост должен содержать по крайней мере картинку или текст.</error>" . $FOOTER);
}

if(strlen($_POST['Message_text']) > 30000)
{
    kotoba_stat("(0013) Ошибка. Текст сообщения слишком длинный.");
    die ($HEAD . '<span class="error">Ошибка. Текст сообщения слишком длинный.</span>' . $FOOTER);
}

if(strlen($_POST['Message_theme']) > 120)
{
    kotoba_stat("(0014) Ошибка. Тема слишком длинная.");
    die ($HEAD . '<span class="error">Ошибка. Тема слишком длинная.</span>' . $FOOTER);
}

if(strlen($_POST['Message_name']) > 64)
{
    kotoba_stat("(0015) Ошибка. Имя пользователя слишком длинное.");
    die ($HEAD . '<span class="error">Ошибка. Имя пользователя слишком длинное.</span>' . $FOOTER);
}

$Message_text = htmlspecialchars($_POST['Message_text'], ENT_QUOTES);
$Message_theme = htmlspecialchars($_POST['Message_theme'], ENT_QUOTES);
$Message_name = htmlspecialchars($_POST['Message_name'], ENT_QUOTES);

if(strlen($Message_text) > 30000)
{
    kotoba_stat("(0013) Ошибка. Текст сообщения слишком длинный.");
    die ($HEAD . '<span class="error">Ошибка. Текст сообщения слишком длинный.</span>' . $FOOTER);
}

if(strlen($$Message_theme) > 120)
{
    kotoba_stat("(0014) Ошибка. Тема слишком длинная.");
    die ($HEAD . '<span class="error">Ошибка. Тема слишком длинная.</span>' . $FOOTER);
}

if(strlen($Message_name) > 64)
{
    kotoba_stat("(0015) Ошибка. Имя пользователя слишком длинное.");
    die ($HEAD . '<span class="error">Ошибка. Имя пользователя слишком длинное.</span>' . $FOOTER);
}

$Message_text = str_replace("\n", "<br>", $Message_text);
$Message_text = str_replace("\r", "", $Message_text);
$Message_text = preg_replace('/\&gt\;\&gt\;(\d+)/', "<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$THREAD_NUM/#$1\">&gt;&gt;$1</a>", $Message_text);
$Message_theme = str_replace("\n", "", $Message_theme);
$Message_theme = str_replace("\r", "", $Message_theme);
$Message_name = str_replace("\n", "", $Message_name);
$Message_name = str_replace("\r", "", $Message_name);

if(strlen($Message_text) > 30000)
{
    kotoba_stat("(0013) Ошибка. Текст сообщения слишком длинный.");
    die ($HEAD . '<span class="error">Ошибка. Текст сообщения слишком длинный.</span>' . $FOOTER);
}
    
$Message_text = preg_replace('/(<br>){3,}/', '<br><br>', $Message_text);

$with_image = false;

if($_FILES['Message_img']['error'] == UPLOAD_ERR_OK)
{
    if($_FILES['Message_img']['size'] < $KOTOBA_MIN_IMGSIZE)
    {
        kotoba_stat("(0012) Ошибка. Загружаемый файл имеет слишком маленький размер.");
        die($HEAD . "<span class=\"error\">Ошибка. Загружаемый файл имеет слишком маленький размер.</error>" . $FOOTER);
    }
    
    $recived_filename = explode(".", $_FILES['Message_img']['name']);
    $recived_ext = strtolower($recived_filename[1]);

    switch($recived_ext)
    {
        case 'jpeg':
            $recived_ext = 'jpg';
            break;
        case 'gif':
        case 'png':
        case 'jpg':
            break;
        default:
            kotoba_stat("(0016) Ошибка. Недопустимый тип файла.");
            die ($HEAD . '<span class="error">Ошибка. Недопустимый тип файла.</span>' . $FOOTER);
    }

    list($usec, $sec) = explode(" ", microtime());
    $saved_filename = $sec . substr($usec, 2, 5);
    $saved_thumbname = $saved_filename . "t." . $recived_ext;
    $raw_filename = $saved_filename;
    $saved_filename .= ".$recived_ext";

    $IMG_SRC_DIR = $_SERVER['DOCUMENT_ROOT'] . "$KOTOBA_DIR_PATH/$BOARD_NAME/img";
    $IMG_THU_DIR = $_SERVER['DOCUMENT_ROOT'] . "$KOTOBA_DIR_PATH/$BOARD_NAME/thumb";

    if (move_uploaded_file($_FILES['Message_img']['tmp_name'], "$IMG_SRC_DIR/$saved_filename") === false)
    {
        kotoba_stat("(0017) Ошибка. Файл не удалось сохранить.");
        die ($HEAD . '<span class="error">Ошибка. Файл не удалось сохранить.</span>' . $FOOTER);
    }
    
    if($KOTOBA_ALLOW_SAEMIMG === false)
    {
        $file_hash = hash_file('md5', "$IMG_SRC_DIR/$saved_filename");

        if(($result = mysql_query("select `id`, `thread` from `posts` where `board` = $BOARD_NUM and LOCATE(\"HASH:$file_hash\",`Post Settings`) <> 0")) !== false)
        {
            if(mysql_num_rows($result) == 0)
            {
                mysql_free_result($result);
            }
            else
            {
                $row = mysql_fetch_array($result, MYSQL_NUM);
                kotoba_stat("(0027) Ошибка. Картинка уже была запощена.");
                $saem_img_path = "$KOTOBA_DIR_PATH/$BOARD_NAME/$row[1]/$row[0]/";
                mysql_free_result($result);
                die($HEAD . "<span class=\"error\">Ошибка. Картинка уже была запощена <a href=\"$saem_img_path\">тут</a></span>" . $FOOTER);

            }
        }
        else
        {
            kotoba_stat("(0026) Ошибка. Не удалось проверить существание картинки на доске с именем $BOARD_NAME. Прична: " . mysql_error());
            die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существание картинки на доске с именем $BOARD_NAME. Прична: " .  mysql_error() . "</error>" . $FOOTER);
        }
    }

    $srcimg_res = getimagesize("$IMG_SRC_DIR/$saved_filename");

    if($srcimg_res[0] < $KOTOBA_MIN_IMGWIDTH && $srcimg_res[1] < $KOTOBA_MIN_IMGHEIGTH)
    {
        kotoba_stat("(0018) Ошибка. Разрешение загружаемого изображения слишком маленькое.");
        die($HEAD . "<span class=\"error\">Ошибка. Разрешение загружаемого изображения слишком маленькое.</error>" . $FOOTER);
    }

    require "thumb_processing.php";

    if(!createThumbnail("$IMG_SRC_DIR/$saved_filename", "$IMG_THU_DIR/$saved_thumbname", $recived_ext, 200, 200))
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        vkotoba_stat("(0019) Ошибка. Не удалось создать уменьшенную копию изображения.");
        die ($HEAD . '<span class="error">Ошибка. Не удалось создать уменьшенную копию изображения.</span>' . $FOOTER);
    }

    $Message_img_params = "IMGNAME:$raw_filename\n";
    $Message_img_params .= "IMGEXT:$recived_ext\n";
    $res = getimagesize("$IMG_THU_DIR/$saved_thumbname");
    $Message_img_params .= "IMGTW:" . $res[0] . "\n";
    $Message_img_params .= "IMGTH:" . $res[1] . "\n";
    $res = getimagesize("$IMG_SRC_DIR/$saved_filename");
    $Message_img_params .= "IMGSW:" . $res[0] . "\n";
    $Message_img_params .= "IMGSH:" . $res[1] . "\n";
    $Message_img_params .= "IMGSIZE:" . $_FILES['Message_img']['size'] . "\n";
    
    if($KOTOBA_ALLOW_SAEMIMG === false)
    {
        $Message_img_params .= "HASH:" . hash_file('md5', "$IMG_SRC_DIR/$saved_filename") . "\n";
    }

    $with_image = true;
}

$Message_settings = 'THEME:' . $Message_theme . "\n";
$Message_settings .= 'NAME:' . $Message_name . "\n";
$Message_settings .= 'IP:' . $_SERVER['REMOTE_ADDR'] . "\n";

if(isset($_POST['Sage']) && $_POST['Sage'] == 'sage')
{
    $Message_settings .= "SAGE:Y\n";
}

if($with_image === true)
{
    $Message_settings .= $Message_img_params;
}

if(isset($_POST['Message_pass']) && $_POST['Message_pass'] != '')
{
	if(($REPLY_PASS = CheckFormat('pass', $_POST['Message_pass'])) === false)
	{
		kotoba_stat("(0037) Ошибка. Пароль для удаления имеет не верный формат.");
		die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
	}

	if(!isset($_COOKIE['rempass']) || $_COOKIE['rempass'] != $REPLY_PASS)
		setcookie("rempass", $REPLY_PASS);
		
	$Message_settings .= 'REMPASS:' . $REPLY_PASS . "\n";
}

if(mysql_query('START TRANSACTION') == false)
{
    if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    kotoba_stat("(0020) Ошибка. Невозможно начать транзакцию. Причина: " . mysql_error());
    die ($HEAD . '<span class="error">Ошибка. Невозможно начать транзакцию. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

if(($result = mysql_query("select count(p.`id`) `count` from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` where p.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) group by p.`board`")) == false || mysql_num_rows($result) == 0)
{
    unlink("$IMG_SRC_DIR/$saved_filename");
    unlink("$IMG_THU_DIR/$saved_thumbname");
    kotoba_stat("(0044) Ошибка. Невозможно количество постов доски $BOARD_NAME. Причина: " . mysql_error());
    die ($HEAD . "<span class=\"error\">Ошибка. Невозможно количество постов доски $BOARD_NAME. Причина: " . mysql_error() . '.</span>' . $FOOTER);
}

$row = mysql_fetch_array($result, MYSQL_NUM);
$POST_COUNT = $row[0];
mysql_free_result($result);

while($POST_COUNT >= $KOTOBA_POST_LIMIT)
{
	if(($result = mysql_query("select p.`thread`, count(p.`id`) `count` from `posts` p join `threads` t on p.`thread` = t.`id` where p.`board` = $BOARD_NUM and t.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) and (position('SAGE:Y' in p.`Post Settings`) = 0 or p.`Post Settings` is null) group by p.`thread` order by max(p.`id`) asc limit 1")) == false || mysql_num_rows($result) == 0)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        kotoba_stat("(0045) Ошибка. Невозможно найти тред для сброса в архив. Причина: " . mysql_error());
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно найти тред для сброса в архив. Причина: " . mysql_error() . '.</span>' . $FOOTER);
    }

    $row = mysql_fetch_array($result, MYSQL_NUM);
    $ARCH_THREAD_NUM = $row[0];
    $ARCH_THREAD_POSTCOUNT = $row[1];
    mysql_free_result($result);
    $Thread_Settings = "ARCHIVE:YES\n";

    if(mysql_query("update `threads` set `Thread Settings` = case when `Thread Settings` is null then concat('', '$Thread_Settings') else concat(`Thread Settings`, '$Thread_Settings') end where `id` = $ARCH_THREAD_NUM and `board` = $BOARD_NUM") === false || mysql_affected_rows() == 0)
    {
        $temp = mysql_error();
        mysql_query('ROLLBACK');
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        kotoba_stat("(0046) Ошибка. Невозможно пометить тред на архивирование. Причина: " . $temp);
        die ($HEAD . '<span class="error">Ошибка. Невозможно пометить тред на архивирование. Причина: ' . $temp . '.</span>' . $FOOTER);
    }
    
    if(mysql_query("update `boards` set `Post Count` = (`Post Count` - $ARCH_THREAD_POSTCOUNT) where `id` = $BOARD_NUM") === false || mysql_affected_rows() == 0)
    {
        $temp = mysql_error();
        mysql_query('ROLLBACK');
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        kotoba_stat("(0024) Ошибка. Невозможно пересчитать количество постов доски (архивирование). Причина: " . $temp);
        die ($HEAD . '<span class="error">Ошибка. Невозможно пересчитать количество постов доски (архивирование). Причина: ' . $temp . '.</span>' . $FOOTER);
    }

    if(($result = mysql_query("select count(p.`id`) `count` from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` where p.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) group by p.`board`")) == false)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
        kotoba_stat("(0044) Ошибка. Невозможно количество постов доски $BOARD_NAME. Причина: " . mysql_error());
        die ($HEAD . "<span class=\"error\">Ошибка. Невозможно количество постов доски $BOARD_NAME. Причина: " . mysql_error() . '.</span>' . $FOOTER);
    }

    $row = mysql_fetch_array($result, MYSQL_NUM);
    $POST_COUNT = $row[0];
    mysql_free_result($result);
	
	kotoba_stat("(debug) Утонул тред $ARCH_THREAD_NUM с числом постов $ARCH_THREAD_POSTCOUNT с доски $BOARD_NUM и теперь количество постов доски $POST_COUNT");
}

if(mysql_query("select @post_num := case when `MaxPostNum` is null then 0 else `MaxPostNum` end from `boards` where `id` = $BOARD_NUM") == false)
{
    if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    kotoba_stat("(0034) Ошибка. Не удалось получить номер последнего поста доски. Причина: " . mysql_error());
    die ($HEAD . '<span class="error"> Ошибка. Не удалось получить номер последнего поста доски. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

if(mysql_query('insert into `posts` (`id`, `thread`, `board`, `Time`, `Text`, `Post Settings`) values (@post_num + 1, ' . $THREAD_NUM . ', ' . $BOARD_NUM . ', \'' . date("Y-m-d H:i:s") . '\', \'' . $Message_text . '\', \'' . $Message_settings . '\')') == false)
{
    $temp = mysql_error();
    mysql_query('ROLLBACK');

    if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    kotoba_stat("(0035) Ошибка. Не удалось сохранить пост. Причина: " . mysql_error());
    die ($HEAD . '<span class="error">Ошибка. Не удалось сохранить пост. Причина: ' . $temp . '.</span>' . $FOOTER);
}

if(mysql_query("update `boards` set `Post Count` = (`Post Count` + 1) where `id` = $BOARD_NUM") == false)
{
    $temp = mysql_error();
    mysql_query('ROLLBACK');

    if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    kotoba_stat("(0036) Ошибка. Не удалось увеличить число постов доски. Причина: " . mysql_error());
    die ($HEAD . '<span class="error">Ошибка. Не удалось увеличить число постов доски. Причина: ' . $temp . '.</span>' . $FOOTER);
}

if(mysql_query("update `boards` set `MaxPostNum` = (case when `MaxPostNum` is null then 0 else `MaxPostNum` end + 1) where `id` = $BOARD_NUM") === false || mysql_affected_rows() == 0)
{
    $temp = mysql_error();
    mysql_query('ROLLBACK');
	
	if($with_image === true)
    {
		unlink("$IMG_SRC_DIR/$saved_filename");
		unlink("$IMG_THU_DIR/$saved_thumbname");
	}

    kotoba_stat("(0043) Ошибка. Невозможно установить максимальный пост доски. Причина: " . $temp);
    die ($HEAD . '<span class="error">Ошибка. Невозможно установить максимальный пост доски. Причина: ' . $temp . '.</span>' . $FOOTER);
}

//TODO Сделать чтобы треды тонули.

if(mysql_query('COMMIT') == false)
{
    if($with_image === true)
    {
        unlink("$IMG_SRC_DIR/$saved_filename");
        unlink("$IMG_THU_DIR/$saved_thumbname");
    }

    kotoba_stat("(0025) Ошибка. Невозможно завершить транзакцию. Причина: " . mysql_error());
    die ($HEAD . '<span class="error">Ошибка. Невозможно завершить транзакцию. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

if(isset($_POST['goto']) && $_POST['goto'] == 'b')
{
    header("Location: $KOTOBA_DIR_PATH/$BOARD_NAME/");
    exit;
}

header("Location: $KOTOBA_DIR_PATH/$BOARD_NAME/$THREAD_NUM/");
exit;
?>
<?php

function kotoba_stat($errmsg)
{
    global $KOTOBA_ENABLE_STAT;
    
    if($KOTOBA_ENABLE_STAT === true)
    {
        global $stat_file;
        
        fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
        //fclose($stat_file);
    }
}

?>
