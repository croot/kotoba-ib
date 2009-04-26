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

require 'config.php';
require 'common.php';

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH  . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);	// Rus: 1 день. En: 1 day.
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();
header("Cache-Control: private");

if(KOTOBA_ENABLE_STAT)  // Rus: Включаем сбор статистики. En: Enable statistic system.
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/threads.stat', 'a')) === false)
        die($HEAD . '<span class="error">Ошибка. Не удалось открыть или создать файл статистики.</span>' . $FOOTER);

require 'events.php';

// Rus: Проверка имени доски.
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

// Rus: Проверка номер треда.
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

// Rus: Проверка пароля удаления сообщений.
if(isset($_COOKIE['rempass']))
{
	if(($REPLY_PASS = CheckFormat('pass', $_COOKIE['rempass'])) === false)
	{
        if(KOTOBA_ENABLE_STAT)
            kotoba_stat(ERR_PASS_BAD_FORMAT);

		die($HEAD . '<span class="error">Ошибка. Пароль для удаления имеет не верный формат.</span>' . $FOOTER);
	}
}
else
{
    $REPLY_PASS = '';
}

require 'databaseconnect.php';

$smarty = new SmartyKotobaSetup();
$smarty->assign('page_title', "Kotoba - $BOARD_NAME/$THREAD_NUM");
$smarty->assign('REPLY_PASS', $REPLY_PASS); // TODO Не подходящее название переменной для удаления.
$smarty->assign('BOARD_NAME', $BOARD_NAME);
$smarty->assign('THREAD_NUM', $THREAD_NUM);

$boards_list = array();
$BOARD_NUM = -1;

// Rus: Получение списка досок и проверка существут ли доска с заданным именем.
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

            //$BOARDS_LIST .= '/<a href="' . KOTOBA_DIR_PATH . "/$row[Name]/\">$row[Name]</a>/ ";
            $boards_list[] = $row['Name'];
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

$smarty->assign('board_list', $boards_list);
$smarty->assign('thread_location', "/$BOARD_NAME/$THREAD_NUM/");

// Rus: Проверка существования треда $THREAD_NUM на доске с именем $BOARD_NAME.
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

// Rus: Получение постов просматриваемого треда.
$query = "select `id`, `Time`, `Text`, `Post Settings` from `posts` where `thread` = $THREAD_NUM and `board` = $BOARD_NUM order by `id` asc";

if(($posts = mysql_query($query)))
{
	if(mysql_num_rows($posts) > 0)
	{
		$post = mysql_fetch_array($posts, MYSQL_ASSOC);
		$Op_settings = get_settings('post', $post['Post Settings']);

        $smarty->assign('original_theme', $Op_settings['THEME']);
        $smarty->assign('original_name', $Op_settings['NAME']);
        $smarty->assign('original_time', $post['Time']);
        $smarty->assign('original_id', $post['id']);
        $smarty->assign('original_link', KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/#$post[id]");
        $smarty->assign('original_remove_link', KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[id]/");
        $smarty->assign('original_text', ($post['Text'] == "" ? "<br>" : $post['Text']));

		if(isset($Op_settings['THEME']) && $Op_settings['THEME'] != '')
            $smarty->assign('page_title', "Kotoba - $BOARD_NAME/$THREAD_NUM - " . $Op_settings['THEME']);

		if(isset($Op_settings['IMGNAME']))  // Rus: С картинкой. En: With image.
		{
			$img_thumb_filename = $Op_settings['IMGNAME'] . 't.' . $Op_settings['IMGEXT'];
			$img_filename = $Op_settings['IMGNAME'] . '.' . $Op_settings['ORIGIMGEXT'];

            $smarty->assign('with_image', true);
            $smarty->assign('original_file_link', KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename");
            $smarty->assign('original_file_name', $img_filename);
            $smarty->assign('original_file_size', $Op_settings['IMGSIZE']);
            $smarty->assign('original_file_width', $Op_settings['IMGSW']);
            $smarty->assign('original_file_heigth', $Op_settings['IMGSW']);
            $smarty->assign('original_file_thumbnail_link', KOTOBA_DIR_PATH . "/$BOARD_NAME/thumb/$img_thumb_filename");
            $smarty->assign('original_file_thumbnail_width', $Op_settings['IMGTW']);
            $smarty->assign('original_file_thumbnail_heigth', $Op_settings['IMGTH']);
		}
		else
		{
            $smarty->assign('with_image', false);
        }

        $thread = array();

		while (($post = mysql_fetch_array($posts, MYSQL_ASSOC)) !== false)
		{
            $post_data = array();
			$Replay_settings = get_settings('post', $post['Post Settings']);

            $post_data['simple_theme'] = $Replay_settings['THEME'];
            $post_data['simple_name'] = $Replay_settings['NAME'];
            $post_data['simple_time'] = $post['Time'];
            $post_data['simple_id'] = $post['id'];
            $post_data['simple_link'] = KOTOBA_DIR_PATH . "/$BOARD_NAME/$THREAD_NUM/#$post[id]";
            $post_data['simple_remove_link'] = KOTOBA_DIR_PATH . "/$BOARD_NAME/r$post[id]/";
            $post_data['simple_text'] = $post['Text'] == '' ? '<br>' : $post['Text'];

			if(isset($Replay_settings['IMGNAME']))
			{
				$img_thumb_filename = $Replay_settings['IMGNAME'] . 't.' . $Replay_settings['IMGEXT'];
				$img_filename = $Replay_settings['IMGNAME'] . '.' . $Replay_settings['ORIGIMGEXT'];

                $post_data['with_image'] = true;
                $post_data['simple_file_link'] = KOTOBA_DIR_PATH . "/$BOARD_NAME/img/$img_filename";
                $post_data['simple_file_name'] = $img_filename;
                $post_data['simple_file_size'] = $Replay_settings['IMGSIZE'];
                $post_data['simple_file_width'] = $Replay_settings['IMGSW'];
                $post_data['simple_file_heigth'] = $Replay_settings['IMGSH'];
                $post_data['simple_file_thumbnail_link'] = KOTOBA_DIR_PATH . "/$BOARD_NAME/thumb/$img_thumb_filename";
                $post_data['simple_file_thumbnail_width'] = $Replay_settings['IMGTW'];
                $post_data['simple_file_thumbnail_heigth'] = $Replay_settings['IMGTH'];
			}
			else
			{
                $post_data['with_image'] = false;
            }

            array_push($thread, $post_data);
        }

        $smarty->assign('thread', $thread);
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

$smarty->display('threads.tpl');
?>
<?php
/*
 * Rus: Выводит сообщение $errmsg в файл статистики $stat_file.
 */
function kotoba_stat($errmsg)
{
    global $stat_file;
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
	fclose($stat_file);
}
?>
