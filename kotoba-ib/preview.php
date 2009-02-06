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

// TODO Переделать логирование ошибок и сбор статистики ошибок.

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/k/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

require "common.php";

$KOTOBA_DIR_PATH = KOTOBA_DIR_PATH;

if(isset($_GET['b']))
{
	
    if(($BOARD_NAME = CheckFormat('board', $_GET['b'])) === false)
    {
        header('Location: ' . KOTOBA_DIR_PATH . '/');
        exit;
    }
}
else
{
	header("Location: $KOTOBA_DIR_PATH/");
	exit;
}

$HEAD = 
"<html>
<head>
	<title>Kotoba - $BOARD_NAME</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"$KOTOBA_DIR_PATH/kotoba.css\">
</head>
<body>
";

$FOOTER = 
'
</body>
</html>';

$OPPOST_PASS = '';

if(isset($_COOKIE['rempass']))
{
	if(($OPPOST_PASS = CheckFormat('pass', $_COOKIE['rempass'])) === false)
	{
		kotoba_stat("(0037) Ошибка. Пароль для удаления имеет не верный формат.");
		die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
	}
}

$FORM =
"
<form action=\"" . KOTOBA_DIR_PATH . "/createthread.php\" method=\"post\" enctype=\"multipart/form-data\">
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"1560576\">
<table align=\"center\" border=\"0\">
<tr valign=\"top\"><td>Name: </td><td><input type=\"text\" name=\"Message_name\" size=\"30\"></td></tr>
<tr valign=\"top\"><td>Theme: </td><td><input type=\"text\" name=\"Message_theme\" size=\"48\"> <input type=\"submit\" value=\"Create Thread\"></td></tr>
<tr valign=\"top\"><td>Message: </td><td><textarea name=\"Message_text\" rows=\"7\" cols=\"50\"></textarea></td></tr>
<tr valign=\"top\"><td>Image: </td><td><input type=\"file\" name=\"Message_img\" size=\"54\"></td></tr>
<tr valign=\"top\"><td>Password: </td><td><input type=\"password\" name=\"Message_pass\" size=\"30\" value=\"$OPPOST_PASS\"></td></tr>
<tr valign=\"top\"><td>GoTo: </td><td>(thread: <input type=\"radio\" name=\"goto\" value=\"t\">) (board: <input type=\"radio\" name=\"goto\" value=\"b\" checked>)</td></tr>
</table>
<input type=\"hidden\" name=\"b\" value=\"$BOARD_NAME\">
</form>
";

require 'database_connect.php';

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
        mysql_free_result($result);
        header("Location: $KOTOBA_DIR_PATH/");
        exit;
    }
}
else
{
	$BOARDS_LIST = '<span class="error">Ошибка при получении списка досок. Причина: ' . mysql_error() . '.</span>';
}

// Фигня не нужная.
$result = mysql_query("select p.`board`, count(p.`id`) `count` from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` where (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) group by p.`board` having p.`board` = $BOARD_NUM");
$row = mysql_fetch_array($result, MYSQL_NUM);
$POST_COUNT = $row[1];
mysql_free_result($result);

$MENU = $BOARDS_LIST . "<br>\n<h4 align=center>βchan</h4>\n<br><center><b>/$BOARD_NAME/</b></center>\n$POST_COUNT/$KOTOBA_POST_LIMIT<hr>\n";

// Получение количества тредов.
if(($result = mysql_query("select count(*) from `threads` where `board` = $BOARD_NUM and (position('ARCHIVE:YES' in `Thread Settings`) = 0 or `Thread Settings` is null)")) !== false)
{
	$row = mysql_fetch_array($result, MYSQL_NUM);
    $threards_count = $row[0];
    mysql_free_result($result);
    
    $pages_count = ($threards_count / 10) + 1;
    
    if(isset($_GET['p']))
    {
        if(($PAGE = CheckFormat('page', $_GET['p'])) === false)
        {
            header("Location: $KOTOBA_DIR_PATH/");
            exit;
        }
    }
    else
    {
        $PAGE = 1;
    }

    if($PAGE < 1 || $PAGE > $pages_count)
    {
        header("Location: $KOTOBA_DIR_PATH/");
        exit;
    }
    
    $threads_range = " limit " . (($PAGE - 1) * 10) . ", 10";

	$PAGES = "<br>";
	
	for($i = 1; $i <= $pages_count; $i++)
	{
		if($i != $PAGE)
		{
			$PAGES .= "(<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/p$i/\">" . ($i < 10 ? "0$i" : "$i") . "</a>) ";
		}
		else
		{
			$PAGES .= "(" . ($i < 10 ? "0$i" : "$i") . ") ";
        }
    }
}
else
{
	//die
}

// Получение номеров тредов просматривоемой доски.
if(($threads = mysql_query("select p.`thread` from `posts` p join `threads` t on p.`thread` = t.`id` where p.`board` = $BOARD_NUM and t.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) group by p.`thread` order by max(p.`id`) desc $threads_range")) != false)
{
	if(mysql_num_rows($threads) > 0)
	{
		$PREVIEW = '';
		$i = 0;						// Количество тредов.
		$last_posts = array();		// Номера последних постов тредов.
		$threads_preview = array(); // HTML код предпросмотра тредов.
		$thread_preview_code = '';	// HTML код предпросмотра текущего треда.

		$thread = mysql_fetch_array($threads, MYSQL_NUM);
		
		while ($thread)
		{
			$PREVIEW_REPLAYS_COUNT = 4;	// Количество ответов в предпросмотре треда.
            $POSTS_COUNT = 0;			// Число постов в треде.
			$last_post_number = null;

			$query = "(select `id`, `Time`, `Text`, `Post Settings` from `posts` where thread = $thread[0] and `board` = $BOARD_NUM order by `id` asc limit 1) union (select `id`, `Time`, `Text`, `Post Settings` from `posts` where thread = $thread[0] and `board` = $BOARD_NUM order by `id` desc limit $PREVIEW_REPLAYS_COUNT) order by `id` asc";
			
            // Получение постов треда для предпросмотра.
            if(($posts = mysql_query($query)) != false)
			{
				if(mysql_num_rows($posts) > 0)
				{
                    if(($result = mysql_query("select count(`id`) from `posts` where `thread` = $thread[0] and `board` = $BOARD_NUM")) != false)
                    {
                        $row = mysql_fetch_array($result, MYSQL_NUM);
                        $POSTS_COUNT = $row[0];
                    }
                    else
                    {
                        $PREVIEW .= "<span class=\"error\">При получении количества постов треда $THREAD_NUM для предпросмотра произошла ошибка. Причина: " . mysql_error() . '.</span>';
                    }
					
					$post = mysql_fetch_array($posts, MYSQL_NUM);					
					$Op_settings = GetSettings('post', $post[3]);

                    $Message_text = '';
                    $offset = 0;
                    $line = 1;

                    while($line <= $KOTOBA_LONGPOST_LINES && (($offset = strpos($post[2], "<br>", ($offset == 0 ? $offset : $offset + strlen("<br>")))) !== false))
                    {
                        $line++;
                    }
                    
                    if($line == ($KOTOBA_LONGPOST_LINES + 1) && $offset !== false)
                    {
                        $Message_text = substr($post[2], 0, $offset);
                        $Message_text .= "<br><br><span class=\"abbrev\">Текст сообщения слишком длинный. Нажмите [<a href=\"" . $thread[0] . "/\">Просмотр</a>] чтобы посмотреть его целиком.</span>";
                    }
                    else
                    {
                        $Message_text = $post[2];
                    }

					$thread_preview_code .= "<div>\n";
					$thread_preview_code .= "<span class=\"filetitle\">" . $Op_settings['THEME'] . "</span> <span class=\"postername\">" . $Op_settings['NAME'] . "</span> " . $post[1];
					
					if(isset($Op_settings['IMGNAME']))
					{
						$img_thumb_filename = $Op_settings['IMGNAME'] . 't.' . $Op_settings['IMGEXT'];
						$img_filename = $Op_settings['IMGNAME'] . '.' . $Op_settings['IMGEXT'];
						
						$thread_preview_code .= " <span class=\"filesize\">Файл: <a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>" .  $Op_settings['IMGSIZE'] . " Байт, " . $Op_settings['IMGSW'] . "x" . $Op_settings['IMGSH'] . "</em>)</span> <span class=\"reflink\"># <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$thread[0]/#$post[0]\">$post[0]</a></span> [<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$thread[0]/\">Ответить</a>] <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span>\n";
						$thread_preview_code .= "<br><a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\"><img src=\"$KOTOBA_DIR_PATH/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"" . $Op_settings['IMGTW'] . "\" heigth=\"" . $Op_settings['IMGTH'] . "\"></a>";
						$thread_preview_code .= "<blockquote>\n" . ($Message_text == "" ? "<br>" : $Message_text) . "</blockquote>\n";
					}
					else
					{
						$thread_preview_code .= " <span class=\"reflink\"># <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$thread[0]/#$post[0]\">" .  $post[0] . "</a></span> [<a href=\"" . $thread[0] . "/\">Ответить</a>] <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span>\n";
						$thread_preview_code .= "<br><blockquote>\n" . ($Message_text == "" ? "<br>" : $Message_text) . "</blockquote>\n";
                    }

					$thread_preview_code .= "<div>\n<span class=\"omittedposts\">" . (($POSTS_COUNT > $PREVIEW_REPLAYS_COUNT + 1) ? "Сообщений пропущено: " . ($POSTS_COUNT - ($PREVIEW_REPLAYS_COUNT + 1)) . ".</span>\n<br><br>" : "</span>\n");
					$thread_preview_code .= "";

					$last_post_number = $post[0];
					$post = mysql_fetch_array($posts, MYSQL_NUM);

					while ($post)
					{
						$Replay_settings = GetSettings('post', $post[3]);
                        
                        $Message_text = '';
                        $offset = 0;
                        $line = 1;

                        while($line <= $KOTOBA_LONGPOST_LINES && (($offset = strpos($post[2], "<br>", ($offset == 0 ? $offset : $offset + strlen("<br>")))) !== false))
                        {
                            $line++;
                        }
                        
                        if($line == $KOTOBA_LONGPOST_LINES + 1 && $offset !== false)
                        {
                            $Message_text = substr($post[2], 0, $offset);
                            $Message_text .= "<br><br><span class=\"abbrev\">Текст сообщения слишком длинный. Нажмите [<a href=\"" . $thread[0] . "/\">Просмотр</a>] чтобы посмотреть его целиком.</span>";
                        }
                        else
                        {
                            $Message_text = $post[2];
                        }
					
						$thread_preview_code .= "\n<table>\n";
						$thread_preview_code .= "<tr>\n\t<td class=\"reply\"><span class=\"filetitle\">" . $Replay_settings['THEME'] . "</span> <span class=\"postername\">" . $Replay_settings['NAME'] . "</span>  " . $post[1];
						
						if(isset($Replay_settings['IMGNAME']))
						{
							$img_thumb_filename = $Replay_settings['IMGNAME'] . 't.' . $Replay_settings['IMGEXT'];
							$img_filename = $Replay_settings['IMGNAME'] . '.' . $Replay_settings['IMGEXT'];

							$thread_preview_code .= " <span class=\"filesize\">Файл: <a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>" .  $Replay_settings['IMGSIZE'] . " Байт " . $Replay_settings['IMGSW'] . "x" . $Replay_settings['IMGSH'] . "</em>)</span> <span class=\"reflink\"># <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$thread[0]/#$post[0]\">" .  $post[0] . "</a></span> <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span>\n";
							$thread_preview_code .= "\t<br<a target=\"_blank\" href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/img/$img_filename\"><img src=\"$KOTOBA_DIR_PATH/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"" . $Replay_settings['IMGTW'] . "\" heigth=\"" . $Replay_settings['IMGTH'] . "\"></a>";
							$thread_preview_code .= "<blockquote>\n" . ($Message_text == "" ? "<br>" : $Message_text) . "</blockquote>\n\t</td>\n</tr>\n";
						}
						else
						{
							$thread_preview_code .= " <span class=\"reflink\"># <a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/$thread[0]/#$post[0]\">" .  $post[0] . "</a></span> <span class=\"delbtn\">[<a href=\"$KOTOBA_DIR_PATH/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span>\n";
							$thread_preview_code .= "<br><blockquote>\n" . ($Message_text == "" ? "<br>" : $Message_text) . "</blockquote>\n\t</td>\n</tr>\n";
						}

						$thread_preview_code .= "</table>\n";

						if(!isset($Replay_settings['SAGE']))
						{
							$last_post_number = $post[0];
						}
						
						$post = mysql_fetch_array($posts, MYSQL_NUM);
					}
					
					$thread_preview_code .= "</div>\n</div>\n<br clear=\"left\">\n<hr>\n\n";
			    }
			
				mysql_free_result($posts);
			}
			else
			{
				$PREVIEW .= "<span class=\"error\">При получении постов треда $THREAD_NUM для предпросмотра произошла ошибка. Причина: " . mysql_error() . '.</span>';
			}

			if($last_post_number)
			{
				$last_posts[$i++] = $last_post_number;
				$threads_preview[$last_post_number] = $thread_preview_code;
			}
			$thread_preview_code = '';
			
			$thread = mysql_fetch_array($threads, MYSQL_NUM);
		}
        
        rsort($last_posts);
        reset($last_posts);
        $number = current($last_posts);

        while($number)
        {
            $PREVIEW .= $threads_preview[$number];
            $number = next($last_posts);
        }
    }

	mysql_free_result($threads);
}
else
{
	$PREVIEW = '<span class="error">При получении номеров тредов произошла ошибка. Причина: ' . mysql_error() . '.</span>';
}

echo $HEAD . $MENU . $FORM . '<hr>' . $PREVIEW . $PAGES . $FOOTER;
?>