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

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH  . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);	// 1 день.
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

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
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/threads.stat', 'a')) === false)
        die($HEAD . '<span class="error">Ошибка. Не удалось открыть или создать файл статистики.</span>' . $FOOTER);

if(isset($_GET['b']) && isset($_GET['t']))
{
	$BOARD_NAME = $_GET['b'];
	$THREAD_NUM = $_GET['t'];
}
else
{
	header("Location: $KOTOBA_DIR_PATH/");
	exit;
}

// Имя доски имеет не верный формат?
if(CheckFormat('board', $BOARD_NAME) === false)
{
    header("Location: $KOTOBA_DIR_PATH/");
    exit;
}

require 'database_connect.php';

$HEAD = 
"<html>
<head>
	<title>Kotoba - $BOARD_NAME/$THREAD_NUM</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"/k/kotoba.css\">
	<script type=\"text/javascript\" src=\"" . KOTOBA_DIR_PATH . "/kusaba.js\"></script>
</head>
<body>
";

$FOOTER = 
'
</body>
</html>';

$REPLY_PASS = '';

if(isset($_COOKIE['rempass']))
{
	if(($REPLY_PASS = CheckFormat('pass', $_COOKIE['rempass'])) === false)
	{
		kotoba_stat("(0037) Ошибка. Пароль для удаления имеет не верный формат.");
		die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
	}
}

$FORM =
'
<form name="Reply_form" action="' . KOTOBA_DIR_PATH . "/reply.php\" method=\"post\" enctype=\"multipart/form-data\">
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1560576\">
<table align=\"center\" border=\"0\">
<tr valign=\"top\"><td>Name: </td><td><input type=\"text\" name=\"Message_name\" size=\"30\"></td></tr>
<tr valign=\"top\"><td>Theme: </td><td><input type=\"text\" name=\"Message_theme\" size=\"56\"> <input type=\"submit\" value=\"Reply\"></td></tr>
<tr valign=\"top\"><td>Message: </td><td><textarea name=\"Message_text\" rows=\"7\" cols=\"50\"></textarea></td></tr>
<tr valign=\"top\"><td>Image: </td><td><input type=\"file\" name=\"Message_img\" size=\"54\"></td></tr>
<tr valign=\"top\"><td>Password: </td><td><input type=\"password\" name=\"Message_pass\" size=\"30\" value=\"$REPLY_PASS\"></td></tr>
<tr valign=\"top\"><td>GoTo: </td><td>(thread: <input type=\"radio\" name=\"goto\" value=\"t\" checked>) (board: <input type=\"radio\" name=\"goto\" value=\"b\">)</td></tr>
<tr valign=\"top\"><td>Sage: </td><td><input type=\"checkbox\" name=\"Sage\" value=\"sage\"></td></tr>
</table>
<input type=\"hidden\" name=\"b\" value=\"$BOARD_NAME\">
<input type=\"hidden\" name=\"t\" value=\"$THREAD_NUM\">
</form>
";

$BOARDS_LIST = '';

// Получение списка досок и проверка существут ли доска с заданным именем.
if(($result = mysql_query('select `Name`, `id` from `boards` order by `Name`')) !== false)
{
	if(mysql_num_rows($result) == 0)
	{
		header("Location: $KOTOBA_DIR_PATH/");
        exit;
	}
	else
	{
		$row = mysql_fetch_array($result, MYSQL_NUM);
		
		while ($row !== false)
		{
			if($row[0] == $BOARD_NAME)
			{
				$exist = true;
				$BOARD_NUM = $row[1];
            }

            $BOARDS_LIST .= "/<a href=\"$KOTOBA_DIR_PATH/$row[0]/\">$row[0]</a>/ ";			
			$row = mysql_fetch_array($result, MYSQL_NUM);
		}
    }

	mysql_free_result($result);
	
	if(!isset($exist))
	{
		header("Location: $KOTOBA_DIR_PATH/");
		exit;
	}
}
else
{
	$BOARDS_LIST = '<span class="error">Ошибка при получении списка досок. Причина: ' . mysql_error() . '.</span>';
}

$MENU = $BOARDS_LIST . "<br>\n<h4 align=center>βchan</h4>\n<br><center><b>/$BOARD_NAME/$THREAD_NUM/</b></center>\n<hr>\n";

// Имя треда имеет не верный формат.
if(CheckFormat('thread', $THREAD_NUM) === false)
{
	header("Location: $KOTOBA_DIR_PATH/");
	exit;
}

$THREAD = '';

// Существует ли тред с номером $THREAD_NUM?
if(($result = mysql_query('select `id` from `threads` where `id` = ' . $THREAD_NUM)) != false)
{
	// Треда с таким номером не существует.
	if(mysql_num_rows($result) == 0)
	{
		header("Location: $KOTOBA_DIR_PATH/");
		exit;
	}
}
else
{
	$THREAD .= '<span class="error">Ошибка при проверке номера треда. Причина: ' . mysql_error() . '.</span><br>';
}

// Получение постов просматриваемого треда.
$query = "select `id`, `Time`, `Text`, `Post Settings` from `posts` where `thread` = $THREAD_NUM and `board` = " . $BOARD_NUM . " order by `id` asc";

if(($posts = mysql_query($query)) != false)
{
	if(mysql_num_rows($posts) > 0)
	{
		$isFirst = true;
		$post = mysql_fetch_array($posts, MYSQL_NUM);		
		$Op_settings = GetSettings('post', $post[3]);

		$THREAD .= "\n<div>\n";
		$THREAD .= "<span class=\"filetitle\">" . $Op_settings['THEME'] . "</span> <span class=\"postername\">" . $Op_settings['NAME'] . "</span> " . $post[1];

		if(isset($Op_settings['THEME']) && $Op_settings['THEME'] != '')
			$HEAD = "<html>\n<head>\n
				\t<title>Kotoba - $BOARD_NAME/$THREAD_NUM - " . $Op_settings['THEME'] . "</title>\n
				\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n
				\t<link rel=\"stylesheet\" type=\"text/css\" href=\"/k/kotoba.css\">\n
				\t<script type=\"text/javascript\" src=\"$KOTOBA_DIR_PATH/kusaba.js\"></script>\n</head>\n<body>\n";
		
		if(isset($Op_settings['IMGNAME']))
		{
			$img_thumb_filename = $Op_settings['IMGNAME'] . 't.' . $Op_settings['IMGEXT'];
			$img_filename = $Op_settings['IMGNAME'] . '.' . $Op_settings['IMGEXT'];
			
			$THREAD .= " <span class=\"filesize\">Файл: <a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>" .  $Op_settings['IMGSIZE'] . " Байт, " . $Op_settings['IMGSW'] . "x" . $Op_settings['IMGSH'] . "</em>)</span> <span class=\"reflink\"><span onclick=\"insert('>>$post[0]');\">#</span> <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$THREAD_NUM/#$post[0]\">$post[0]</a></span> <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[0]\"></a>\n";
			$THREAD .= "<br><a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\"><img src=\"$KOTOBA_DIR_PATH/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"" . $Op_settings['IMGTW'] . "\" heigth=\"" . $Op_settings['IMGTH'] . "\"></a>";
			$THREAD .= "<blockquote>\n" . ($post[2] == "" ? "<br>" : $post[2]) . "\n</blockquote>\n";
		}
		else
		{
			$THREAD .= " <span class=\"reflink\"><span onclick=\"insert('>>$post[0]');\">#</span> <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$THREAD_NUM/#$post[0]\">$post[0]</a></span> <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[0]\"></a>\n";
			$THREAD .= "<br><blockquote>\n" . ($post[2] == "" ? "<br>" : $post[2]) . "\n</blockquote>\n";
        }

		$THREAD .= "<div>\n";
		
		$post = mysql_fetch_array($posts, MYSQL_NUM);

		while ($post)
		{
			$Replay_settings = GetSettings('post', $post[3]);

			$THREAD .= "<table>\n";
			$THREAD .= "<tr>\n\t<td class=\"reply\"><span class=\"filetitle\">" . $Replay_settings['THEME'] . "</span> <span class=\"postername\">" . $Replay_settings['NAME'] . "</span>  " . $post[1];
			
			if(isset($Replay_settings['IMGNAME']))
			{
				$img_thumb_filename = $Replay_settings['IMGNAME'] . 't.' . $Replay_settings['IMGEXT'];
				$img_filename = $Replay_settings['IMGNAME'] . '.' . $Replay_settings['IMGEXT'];

				$THREAD .= " <span class=\"filesize\">Файл: <a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>" .  $Replay_settings['IMGSIZE'] . " Байт " . $Replay_settings['IMGSW'] . "x" . $Replay_settings['IMGSH'] . "</em>)</span> <span class=\"reflink\"><span onclick=\"insert('>>$post[0]');\">#</span> <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$THREAD_NUM/#$post[0]\">$post[0]</a></span> <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[0]\"></a>\n";
				$THREAD .= "\t<br><a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\"><img src=\"$KOTOBA_DIR_PATH/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"" . $Replay_settings['IMGTW'] . "\" heigth=\"" . $Replay_settings['IMGTH'] . "\"></a>";
				$THREAD .= "<blockquote>\n" . ($post[2] == "" ? "<br>" : $post[2]) . "</blockquote>\n\t</td>\n</tr>\n";
			}
			else
			{
				$THREAD .= " <span class=\"reflink\"><span onclick=\"insert('>>$post[0]');\">#</span> <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$THREAD_NUM/#$post[0]\">$post[0]</a></span> <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[0]\"></a>\n";
				$THREAD .= "\t<br><blockquote>\n" . ($post[2] == "" ? "<br>" : $post[2]) . "</blockquote>\n\t</td>\n</tr>\n";
            }
			
			$THREAD .= "</table>\n";

			$post = mysql_fetch_array($posts, MYSQL_NUM);
		}

		$THREAD .= "</div>\n</div>\n<br clear=\"left\">\n<hr>";
	}

	mysql_free_result($posts);
}
else
{
	$THREAD .= '<span class="error">Ошибка при получении постов. Причина: ' . mysql_error() . '.</span>';
}

echo $HEAD . $MENU . $FORM . '<hr>' . $THREAD . $FOOTER;

function exit_with($err_msg)
{
	global $HEAD, $MENU, $FORM, $FOOTER;
	die ($HEAD . $MENU . $FORM . '<hr>' . $err_msg . '<hr>' . $FOOTER);
}
?>