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

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH  . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);	// 1 день.
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

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

require 'events.php';

if(isset($_GET['b']))
{
    if(($BOARD_NAME = CheckFormat('board', $_GET['b'])) === false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_BOARD_BAD_FORMAT);

		die($HEAD . '<span class="error">Ошибка. Имя доски имеет не верный формат.</span>' . $FOOTER);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_BOARD_NOT_SPECIFED);

	die($HEAD . '<span class="error">Ошибка. Не задано имя доски.</span>' . $FOOTER);
}

if(isset($_GET['t']))
{
    if(($THREAD_NUM = CheckFormat('thread', $_GET['t'])) === false)
	{
		if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_THREAD_BAD_FORMAT);

        die($HEAD . '<span class="error">Ошибка. Номер треда имеет не верный формат.</span>' . $FOOTER);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_THREAD_NOT_SPECIFED);

	die($HEAD . '<span class="error">Ошибка. Не задан номер треда.</span>' . $FOOTER);
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

$REPLY_PASS = '';

if(isset($_COOKIE['rempass']))
{
	if(($REPLY_PASS = CheckFormat('pass', $_COOKIE['rempass'])) === false)
	{
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_PASS_BAD_FORMAT);
            
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
$BOARD_NUM = -1;

// Получение списка досок и проверка существут ли доска с заданным именем.
if(($result = mysql_query('select `Name`, `id` from `boards` order by `Name`')) !== false)
{
	if(mysql_num_rows($result) == 0)
	{
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_BOARDS_NOT_EXIST);

        die($HEAD . '<span class="error">Ошибка. Не создано ни одной доски.</span>' . $FOOTER);
	}
	else
	{
		while (($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false)
		{
			if($row['Name'] == $BOARD_NAME)
				$BOARD_NUM = $row['id'];

            $BOARDS_LIST .= '/<a href="' . KOTOBA_DIR_PATH . "/$row[Name]/\">$row[Name]</a>/ ";
		}
    }

	mysql_free_result($result);

	if($BOARD_NUM == -1)
	{
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));

        die($HEAD . "<span class=\"error\">Ошибка. Доски с именем $BOARD_NAME не существует.</span>" . $FOOTER);
    }
}
else
{
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_BOARDS_LIST, mysql_error()));

	die($HEAD . '<span class="error">Ошибка. Невозможно получить список досок. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

$MENU = $BOARDS_LIST . "<br>\n<h4 align=center>βchan</h4>\n<br><center><b>/$BOARD_NAME/$THREAD_NUM/</b></center>\n<hr>\n";

// Проверка существования треда $THREAD_NUM на доске с именем $BOARD_NAME.
if(($result = mysql_query("select `id` from `threads` where `id` = $THREAD_NUM and `board` = $BOARD_NUM")) !== false)
{
	if(mysql_num_rows($result) != 1)
	{
        if(KOTOBA_ENABLE_STAT)
			kotoba_stat(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
			
		mysql_free_result($result);
		die($HEAD . "<span class=\"error\">Ошибка. Треда с номером $THREAD_NUM на доске $BOARD_NAME не найдено.</span>" . $FOOTER);
	}
    
    mysql_free_result($result);
}
else
{
	if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_THREAD_EXIST_CHECK, $THREAD_NUM, $BOARD_NAME, mysql_error()));
    
	die($HEAD . "<span class=\"error\">Ошибка. Не удалось проверить существание треда с номером $THREAD_NUM на доске $BOARD_NAME. Прична: " .  mysql_error() . "</error>" . $FOOTER);
}

// Получение постов просматриваемого треда.
$query = "select `id`, `Time`, `Text`, `Post Settings` from `posts` where `thread` = $THREAD_NUM and `board` = $BOARD_NUM order by `id` asc";
$THREAD = '';

if(($posts = mysql_query($query)))
{
	if(mysql_num_rows($posts) > 0)
	{
		//$isFirst = true;
		$post = mysql_fetch_array($posts, MYSQL_ASSOC);
		$Op_settings = GetSettings('post', $post['Post Settings']);

		$THREAD .= "\n<div>\n";
		$THREAD .= "<span class=\"filetitle\">$Op_settings[THEME]</span> <span class=\"postername\">$Op_settings[NAME]</span> $post[Time]";

		if(isset($Op_settings['THEME']) && $Op_settings['THEME'] != '')
			$HEAD = "<html>\n<head>\n
				\t<title>Kotoba - $BOARD_NAME/$THREAD_NUM - " . $Op_settings['THEME'] . "</title>\n
				\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n
				\t<link rel=\"stylesheet\" type=\"text/css\" href=\"/k/kotoba.css\">\n
				\t<script type=\"text/javascript\" src=\"" . KOTOBA_DIR_PATH . "/kusaba.js\"></script>\n</head>\n<body>\n";
		
		if(isset($Op_settings['IMGNAME']))  // С картинкой.
		{
			$img_thumb_filename = $Op_settings['IMGNAME'] . 't.' . $Op_settings['IMGEXT'];
			$img_filename = $Op_settings['IMGNAME'] . '.' . $Op_settings['IMGEXT'];
			
			$THREAD .= " <span class=\"filesize\">Файл: <a target=\"_blank\" href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>$Op_settings[IMGSIZE] Байт, $Op_settings[IMGSW]x$Op_settings[IMGSH]</em>)</span> <span class=\"reflink\"><span onclick=\"insert('>>$post[id]');\">#</span> <a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/#$post[id]\">$post[id]</a></span> <span class=\"delbtn\">[<a href=\" " . KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[id]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[id]\"></a>\n";
			$THREAD .= "<br><a target=\"_blank\" href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename\"><img src=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"$Op_settings[IMGTW]\" heigth=\"$Op_settings[IMGTH]\"></a>";
			$THREAD .= "<blockquote>\n" . ($post['Text'] == "" ? "<br>" : $post['Text']) . "\n</blockquote>\n";
		}
		else
		{
			$THREAD .= " <span class=\"reflink\"><span onclick=\"insert('>>$post[id]');\">#</span> <a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/#$post[id]\">$post[id]</a></span> <span class=\"delbtn\">[<a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[id]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[id]\"></a>\n";
			$THREAD .= "<br><blockquote>\n" . ($post['Text'] == "" ? "<br>" : $post['Text']) . "\n</blockquote>\n";
        }

		$THREAD .= "<div>\n";
		
		while (($post = mysql_fetch_array($posts, MYSQL_ASSOC)) !== false)
		{
			$Replay_settings = GetSettings('post', $post['Post Settings']);

			$THREAD .= "<table>\n";
			$THREAD .= "<tr>\n\t<td class=\"reply\"><span class=\"filetitle\">$Replay_settings[THEME]</span> <span class=\"postername\">$Replay_settings[NAME]</span> $post[Time]";
			
			if(isset($Replay_settings['IMGNAME']))
			{
				$img_thumb_filename = $Replay_settings['IMGNAME'] . 't.' . $Replay_settings['IMGEXT'];
				$img_filename = $Replay_settings['IMGNAME'] . '.' . $Replay_settings['IMGEXT'];

				$THREAD .= " <span class=\"filesize\">Файл: <a target=\"_blank\" href=\"". KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>$Replay_settings[IMGSIZE] Байт $Replay_settings[IMGSW]x$Replay_settings[IMGSH]</em>)</span> <span class=\"reflink\"><span onclick=\"insert('>>$post[id]');\">#</span> <a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/#$post[id]\">$post[id]</a></span> <span class=\"delbtn\">[<a href=\"" . KOTOBA_DIR_PATH. "/$BOARD_NAME/r$post[id]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[id]\"></a>\n";
				$THREAD .= "\t<br><a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\"><img src=\"$KOTOBA_DIR_PATH/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"" . $Replay_settings['IMGTW'] . "\" heigth=\"" . $Replay_settings['IMGTH'] . "\"></a>";
				$THREAD .= "<blockquote>\n" . ($post['Text'] == "" ? "<br>" : $post['Text']) . "</blockquote>\n\t</td>\n</tr>\n";
			}
			else
			{
				$THREAD .= " <span class=\"reflink\"><span onclick=\"insert('>>$post[id]');\">#</span> <a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/#$post[id]\">$post[id]</a></span> <span class=\"delbtn\">[<a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[id]/\" title=\"Удалить\">×</a>]</span><a name=\"$post[id]\"></a>\n";
				$THREAD .= "\t<br><blockquote>\n" . ($post['Text'] == "" ? "<br>" : $post['Text']) . "</blockquote>\n\t</td>\n</tr>\n";
            }
			
			$THREAD .= "</table>\n";
        }

		$THREAD .= "</div>\n</div>\n<br clear=\"left\">\n<hr>";
	}
    else
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_THREAD_BOARD_POSTS, $THREAD_NUM, $BOARD_NAME));

        die($HEAD . "<span class=\"error\">Ошибка. В треде $THREAD_NUM на доске $BOARD_NAME нет ни одного поста.</span>" . $FOOTER);
    }

	mysql_free_result($posts);
}
else
{
    if(KOTOBA_ENABLE_STAT)
        kotoba_stat(sprintf(ERR_GET_THREAD_POSTS, $THREAD_NUM, $BOARD_NAME, mysql_error()));

    die($HEAD . "<span class=\"error\">Ошибка. Невозможно получить посты для предпросмотра треда $thread[id] доски $BOARD_NAME. Причина: " . mysql_error() . '.</span>' . $FOOTER);
}

echo $HEAD . $MENU . $FORM . '<hr>' . $THREAD . $FOOTER;
?>
<?php

/*
 * Выводит сообщение $errmsg в файл статистики $stat_file.
 *
 */
function kotoba_stat($errmsg)
{
    global $stat_file;
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
    //fclose($stat_file);
}

?>