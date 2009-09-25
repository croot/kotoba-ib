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
if(isset($_GET['p']))
{
	if(($page = check_format('page', $_GET['p'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['PAGE_NUM'], $smarty,
				basename(__FILE__) . ' ' . __LINE__);
	}
}
else
	$page = 1;
$removepass = !isset($_SESSION['rempass']) || $_SESSION['rempass'] == null ? ''
				: $_SESSION['rempass'];
$boards = db_boards_get_preview($_SESSION['user'], $link, $smarty);
$categories = db_categories_get($link, $smarty);
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
	kotoba_error(sprintf(Errmsgs::$messages['BOARD_NOT_FOUND'], $board_name),
		$smarty,
		basename(__FILE__) . ' ' . __LINE__, $link);
$page_max = ($board['threads_count'] % $_SESSION['threads_per_page'] == 0
	? (int)($board['threads_count'] / $_SESSION['threads_per_page'])
	: (int)($board['threads_count'] / $_SESSION['threads_per_page']) + 1);
/*
 * Получние нитей, сообщений и другой необходимой информации.
 */
$threads = db_threads_get_preview($board['id'], $page, $_SESSION['user'],
	$_SESSION['threads_per_page'], $link, $smarty);
$posts = db_posts_get_preview($threads, $_SESSION['user'],
	$_SESSION['posts_per_thread'], $link, $smarty);
$posts_uploads = db_posts_uploads_get_all($posts, $link, $smarty);
$uploads = db_uploads_get_all($posts, $link, $smarty);
$hidden_threads = db_hidden_threads_get_all($board['id'], $_SESSION['user'],
	$link, $smarty);
$upload_types = db_upload_types_get_preview($board['id'], $link, $smarty);
/*
 * Формирование вывода.
 */
$smarty->assign('boards', $boards);
$smarty->assign('rempass', $removepass);
$smarty->assign('board_name', $board['name']);
$smarty->assign('board_title', $board['title']);
$smarty->assign('upload_types', $upload_types);
$smarty->assign('is_guest', $_SESSION['user'] == 1 ? true : false);
$smarty->assign('bump_limit', $board['bump_limit']);
$pages = array();
for($i = 1; $i <= $page_max; $i++)
	array_push($pages, $i);
$smarty->assign('pages', $pages);
$smarty->assign('page', $page);
$preview_html = $smarty->fetch('preview_header.tpl');
$preview_thread_html = '';
$preview_posts_html = '';
$recived_posts_count = 0;
foreach($threads as $t)
{
	$smarty->assign('thread_id', $t['id']);
	foreach($posts as $p)
		if($t['id'] == $p['thread'])
		{
			$recived_posts_count++;
			/* Оригинальное сообщение. */
			if($t['original_post'] == $p['number'])
			{
				$smarty->assign('with_image', false);
				$smarty->assign('original_theme', $p['subject']);
				$smarty->assign('original_name', $p['name']);
				$smarty->assign('original_time', $p['date_time']);
				$smarty->assign('original_num', $p['number']);
				$smarty->assign('original_text', preview_message($p['text'], $_SESSION['lines_per_post'], $is_cutted));
				$smarty->assign('original_text_cutted', $is_cutted);
				/* В данной версии 1 сообщение = 1 файл */
				foreach($posts_uploads as $pu)
					if($p['id'] == $pu['post'])
					{
						/* В данной версии 1 сообщение = 1 файл */
						foreach($uploads as $u)
							if($pu['upload'] == $u['id'])
							{
								$smarty->assign('with_image', true);
								$smarty->assign('original_file_link', Config::DIR_PATH . "/{$board['name']}/img/{$u['file_name']}");
								$smarty->assign('original_file_name', $u['file_name']);
								$smarty->assign('original_file_size', $u['size']);
								$smarty->assign('original_file_width', $u['file_w']);
								$smarty->assign('original_file_heigth', $u['file_h']);
								$smarty->assign('original_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/{$u['thumbnail_name']}");
								$smarty->assign('original_file_thumbnail_width', $u['thumbnail_w']);
								$smarty->assign('original_file_thumbnail_heigth', $u['thumbnail_h']);
							}
					}
			}
			else
			{
				$smarty->assign('with_image', false);
				$smarty->assign('simple_theme', $p['subject']);
				$smarty->assign('simple_name', $p['name']);
				$smarty->assign('simple_time', $p['date_time']);
				$smarty->assign('simple_num', $p['number']);
				$smarty->assign('simple_text', preview_message($p['text'], $_SESSION['lines_per_post'], $is_cutted));
				$smarty->assign('simple_text_cutted', $is_cutted);
				foreach($posts_uploads as $pu)
					if($p['id'] == $pu['post'])
					{
						foreach($uploads as $u)
							if($pu['upload'] == $u['id'])
							{
								$smarty->assign('with_image', true);
								$smarty->assign('simple_file_link', Config::DIR_PATH . "/{$board['name']}/img/{$u['file_name']}");
								$smarty->assign('simple_file_name', $u['file_name']);
								$smarty->assign('simple_file_size', $u['size']);
								$smarty->assign('simple_file_width', $u['file_w']);
								$smarty->assign('simple_file_heigth', $u['file_h']);
								$smarty->assign('simple_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/{$u['thumbnail_name']}");
								$smarty->assign('simple_file_thumbnail_width', $u['thumbnail_w']);
								$smarty->assign('simple_file_thumbnail_heigth', $u['thumbnail_h']);
							}
					}
				$preview_posts_html .= $smarty->fetch('simple_post.tpl');
			}
		}
		$smarty->assign('skipped', $t['posts_count'] - $recived_posts_count);
		/* TODO Потенциальная проблема с with_image */
		$preview_thread_html .= $smarty->fetch('preview_thread_header.tpl');
		$preview_thread_html .= $preview_posts_html;
		$preview_thread_html .= $smarty->fetch('preview_thread_footer.tpl');
		$preview_html .= $preview_thread_html;
		$preview_thread_html = '';
		$preview_posts_html = '';
		$recived_posts_count = 0;
}
$smarty->assign('hidden_threads', $hidden_threads);
$preview_html .= $smarty->fetch('preview_footer.tpl');
die($preview_html);
?>
