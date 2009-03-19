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
ini_set('session.gc_maxlifetime', 60 * 60 * 24);    // 1 день.
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

$HEAD = 
'<html>
<head>
	<title>Kotoba preview</title>
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
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/preview.stat', 'a')) == false)
        die($HEAD . '<span class="error">Ошибка. Неудалось открыть или создать файл статистики.</span>' . $FOOTER);

require 'events.php';

if(isset($_GET['b']))
{
    if(($BOARD_NAME = CheckFormat('board', $_GET['b'])) == false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_BOARD_BAD_FORMAT);

		die($HEAD . '<span class="error">Ошибка. Имя доски имеет неверный формат.</span>' . $FOOTER);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_BOARD_NOT_SPECIFED);

	die($HEAD . '<span class="error">Ошибка. Не задано имя доски.</span>' . $FOOTER);
}

if(isset($_GET['p']))
{
	if(($PAGE = CheckFormat('page', $_GET['p'])) == false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_PAGE_BAD_FORMAT);

		die($HEAD . '<span class="error">Ошибка. Номер страницы имеет неверный формат.</span>' . $FOOTER);
	}
}
else
{
	$PAGE = 1;
}

if(isset($_COOKIE['rempass']))
{
	if(($OPPOST_PASS = CheckFormat('pass', $_COOKIE['rempass'])) == false)
	{
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_PASS_BAD_FORMAT);
            
		die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет неверный формат.</span>' . $FOOTER);
	}
}
else
{
	$OPPOST_PASS = '';
}

require 'databaseconnect.php';

if(isset($_SESSION['isLoggedIn']))	// Зарегистрированный пользователь.
{
	if(($result = mysql_query('select `id`, `User Settings` from `users` where SID = \'' . session_id() . '\'')) !== false)
	{
		if(mysql_num_rows($result) > 0)
		{
			$user = mysql_fetch_array($result, MYSQL_ASSOC);
			$User_id = $user['id'];
			$User_Settings = GetSettings('user', $user['User Settings']);
			mysql_free_result($result);
		}
	}
	else
	{
		if(KOTOBA_ENABLE_STAT)
				kotoba_stat(sprintf(ERR_USER_DATA, mysql_error()));

		die($HEAD . '<span class="error">Ошибка. Невозможно получить данные пользователя. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
	}
}

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
		$BOARD_NUM = -1;
		$BOARDS_LIST = '';
		
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

$FORM =
'
<form action="' . KOTOBA_DIR_PATH . "/createthread.php\" method=\"post\" enctype=\"multipart/form-data\">
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

$result = mysql_query(
	'select p.`board`, count(p.`id`) `count`
	from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board`
	where (position(\'ARCHIVE:YES\' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null)
	group by p.`board`
	having p.`board` = ' . $BOARD_NUM);
$row = mysql_fetch_array($result, MYSQL_NUM);
$POST_COUNT = $row[1];
mysql_free_result($result);

$MENU = $BOARDS_LIST . "<br>\n<h4 align=center>βchan</h4>\n<br><center><b>/$BOARD_NAME/</b></center>\nПостлимит: $POST_COUNT/" . KOTOBA_POST_LIMIT . "<br>\nБамплимит: " . KOTOBA_BUMPLIMIT . "<hr>\n";

// Получение количества не утонувших тредов просматриваемой доски и постраничная разбивка.
if(($result = mysql_query(
	"select count(*) `count`
	from `threads`
	where `board` = $BOARD_NUM and (position('ARCHIVE:YES' in `Thread Settings`) = 0 or `Thread Settings` is null)")) !== false)
{
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
    $threards_count = $row['count'];
    mysql_free_result($result);
    $pages_count = ($threards_count / 10) + 1;	// По 10 тредов на странице.

    if($PAGE < 1 || $PAGE > $pages_count)
    {
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_PAGE_BAD_RANGE);

		die($HEAD . '<span class="error">Ошибка. Страница находится вне допустимого диапазона.</span>' . $FOOTER);
    }
    
    $threads_range = " limit " . (($PAGE - 1) * 10) . ", 10";	// 10 тредов начиная с ...
	$PAGES = "<br>";
	
	for($i = 1; $i <= $pages_count; $i++)
		if($i != $PAGE)
			$PAGES .= '(<a href="' . KOTOBA_DIR_PATH . "/$BOARD_NAME/" . (($i == 1) ? '' : "p$i/") . "\">" . ($i < 10 ? "0$i" : "$i") . '</a>) ';
		else
			$PAGES .= '(' . ($i < 10 ? "0$i" : "$i") . ') ';
}
else
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_THREADS_CALC_FALTURE, mysql_error()));

	die($HEAD . '<span class="error">Ошибка. Невозможно подсчитать количество тредов просматриваемой доски. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

$HEAD = 
"<html>
<head>
	<title>Kotoba - $BOARD_NAME</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"" . KOTOBA_DIR_PATH . '/kotoba.css">
</head>
<body>
';

// Получение номеров не утонувших тредов просматривоемой доски в заданном (в зависимости от страницы)
// диапазоне и отсортированных по убыванию номера последнего поста без сажи и не запощенного после бамплимита.
if(($threads = mysql_query(
	'select p.`thread` `id` ' .
	"from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` where t.`board` = $BOARD_NUM " .
	'and (position(\'ARCHIVE:YES\' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) ' .
	'and (position(\'SAGE:Y\' in p.`Post Settings`) = 0 or p.`Post Settings` is null) ' .
	'and (position(\'BLIMIT:Y\' in p.`Post Settings`) = 0 or p.`Post Settings` is null) ' .
	"group by p.`thread` order by max(p.`id`) desc $threads_range")) != false)
{
	if(mysql_num_rows($threads) > 0)	// На доске может и не быть тредов, как это бывает при создании новой доски.
	{
		$PREVIEW = '';
		$thread_preview_code = '';	// HTML код предпросмотра текущего треда.
		
		while (($thread = mysql_fetch_array($threads)) != false)
		{
			$PREVIEW_REPLAYS_COUNT = 6;	// Количество ответов в предпросмотре треда.
            $POSTS_COUNT = 0;			// Число постов в треде.
			$last_post_number = null;

			// Оп пост + $PREVIEW_REPLAYS_COUNT последних постов.
			$query = 
				"(select `id`, `Time`, `Text`, `Post Settings` 
					from `posts` where thread = $thread[id] and `board` = $BOARD_NUM order by `id` asc limit 1)
				union 
				(select `id`, `Time`, `Text`, `Post Settings` 
					from `posts` where thread = $thread[id] and `board` = $BOARD_NUM order by `id` desc limit $PREVIEW_REPLAYS_COUNT) order by `id` asc";
			
            // Получение постов треда для предпросмотра.
            if(($posts = mysql_query($query)) != false)
			{
				if(mysql_num_rows($posts) > 0)
				{
                    if(($result = mysql_query("select count(`id`) `count` from `posts` where `thread` = $thread[id] and `board` = $BOARD_NUM")) != false)
                    {
                        $row = mysql_fetch_array($result, MYSQL_ASSOC);
                        $POSTS_COUNT = $row['count'];
						mysql_free_result($result);
                    }
                    else
                    {
						if(KOTOBA_ENABLE_STAT)
							kotoba_stat(sprintf(ERR_THREAD_POSTS_CALC, $thread['id'], mysql_error()));

						die($HEAD . "<span class=\"error\">Ошибка. Невозможно подсчитать количество постов треда $thread[id] для предпросмотра. Причина: " . mysql_error() . '.</span>' . $FOOTER);
                    }

					// Код ОП поста.
					$post = mysql_fetch_array($posts, MYSQL_BOTH);
					$Op_settings = GetSettings('post', $post['Post Settings']);

					// Урезание длинного текста.
                    $Message_text = $post['Text'];
                    
//					if(($count = preg_match('/((?:.+?(?:<br>|<\/ul>|<\/ol>|<\/li>|$)){1,10})/', $post['Text'], $result)) === false)
//					{
//						$Message_text = $post['Text'];
//					}
//					else
//					{
//						if($count == 0)
//						{
//							$Message_text = $post['Text'];
//						}
//						else
//						{
//							$Message_text = preg_replace('/(<ul><li>.+(?!<ul>|<ol>)<\/li>$)/', '$1</ul>', $result[1]);
//							$Message_text = preg_replace('/(<ol><li>.+(?!<ul>|<ol>)<\/li>$)/', '$1</ol>', $Message_text);
//
//							if(strlen($post['Text']) > strlen($Message_text))	// Если урезали.
//								$Message_text .= "<br><span class=\"abbrev\">Текст сообщения слишком длинный. Нажмите [<a href=\"$thread[id]/\">Просмотр</a>] чтобы посмотреть его целиком.</span>";
//						}
//					}

					$thread_preview_code .= "\n<div>\n";
					$thread_preview_code .= "<span class=\"filetitle\">$Op_settings[THEME]</span> <span class=\"postername\">$Op_settings[NAME]</span> $post[Time]";
					
					if(isset($Op_settings['IMGNAME']))	//TODO Оу. Какой интересный if
					{
						$img_thumb_filename = $Op_settings['IMGNAME'] . 't.' . $Op_settings['IMGEXT'];
						$img_filename = $Op_settings['IMGNAME'] . '.' . $Op_settings['IMGEXT'];
						
						$thread_preview_code .= " <span class=\"filesize\">Файл: <a target=\"_blank\" href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>" .  $Op_settings['IMGSIZE'] . " Байт, " . $Op_settings['IMGSW'] . "x" . $Op_settings['IMGSH'] . "</em>)</span> <span class=\"reflink\"># <a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$thread[0]/#$post[0]\">$post[0]</a></span> [<a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$thread[0]/\">Ответить</a>] <span class=\"delbtn\">[<a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span> " . ((isset($_SESSION['isLoggedIn']) && ($User_Settings['ADMIN'] === 'Y')) ? $Op_settings['IP'] : '') . "<br>\n";
						$thread_preview_code .= "<a target=\"_blank\" href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename\"><img src=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"" . $Op_settings['IMGTW'] . "\" heigth=\"" . $Op_settings['IMGTH'] . "\"></a>";
						$thread_preview_code .= "\n<blockquote>\n" . ($Message_text == "" ? "<br>" : $Message_text) . "\n</blockquote>\n";
					}
					else
					{
						$thread_preview_code .= " <span class=\"reflink\"># <a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$thread[0]/#$post[0]\">" .  $post[0] . "</a></span> [<a href=\"" . $thread[0] . "/\">Ответить</a>] <span class=\"delbtn\">[<a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[0]/\" title=\"Удалить\">×</a>]</span> " . ((isset($_SESSION['isLoggedIn']) && ($User_Settings['ADMIN'] === 'Y')) ? $Op_settings['IP'] : '') . "\n";
						$thread_preview_code .= "<br>\n<blockquote>\n" . ($Message_text == "" ? "<br>" : $Message_text) . "\n</blockquote>\n";
                    }

					$thread_preview_code .= "<div>\n<span class=\"omittedposts\">" . (($POSTS_COUNT > $PREVIEW_REPLAYS_COUNT + 1) ? "Сообщений пропущено: " . ($POSTS_COUNT - ($PREVIEW_REPLAYS_COUNT + 1)) . '.' . (($POSTS_COUNT > KOTOBA_BUMPLIMIT) ? ' Тред достиг бамплимта.' : '') . "</span>\n<br><br>" : "</span>\n");
					
					// Код остальных постов треда.
					while (($post = mysql_fetch_array($posts, MYSQL_BOTH)) !== false)
					{
						$Replay_settings = GetSettings('post', $post['Post Settings']);
                        $Message_text = $post['Text'];
						
//                        if(($count = preg_match('/((?:.+?(?:<br>|<\/ul>|<\/ol>|<\/li>|$)){1,10})/', $post['Text'], $result)) === false)
//						{
//							$Message_text = $post['Text'];
//						}
//						else
//						{
//							if($count == 0)
//							{
//								$Message_text = $post['Text'];
//							}
//							else
//							{
//								$Message_text = preg_replace('/(<ul><li>.+(?!<ul>|<ol>)<\/li>$)/', '$1</ul>', $result[1]);
//								$Message_text = preg_replace('/(<ol><li>.+(?!<ul>|<ol>)<\/li>$)/', '$1</ol>', $Message_text);
//
//								if(strlen($post['Text']) > strlen($Message_text))	// Если урезали.
//									$Message_text .= "<br><span class=\"abbrev\">Текст сообщения слишком длинный. Нажмите [<a href=\"$thread[id]/\">Просмотр</a>] чтобы посмотреть его целиком.</span>";
//							}
//						}

						$thread_preview_code .= "\n<table>\n";
						$thread_preview_code .= "<tr>\n\t<td class=\"reply\"><span class=\"filetitle\">$Replay_settings[THEME]</span> <span class=\"postername\">$Replay_settings[NAME]</span> $post[Time] <span class=\"reflink\"># <a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/$thread[0]/#$post[id]\">" .  $post[id] . "</a></span> <span class=\"delbtn\">[<a href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[id]/\" title=\"Удалить\">×</a>]</span> " . ((isset($_SESSION['isLoggedIn']) && ($User_Settings['ADMIN'] === 'Y')) ? $Replay_settings['IP'] : '') . "<br>\n";
						
						if(isset($Replay_settings['IMGNAME']))
						{
							$img_thumb_filename = $Replay_settings['IMGNAME'] . 't.' . $Replay_settings['IMGEXT'];
							$img_filename = $Replay_settings['IMGNAME'] . '.' . $Replay_settings['IMGEXT'];

							$thread_preview_code .= "<span class=\"filesize\">Файл: <a target=\"_blank\" href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename\">$img_filename</a> -(<em>" .  $Replay_settings['IMGSIZE'] . " Байт " . $Replay_settings['IMGSW'] . "x" . $Replay_settings['IMGSH'] . "</em>)</span>\n";
							$thread_preview_code .= "\t<br<a target=\"_blank\" href=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename\"><img src=\"" . KOTOBA_DIR_PATH . "/$BOARD_NAME/thumb/$img_thumb_filename\" class=\"thumb\" width=\"" . $Replay_settings['IMGTW'] . "\" heigth=\"" . $Replay_settings['IMGTH'] . "\"></a>";
						}
						
						$thread_preview_code .= "<blockquote>\n" . ($Message_text == "" ? "<br>" : $Message_text) . "</blockquote>\n\t</td>\n</tr>\n";
						$thread_preview_code .= "</table>\n";
					} // Следующий пост.
					
					$thread_preview_code .= "</div>\n</div>\n<br clear=\"left\">\n<hr>\n\n";
			    }
				else
				{
					if(KOTOBA_ENABLE_STAT)
						kotoba_stat(sprintf(ERR_THREAD_NO_POSTS, $thread['id']));

					die($HEAD . "<span class=\"error\">Ошибка. В треде $thread[id] нет ни одного поста.</span>" . $FOOTER);
                }
			
				mysql_free_result($posts);
			}
			else
			{
				if(KOTOBA_ENABLE_STAT)
					kotoba_stat(sprintf(ERR_GET_THREAD_POSTS, $thread['id'], $BOARD_NAME, mysql_error()));

				die($HEAD . "<span class=\"error\">Ошибка. Невозможно получить посты для предпросмотра треда $thread[id] доски $BOARD_NAME. Причина: " . mysql_error() . '.</span>' . $FOOTER);
			}

			$PREVIEW .= $thread_preview_code;
			$thread_preview_code = '';
		}// Следующий тред.
    }

	mysql_free_result($threads);
}
else
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_THREADS_NUM, mysql_error()));

	die($HEAD . '<span class="error">Ошибка. Невозможно получить номера тредов. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

echo $HEAD . $MENU . $FORM . '<hr>' . $PREVIEW . $PAGES . $FOOTER;
?>
<?php
/*
 * Выводит сообщение $errmsg в файл статистики $stat_file.
 */
function kotoba_stat($errmsg)
{
    global $stat_file;
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
	fclose($stat_file);
}
?>