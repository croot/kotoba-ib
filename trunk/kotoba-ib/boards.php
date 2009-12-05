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
// Скрипт просмотра досок.
require 'config.php';
require 'modules/errors.php';
require 'modules/lang/' . Config::LANGUAGE . '/errors.php';
require 'modules/db.php';
require 'modules/cache.php';
require 'modules/common.php';
require 'modules/popdown_handlers.php';
require 'modules/events.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	header("Cache-Control: private");						// Fix for Firefox.
// Проверка входных параметров.
	$board_name = boards_check_name($_GET['b']);
	$page = 1;
	if(isset($_GET['p']))
		$page = check_page($_GET['p']);
	$rempass = !isset($_SESSION['rempass']) || $_SESSION['rempass'] === null
		? '' : $_SESSION['rempass'];
	$boards = boards_get_all_view($_SESSION['user']);
	$board = null;
	$found = false;
	foreach($boards as $b)
		if($b['name'] == $board_name)
		{
			$found = true;
			$board = $b;
			break;
		}
	if(!$found)
		throw new NodataException(sprintf(NodataException::$messages['BOARD_NOT_FOUND'],
				$board_name));
	foreach(popdown_handlers_get_all() as $popdown_handler)
		if($board['popdown_handler'] == $popdown_handler['id'])
		{
			$popdown_handler['name']();
			break;
		}
	$threads_count = threads_get_view_threadscount($_SESSION['user'],
		$board['id']);
	$page_max = ($threads_count % $_SESSION['threads_per_page'] == 0
		? (int)($threads_count / $_SESSION['threads_per_page'])
		: (int)($threads_count / $_SESSION['threads_per_page']) + 1);
	if($page_max == 0)
		$page_max = 1;		// Important for empty boards.
	if($page > $page_max)
		$page = $page_max;	// TODO May be throw exception here?
	$is_admin = false;
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$is_admin = true;
// Получние нитей, сообщений и другой необходимой информации.
	$threads = threads_get_board_view($board['id'], $page, $_SESSION['user'],
		$_SESSION['threads_per_page']);
	$posts = posts_get_threads_view($threads, $_SESSION['user'],
		$_SESSION['posts_per_thread']);
	$posts_uploads = posts_uploads_get_posts($posts);
	$uploads = uploads_get_posts($posts);
	$hidden_threads = hidden_threads_get_board($board['id'], $_SESSION['user']);
	$upload_types = upload_types_get_board($board['id']);
	// Формирование вывода.
	$smarty->assign('boards', $boards);
	$smarty->assign('rempass', $rempass);
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
	$smarty->assign('with_files', $board['with_files']);
	$smarty->assign('force_anonymous', $board['force_anonymous']);
	event_daynight($smarty);	// EVENT HERE! (not default kotoba function)
	$boards_html = $smarty->fetch('board_header.tpl');
	$boards_thread_html = '';
	$boards_posts_html = '';
	$recived_posts_count = 0;
	foreach($threads as $t)
	{
		$smarty->assign('thread_num', $t['original_post']);
		foreach($posts as $p)
			// Сообщение принадлежит текущей нити.
			if($t['id'] == $p['thread'])
			{
				$recived_posts_count++;
				// У некоторых старых сообщений нет имени отправителя.
				if(!$board['force_anonymous'] && $board['default_name']
					&& !$p['name'])
				{
					$p['name'] = $board['default_name'];
				}
				if($t['original_post'] == $p['number'])
				{
					// Оригинальное сообщение.
					$smarty->assign('original_with_files', false);
					$smarty->assign('original_theme', $p['subject']);
					$smarty->assign('original_name', $p['name']);
					$smarty->assign('original_time', $p['date_time']);
					$smarty->assign('original_num', $p['number']);
					$smarty->assign('original_text', posts_corp_text($p['text'], $_SESSION['lines_per_post'], $is_cutted));
					$smarty->assign('original_text_cutted', $is_cutted);
					// В данной версии 1 сообщение = 1 файл
					foreach($posts_uploads as $pu)
						if($p['id'] == $pu['post'])
						{
							// В данной версии 1 сообщение = 1 файл
							foreach($uploads as $u)
								if($pu['upload'] == $u['id'])
								{
									$smarty->assign('original_with_files', true);
									$smarty->assign('original_file_link', Config::DIR_PATH . "/{$board['name']}/img/" . basename($u['file_name']));
									$smarty->assign('original_file_name', basename($u['file_name']));
									$smarty->assign('original_file_size', $u['size']);
									$smarty->assign('original_file_width', $u['file_w']);
									$smarty->assign('original_file_heigth', $u['file_h']);
									$smarty->assign('original_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/" . basename($u['thumbnail_name']));
									$smarty->assign('original_file_thumbnail_width', $u['thumbnail_w']);
									$smarty->assign('original_file_thumbnail_heigth', $u['thumbnail_h']);
								}
						}
					$original_ip = long2ip($p['ip']);
					$original_id = $p['id'];
				}
				else
				{
					$smarty->assign('simple_with_files', false);
					$smarty->assign('simple_theme', $p['subject']);
					$smarty->assign('simple_name', $p['name']);
					$smarty->assign('simple_time', $p['date_time']);
					$smarty->assign('simple_num', $p['number']);
					$smarty->assign('simple_text', posts_corp_text($p['text'], $_SESSION['lines_per_post'], $is_cutted));
					$smarty->assign('simple_text_cutted', $is_cutted);
					foreach($posts_uploads as $pu)
						if($p['id'] == $pu['post'])
						{
							foreach($uploads as $u)
								if($pu['upload'] == $u['id'])
								{
									$smarty->assign('simple_with_files', true);
									$smarty->assign('simple_file_link', Config::DIR_PATH . "/{$board['name']}/img/" . basename($u['file_name']));
									$smarty->assign('simple_file_name', basename($u['file_name']));
									$smarty->assign('simple_file_size', $u['size']);
									$smarty->assign('simple_file_width', $u['file_w']);
									$smarty->assign('simple_file_heigth', $u['file_h']);
									$smarty->assign('simple_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/" . basename($u['thumbnail_name']));
									$smarty->assign('simple_file_thumbnail_width', $u['thumbnail_w']);
									$smarty->assign('simple_file_thumbnail_heigth', $u['thumbnail_h']);
								}
						}
					$boards_posts_html .= $smarty->fetch('post_simple.tpl');
					if($is_admin)
					{
						$smarty->assign('post_id',  $p['id']);
						$smarty->assign('ip', long2ip($p['ip']));
						$boards_posts_html .= $smarty->fetch('mod_mini_panel.tpl');
					}
				}// Оригинальное или простое сообщение.
			}// Сообщение принадлежит текущей нити.
		$smarty->assign('sticky', $t['sticky']);
		$smarty->assign('skipped', ($t['posts_count'] - $recived_posts_count));
		$boards_thread_html .= $smarty->fetch('board_thread_header.tpl');
		if($is_admin)
		{
			$smarty->assign('post_id',  $original_id);
			$smarty->assign('ip', $original_ip);
			$boards_thread_html .= $smarty->fetch('mod_mini_panel.tpl');
		}
		$boards_thread_html .= $boards_posts_html;
		$boards_thread_html .= $smarty->fetch('board_thread_footer.tpl');
		$boards_html .= $boards_thread_html;
		$boards_thread_html = '';
		$boards_posts_html = '';
		$recived_posts_count = 0;
	}
	$smarty->assign('hidden_threads', $hidden_threads);
	$boards_html .= $smarty->fetch('board_footer.tpl');
	DataExchange::releaseResources();
	echo $boards_html;
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>