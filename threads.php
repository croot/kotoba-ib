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
/*
 * Скрипт просмотра нити.
 */

require 'kwrapper.php';
kotoba_setup($link, $smarty);
header("Cache-Control: private");
/*
 * Проверка входных параметров.
 */
if(isset($_GET['b']))
{
    if(($board_name = check_format('board', $_GET['b'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BOARD_NAME'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
}
else
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['BOARD_NOT_SPECIFED'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
}
if(isset($_GET['t']))
{
    if(($thread_id = check_format('id', intval($_GET['t']))) === false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['THREAD_ID'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
}
else
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['THREAD_NOT_SPECIFED'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
}
$rempass = !isset($_SESSION['rempass']) || $_SESSION['rempass'] == null ? ''
				: $_SESSION['rempass'];
try
{
	$boards = db_boards_get_view($_SESSION['user'], $link);
	$categories = db_categories_get($link);
	foreach($categories as $category)	// TODO А что будет если нет категорий?
		foreach($boards as &$b)
			if($b['category'] == $category['id'])
				/* Заменим id категории на её имя. */
				$b['category'] = $category['name'];
	unset($b);	// Иначе затрём при следующем цикле последнюю доску с массиве.
	/* Проверим, существует ли запрашиваемая для предпросмотра доска. */
	$found = false;
	foreach($boards as $b)
		if($b['name'] == $board_name)
		{
			$found = true;
			$board = $b;
			break;
		}
	if(! $found)
		throw new Exception(sprintf(Errmsgs::$messages['BOARD_NOT_FOUND'],
				$board_name));
	/*
	 * Получение данных.
	 */
	$thread = db_threads_get_specifed_view($thread_id, $_SESSION['user'], $link);
	if($thread['archived'] != null)
	{
		/* Нить была сброшена в архив. */
		mysqli_close($link);
		header('Location: ' . Config::DIR_PATH
			. "/{$board['name']}/arch/{$thread['id']}/{$thread['id']}.html");
		exit;
	}
	$thread_mod = db_threads_get_specifed_moderate($thread['id'],
		$_SESSION['user'], $link);
	$posts = db_posts_get_view(array($thread), $_SESSION['user'],
		$thread['posts_count'], $link);
	$posts_uploads = db_posts_uploads_get_all($posts, $link);
	$uploads = db_uploads_get_all($posts, $link);
	$hidden_threads = db_hidden_threads_get_all($board['id'], $_SESSION['user'],
		$link);
	$upload_types = db_upload_types_get($board['id'], $link);
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	if(isset($link) && $link instanceof MySQLi)
		mysqli_close($link);
	die($smarty->fetch('error.tpl'));
}
/*
 * Формирование вывода.
 */
$smarty->assign('boards', $boards);
$smarty->assign('rempass', $rempass);
$smarty->assign('board_name', $board['name']);
$smarty->assign('original_post', $thread['original_post']);
$smarty->assign('thread_id', $thread['id']);
$smarty->assign('bump_limit', $thread['bump_limit']);
$smarty->assign('posts_count', $thread['posts_count']);
$smarty->assign('is_guest', $_SESSION['user'] == 1 ? true : false);
$smarty->assign('upload_types', $upload_types);
if($thread_mod != null)
	$smarty->assign('thread_mod', array($thread_mod));
$view_html = $smarty->fetch('view_header.tpl');
$view_thread_html = '';
$view_posts_html = '';
foreach($posts as $p)
	/* Оригинальное сообщение. */
	if($thread['original_post'] == $p['number'])
	{
		$smarty->assign('with_image', false);
		$smarty->assign('original_theme', $p['subject']);
		$smarty->assign('original_name', $p['name']);
		$smarty->assign('original_time', $p['date_time']);
		$smarty->assign('original_id', $p['id']);
		$smarty->assign('original_text', $p['text']);
		/* В данной версии 1 сообщение = 1 файл */
		foreach($posts_uploads as $pu)
			if($p['id'] == $pu['post'])
			{
				/* В данной версии 1 сообщение = 1 файл */
				foreach($uploads as $u)
					if($pu['upload'] == $u['id'])
					{
						$smarty->assign('with_image', true);
						$smarty->assign('original_file_link', Config::DIR_PATH . "/{$u['file_name']}");
						$smarty->assign('original_file_name', $u['file_name']);
						$smarty->assign('original_file_size', $u['size']);
						$smarty->assign('original_file_width', $u['file_w']);
						$smarty->assign('original_file_heigth', $u['file_h']);
						$smarty->assign('original_file_thumbnail_link', Config::DIR_PATH . "/{$u['thumbnail_name']}");
						$smarty->assign('original_file_thumbnail_width', $u['thumbnail_w']);
						$smarty->assign('original_file_thumbnail_heigth', $u['thumbnail_h']);
					}
			}
		$view_thread_html = $smarty->fetch('view_thread_header.tpl');
	}
	else
	{
		$smarty->assign('with_image', false);
		$smarty->assign('simple_theme', $p['subject']);
		$smarty->assign('simple_name', $p['name']);
		$smarty->assign('simple_time', $p['date_time']);
		$smarty->assign('simple_num', $p['number']);
		$smarty->assign('simple_text', $p['text']);
		foreach($posts_uploads as $pu)
			if($p['id'] == $pu['post'])
			{
				foreach($uploads as $u)
					if($pu['upload'] == $u['id'])
					{
						$smarty->assign('with_image', true);
						$smarty->assign('simple_file_link', Config::DIR_PATH . "/{$u['file_name']}");
						$smarty->assign('simple_file_name', $u['file_name']);
						$smarty->assign('simple_file_size', $u['size']);
						$smarty->assign('simple_file_width', $u['file_w']);
						$smarty->assign('simple_file_heigth', $u['file_h']);
						$smarty->assign('simple_file_thumbnail_link', Config::DIR_PATH . "/{$u['thumbnail_name']}");
						$smarty->assign('simple_file_thumbnail_width', $u['thumbnail_w']);
						$smarty->assign('simple_file_thumbnail_heigth', $u['thumbnail_h']);
					}
			}
		$view_posts_html .= $smarty->fetch('simple_post.tpl');
	}
$view_html .= $view_thread_html . $view_posts_html;
$smarty->assign('hidden_threads', $hidden_threads);
$view_html .= $smarty->fetch('view_footer.tpl');
die($view_html);
?>