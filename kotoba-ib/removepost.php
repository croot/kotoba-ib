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
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/removepost.stat', 'a')) === false)
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
        
    die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существование доски с именем $BOARD_NAME. Прична: " .  mysql_error() . '</span>' . $FOOTER);
}

// Поиск треда, в котором находится удаляемый пост и проверка, существует ли пост.
if(($result = mysql_query("select `thread` from `posts` where `board` = $BOARD_NUM and `id` = $POST_NUM")) !== false)
{
	if(mysql_num_rows($result) != 1)
	{
		if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_POST_NOT_FOUND, $POST_NUM, $BOARD_NAME));
			
		mysql_free_result($result);
		die($HEAD . "<span class=\"error\">Ошибка. Поста с номером $POST_NUM на доске $BOARD_NAME не существует.</span>" . $FOOTER);
	}
	else
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
        $THREAD_NUM = $row['thread'];
        mysql_free_result($result);
    }
}
else
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_POST_EXIST_CHECK, $POST_NUM, $BOARD_NAME, mysql_error()));
        
    die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существование поста с номером $POST_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . '</error>' . $FOOTER);
}

$HEAD = 
"<html>
<head>
	<title>Kotoba Remove - $BOARD_NAME/$POST_NUM</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"" . KOTOBA_DIR_PATH . '/kotoba.css">
</head>
<body>
';

$FORM =
'
<form action="' . KOTOBA_DIR_PATH . "/removepost.php\" method=\"post\">
<table align=\"center\" border=\"0\">
<tr valign=\"top\"><td>Password: </td><td><input type=\"password\" name=\"Message_pass\" size=\"30\">  <input type=\"submit\" value=\"Remove\"></td></tr>
</table>
<input type=\"hidden\" name=\"b\" value=\"$BOARD_NAME\">
<input type=\"hidden\" name=\"r\" value=\"$POST_NUM\">
</form>
";

if(isset($_COOKIE['rempass']))
{
	if(($REM_PASS = CheckFormat('pass', $_COOKIE['rempass'])) === false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_PASS_BAD_FORMAT);

		die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
	}
}
else
{
	if(isset($_POST['Message_pass']))
	{
		if(($REM_PASS = CheckFormat('pass', $_POST['Message_pass'])) === false)
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(ERR_PASS_BAD_FORMAT);

			die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
		}
    }
	else
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(WARN_NO_REMPASS);

		die($HEAD . "<span class=\"warning\">Предупреждение. Пароль на удаление не задан.</span> Введите пароль.<br>" . $FORM . $FOOTER);
    }
}

// TODO Сделать удаление картинок удаляемых постов.
// TODO Может это не очень хорошо, что пароль хранится всюду в открытом виде.

if($POST_NUM != $THREAD_NUM)	// Удаляется пост из треда.
{
	if(mysql_query("delete from `posts` where `board` = $BOARD_NUM and `id` = $POST_NUM and `thread` = $THREAD_NUM and INSTR(`Post Settings`, 'REMPASS:$REM_PASS') <> 0") === true)
	{
		if(mysql_affected_rows() == 1)	// Пост удалился.
		{
			header('Location: ' . KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/");
			exit;
        }
		else
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(ERR_WRONG_PASSWORD);

			die($HEAD . '<span class="error">Ошибка. Не верный пароль.</span>' . $FOOTER);
        }
	}
	else
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_POST_REMOVE, $POST_NUM, $BOARD_NAME, mysql_error()));

		die($HEAD . "<span class=\"error\">Ошибка. Не удалось удалить пост с номером $POST_NUM с доски $BOARD_NAME. Прична: " .  mysql_error() . '</span>' . $FOOTER);
	}
}
else	// Удаляетя тред.
{
	if(mysql_query("delete from `posts` where `board` = $BOARD_NUM and `id` = $POST_NUM and `thread` = $THREAD_NUM and INSTR(`Post Settings`, 'REMPASS:$REM_PASS') <> 0") === true) // Удалим оппост.
	{
		if(mysql_affected_rows() == 1)
		{
			if(mysql_query("delete from `posts` where `board` = $BOARD_NUM and `thread` = $THREAD_NUM") === false)	// Удалим остальные посты треда.
			{
				if(KOTOBA_ENABLE_STAT)
					kotoba_stat(sprintf(ERR_THREAD_POSTS_REMOVE, $THREAD_NUM, $BOARD_NAME, mysql_error()));

				die($HEAD . "<span class=\"error\">Ошибка. Не удалось удалить посты треда $THREAD_NUM с доски $BOARD_NAME. Прична: " .  mysql_error() . '</span>' . $FOOTER);
			}

			if(mysql_query("delete from `threads` where `board` = $BOARD_NUM and `id` = $THREAD_NUM") === false)	// Удалим тред.
			{
				if(KOTOBA_ENABLE_STAT)
					kotoba_stat(sprintf(ERR_THREAD_REMOVE, $THREAD_NUM, $BOARD_NAME, mysql_error()));

				die($HEAD . "<span class=\"error\">Ошибка. Не удалось удалить тред $THREAD_NUM с доски $BOARD_NAME. Прична: " .  mysql_error() . '</span>' . $FOOTER);
			}
			
			header('Location: ' . KOTOBA_DIR_PATH . "/$BOARD_NAME/");
			exit;
		}
		else
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(ERR_WRONG_PASSWORD);

			die($HEAD . '<span class="error">Ошибка. Не верный пароль.</span>' . $FOOTER);
        }
	}
	else
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_POST_REMOVE, $POST_NUM, $BOARD_NAME, mysql_error()));

		die($HEAD . "<span class=\"error\">Ошибка. Не удалось удалить пост с номером $POST_NUM с доски $BOARD_NAME. Прична: " .  mysql_error() . '</span>' . $FOOTER);
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