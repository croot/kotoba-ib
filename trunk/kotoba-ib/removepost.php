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

if(!isset($_GET['b']))
{
    if(!isset($_POST['b']))
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_BOARD_NOT_SPECIFED);

        die($HEAD . '<span class="error">Ошибка. Не задано имя доски.</span>' . $FOOTER);
    }
    else
    {
        $BOARD_NAME = $_POST['b'];
    }
}
else
{
    $BOARD_NAME = $_GET['b'];
}

if(!isset($_GET['r']))
{
    if(!isset($_POST['r']))
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_POST_NOT_SPECIFED);

        die($HEAD . '<span class="error">Ошибка. Не задан номер поста.</span>' . $FOOTER);
    }
    else
    {
        $POST_NUM =   $_POST['r'];
    }
}
else
{
    $POST_NUM =   $_GET['r'];
}

if(($BOARD_NAME = CheckFormat('board', $BOARD_NAME)) === false)
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_BOARD_BAD_FORMAT);
        
    die($HEAD . '<span class="error">Ошибка. Имя доски имеет не верный формат.</span>' . $FOOTER);
}

if(($POST_NUM = CheckFormat('post', $POST_NUM)) === false)
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_POST_BAD_FORMAT);
        
    die($HEAD . '<span class="error">Ошибка. Номер поста имеет не верный формат.</span>' . $FOOTER);
}


$HEAD = 
'<html>
<head>
	<title>Kotoba Remove</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="' . KOTOBA_DIR_PATH . '/kotoba.css">
</head>
<body>
';

require 'database_connect.php';

// Проверка существования доски с именем $BOARD_NAME.
if(($result = mysql_query("select `id` from `boards` where `Name` = \"$BOARD_NAME\"")) !== false)
{
	if(mysql_num_rows($result) != 1)
	{
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));
			
		mysql_free_result($result);
		die($HEAD . "<span class=\"error\">Ошибка. Доски с именем $BOARD_NAME не существует.</span>" . $FOOTER);
	}
	else
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$BOARD_NUM = $row['id'];
        mysql_free_result($result);
    }
}
else
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_BOARD_EXIST_CHECK, $BOARD_NAME, mysql_error()));
        
    die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существание доски с именем $BOARD_NAME. Прична: " .  mysql_error() . '</span>' . $FOOTER);
}

//
if(($result = mysql_query("select `thread` from `posts` where `board` = $BOARD_NUM and `id` = $POST_NUM")) !== false)
{
	if(mysql_num_rows($result) == 0)
	{
        mysql_free_result($result);
        kotoba_stat("(0038) Ошибка. Поста с номером $POST_NUM на доске $BOARD_NAME не существует.");
		die($HEAD . "<span class=\"error\">Ошибка. Поста с номером $POST_NUM на доске $BOARD_NAME не существует.</span>" . $FOOTER);
	}
	else
	{
		$row = mysql_fetch_array($result, MYSQL_NUM);
        $THREAD_NUM = $row[0];
        mysql_free_result($result);
    }
}
else
{
    kotoba_stat("(0039) Ошибка. Не удалось проверить существание поста с номером $POST_NUM на доске $BOARD_NAME. Прична: " . mysql_error());
	die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существание поста с номером $POST_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . "</error>" . $FOOTER);
}

$HEAD = 
"<html>
<head>
	<title>Kotoba Remove - $BOARD_NAME/$POST_NUM</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"/k/kotoba.css\">
</head>
<body>
";

$FORM =
"
<form action=\"$KOTOBA_DIR_PATH/removepost.php\" method=\"post\">
<table align=\"center\" border=\"0\">
<tr valign=\"top\"><td>Password: </td><td><input type=\"password\" name=\"Message_pass\" size=\"30\">  <input type=\"submit\" value=\"Reply\"></td></tr>
</table>
<input type=\"hidden\" name=\"b\" value=\"$BOARD_NAME\">
<input type=\"hidden\" name=\"r\" value=\"$POST_NUM\">
</form>
";

if(!isset($_COOKIE['rempass']) && !isset($_POST['Message_pass']))
	die($HEAD . "<p>Пароль на удаление не задан. Введите пароль.</p>" . $FORM . $FOOTER);

if(isset($_COOKIE['rempass']))
	$REM_PASS = $_COOKIE['rempass'];
	
if(isset($_POST['Message_pass']))
	$REM_PASS = $_POST['Message_pass'];
	
if(($REM_PASS = CheckFormat('pass', $REM_PASS)) === false)
{
	kotoba_stat("(0037) Ошибка. Пароль для удаления имеет не верный формат.");
	die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
}

// TODO Сделать удаление картинок удаляемых постов.

if($POST_NUM != $THREAD_NUM)
{
	if(mysql_query("delete from `posts` where `board` = $BOARD_NUM and `id` = $POST_NUM and `thread` = $THREAD_NUM and INSTR(`Post Settings`, 'REMPASS:$REM_PASS') <> 0") === true && mysql_affected_rows() > 0)
	{
		if(mysql_query("update `boards` set `Post Count` = (`Post Count` - 1) where `id` = $BOARD_NUM") === true && mysql_affected_rows() > 0)
		{
			header("Location: $KOTOBA_DIR_PATH/$BOARD_NAME/$THREAD_NUM/");
			exit;
		}
		else
		{
			kotoba_stat("(0041) Ошибка. Не удалось пересчитать количество постов на доске $BOARD_NAME. Прична: " .  mysql_error());
			die($HEAD . "<span class=\"error\">Ошибка. Не удалось пересчитать количество постов на доске $BOARD_NAME. Прична: " .  mysql_error() . "</span>" . $FOOTER);
		}
	}
	else
	{
		kotoba_stat("(0040) Ошибка. Не верный пароль $REM_PASS для удаления поста с номером $POST_NUM на доске $BOARD_NAME. Прична: " .  mysql_error());
		die($HEAD . "<span class=\"error\">Ошибка. Не верный пароль $REM_PASS для удаления поста с номером $POST_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . "</span>" . $FOOTER);
	}
}
else
{
	if(mysql_query("delete from `posts` where `board` = $BOARD_NUM and `id` = $POST_NUM and `thread` = $THREAD_NUM and INSTR(`Post Settings`, 'REMPASS:$REM_PASS') <> 0") === true && mysql_affected_rows() > 0)
	{
		if(mysql_query("delete from `posts` where `board` = $BOARD_NUM and `thread` = $THREAD_NUM") === false)
		{
			kotoba_stat("(0042) Ошибка. Не удалось удалить посты треда $THREAD_NUM на доске $BOARD_NAME. Прична: " .  mysql_error());
			die($HEAD . "<span class=\"error\">Ошибка. Не удалось удалить посты треда $THREAD_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . "</span>" . $FOOTER);
		}

		if(mysql_query("update `boards` join (select p.`board`, count(p.`id`) `count` from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` where p.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) group by p.`board`) q set `Post Count` = `count` where `id` = q.`board`") === true && mysql_affected_rows() > 0)
		{
			header("Location: $KOTOBA_DIR_PATH/$BOARD_NAME/");
			exit;
		}
		else
		{
			kotoba_stat("(0041) Ошибка. Не удалось пересчитать количество постов на доске $BOARD_NAME. Прична: " .  mysql_error());
			die($HEAD . "<span class=\"error\">Ошибка. Не удалось пересчитать количество постов на доске $BOARD_NAME. Прична: " .  mysql_error() . "</span>" . $FOOTER);
		}
	}
	else
	{
		kotoba_stat("(0040) Ошибка. Не верный пароль $REM_PASS для удаления поста с номером $POST_NUM на доске $BOARD_NAME. Прична: " .  mysql_error());
		die($HEAD . "<span class=\"error\">Ошибка. Не верный пароль $REM_PASS для удаления поста с номером $POST_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . "</span>" . $FOOTER);
	}
}
?>
<?php

function kotoba_stat($errmsg)
{
    global $stat_file;
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
    //fclose($stat_file);
}

?>