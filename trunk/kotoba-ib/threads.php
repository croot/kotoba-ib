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
// Скрипт просмотра нити.
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
	$thread_num = threads_check_number($_GET['t']);
	$rempass = !isset($_SESSION['rempass']) || $_SESSION['rempass'] == null
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
	$is_admin = false;
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$is_admin = true;
	// Получение данных.
	$thread = threads_get_specifed_view($board['id'], $thread_num, $_SESSION['user']);
	if($thread['archived'])
	{
		// TODO На самом деле заархивированные нити не будут существовать в базе всё время.
		// Нить была сброшена в архив.
		DataExchange::releaseResources();
		header('Location: ' . Config::DIR_PATH . "/{$board['name']}/arch/"
			. "{$thread['original_post']}/{$thread['original_post']}.html");
		exit;
	}
	$is_moderatable = threads_check_specifed_moderate($thread['id'],
		$_SESSION['user']);
	$posts = posts_get_threads_view(array($thread), $_SESSION['user'],
		$thread['posts_count']);
	$posts_uploads = posts_uploads_get_posts($posts);
	$uploads = uploads_get_posts($posts);
	$hidden_threads = hidden_threads_get_board($board['id'], $_SESSION['user']);
	$upload_types = upload_types_get_board($board['id']);
	// Формирование вывода.
	$smarty->assign('boards', $boards);
	$smarty->assign('rempass', $rempass);
	$smarty->assign('board_name', $board['name']);
	$smarty->assign('thread', array($thread));
	$smarty->assign('thread_num', $thread['original_post']);
	$smarty->assign('is_guest', $_SESSION['user'] == 1 ? true : false);
	$smarty->assign('upload_types', $upload_types);
	$smarty->assign('is_moderatable', $is_moderatable);
	$smarty->assign('with_files', $board['with_files'] || $thread['with_files']);
	$smarty->assign('force_anonymous', $board['force_anonymous']);
	event_daynight($smarty);	// EVENT HERE! (not default kotoba function)
	$view_html = $smarty->fetch('threads_header.tpl');
	$view_thread_html = '';
	$view_posts_html = '';
	foreach($posts as $p)
		if($thread['original_post'] == $p['number'])
		{
			// У некоторых старых сообщений нет имени отправителя.
			if(!$board['force_anonymous'] && $board['default_name']
				&& !$p['name'])
			{
				$p['name'] = $board['default_name'];
			}
			// Оригинальное сообщение.
			$smarty->assign('original_with_files', false);
			$smarty->assign('original_theme', $p['subject']);
			$smarty->assign('original_name', $p['name']);
			$smarty->assign('original_time', $p['date_time']);
			$smarty->assign('original_id', $p['id']);
			$smarty->assign('original_num', $p['number']);
			$smarty->assign('original_text', $p['text']);
			// В данной версии 1 сообщение = 1 файл.
			foreach($posts_uploads as $pu)
				if($p['id'] == $pu['post'])
				{
					// В данной версии 1 сообщение = 1 файл.
					foreach($uploads as $u)
						if($pu['upload'] == $u['id'])
						{
							$smarty->assign('original_with_files', true);
							$smarty->assign('original_file_link', Config::DIR_PATH . "/{$board['name']}/img/" . basename($u['file_name']));
							$smarty->assign('original_file_name', $u['file_name']);
							$smarty->assign('original_file_size', $u['size']);
							$smarty->assign('original_file_width', $u['file_w']);
							$smarty->assign('original_file_heigth', $u['file_h']);
							$smarty->assign('original_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/" . basename($u['thumbnail_name']));
							$smarty->assign('original_file_thumbnail_width', $u['thumbnail_w']);
							$smarty->assign('original_file_thumbnail_heigth', $u['thumbnail_h']);
						}
				}
			$smarty->assign('sticky', $thread['sticky']);
			$view_thread_html = $smarty->fetch('post_original.tpl');
			if($is_admin)
			{
				$smarty->assign('post_id',  $p['id']);
				$smarty->assign('ip', long2ip($p['ip']));
				$smarty->assign('post_num', $p['number']);
				$view_thread_html .= $smarty->fetch('mod_mini_panel.tpl');
			}
		}
		else
		{
			$smarty->assign('simple_with_files', false);
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
							$smarty->assign('simple_with_files', true);
							$smarty->assign('simple_file_link', Config::DIR_PATH . "/{$board['name']}/img/" . basename($u['file_name']));
							$smarty->assign('simple_file_name', $u['file_name']);
							$smarty->assign('simple_file_size', $u['size']);
							$smarty->assign('simple_file_width', $u['file_w']);
							$smarty->assign('simple_file_heigth', $u['file_h']);
							$smarty->assign('simple_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/" . basename($u['thumbnail_name']));
							$smarty->assign('simple_file_thumbnail_width', $u['thumbnail_w']);
							$smarty->assign('simple_file_thumbnail_heigth', $u['thumbnail_h']);
						}
				}
			$view_posts_html .= $smarty->fetch('post_simple.tpl');
			if($is_admin)
			{
				$smarty->assign('post_id',  $p['id']);
				$smarty->assign('ip', long2ip($p['ip']));
				$smarty->assign('post_num', $p['number']);
				$view_posts_html .= $smarty->fetch('mod_mini_panel.tpl');
			}
		}
	$view_html .= $view_thread_html . $view_posts_html;
	$smarty->assign('hidden_threads', $hidden_threads);
	$view_html .= $smarty->fetch('threads_footer.tpl');
	DataExchange::releaseResources();
	echo $view_html;
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>