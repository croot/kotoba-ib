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
	$board_name = boards_check_name($_GET['board']);
	$thread_num = threads_check_number($_GET['thread']);
	$password = !isset($_SESSION['password']) || $_SESSION['password'] == null
		? '' : $_SESSION['password'];
	/*
	 * Доски нужны для вывода списка досок, поэтому получим все и среди них
	 * будем искать запрашиваемую.
	 */
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
// Получение данных.
	$is_admin = false;
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$is_admin = true;
	$thread = threads_get_specifed_view($board['id'], $thread_num,
		$_SESSION['user']);
	if($thread['archived'])
	{
		// Нить была заархивирована.
		DataExchange::releaseResources();
		header('Location: ' . Config::DIR_PATH . "/{$board['name']}/arch/"
			. "{$thread['original_post']}.html");
		exit;
	}
	$is_moderatable = threads_check_specifed_moderate($thread['id'],
		$_SESSION['user']);
	$is_admin = false;
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$is_admin = true;
	$filter = function($posts_per_thread, $thread, $post)
	{
		static $recived = 0;
		if($thread['original_post'] == $post['number'])
			return true;
		$recived++;
		if($recived > $thread['posts_count'] - $posts_per_thread)
			return true;
		return false;
	};
	$posts = posts_get_visible_filtred_by_threads(array($thread),
		$_SESSION['user'], $filter, $thread['posts_count']);
	$posts_uploads = posts_uploads_get_by_posts($posts);
	$uploads = uploads_get_by_posts($posts);
	$ht_filter = function($user, $hidden_thread)
	{
		if($hidden_thread['user'] == $user)
			return true;
		return false;
	};
	$hidden_threads = hidden_threads_get_filtred_by_boards(array($board),
		$ht_filter, $_SESSION['user']);
	$upload_types = upload_types_get_board($board['id']);
	$macrochan_tags = array('orgasm_face');
// Формирование вывода.
	$smarty->assign('board', $board);
	$smarty->assign('boards', $boards);
	$smarty->assign('thread', array($thread));
	$smarty->assign('is_moderatable', $is_moderatable);
	$smarty->assign('is_admin', $is_admin);
	$smarty->assign('password', $password);
	$smarty->assign('upload_types', $upload_types);
	$smarty->assign('goto', $_SESSION['goto']);
	$smarty->assign('macrochan_tags', $macrochan_tags);
	$smarty->assign('ib_name', Config::IB_NAME);
	//event_daynight($smarty);	// EVENT HERE! (not default kotoba function)
	$view_html = $smarty->fetch('threads_header.tpl');
	$view_thread_html = '';
	$view_posts_html = '';
	$original_post = null;			// Оригинальное сообщение с допольнительными полями.
	$original_uploads = array();	// Массив файлов, прикрепленных к оригинальному сообщению.
	$simple_uploads = array();		// Массив файлов, прикрепленных к сообщению.
	foreach($posts as $p)
	{
		// Имя отправителя по умолчанию.
		if(!$board['force_anonymous'] && $board['default_name']
			&& !$p['name'])
		{
			$p['name'] = $board['default_name'];
		}
		// Оригинальное сообщение.
		if($thread['original_post'] == $p['number'])
		{
			$p['with_files'] = false;
			// В данной версии 1 сообщение = 1 файл.
			foreach($posts_uploads as $pu)
				if($p['id'] == $pu['post'])
				{
					// В данной версии 1 сообщение = 1 файл.
					foreach($uploads as $u)
						if($pu['upload'] == $u['id'])
						{
							$p['with_files'] = true;
							$u['is_embed'] = false;
							switch($u['upload_type'])
							{
								case Config::LINK_TYPE_VIRTUAL:
									$u['file_link'] = Config::DIR_PATH . "/{$board['name']}/img/{$u['file']}";
									$u['file_name'] = $u['file'];
									if($u['is_image'])
										$u['file_thumbnail_link'] = Config::DIR_PATH . "/{$board['name']}/thumb/{$u['thumbnail']}";
									else
										$u['file_thumbnail_link'] = Config::DIR_PATH . "/res/{$u['thumbnail']}";
									break;
								case Config::LINK_TYPE_URL:
									$u['file_link'] = $u['file'];
									$u['file_name'] = $u['file'];
									$u['file_thumbnail_link'] = $u['thumbnail'];
									break;
								case Config::LINK_TYPE_CODE:
									$u['is_embed'] = true;
									$smarty->assign('code', $u['file']);
									$u['file_link'] = $smarty->fetch('youtube.tpl');
									break;
								default:
									throw new CommonException('Not supported.');
									break;
							}
							array_push($original_uploads, $u);
						}
				}
			$p['ip'] = long2ip($p['ip']);
			$smarty->assign('original_post', $p);
			$smarty->assign('original_uploads', $original_uploads);
			$smarty->assign('sticky', $thread['sticky']);
			$view_thread_html = $smarty->fetch('post_original.tpl');
		}
		else
		{
			$p['with_files'] = false;
			foreach($posts_uploads as $pu)
				if($p['id'] == $pu['post'])
				{
					foreach($uploads as $u)
						if($pu['upload'] == $u['id'])
						{
							$p['with_files'] = true;
							$u['is_embed'] = false;
							switch($u['upload_type'])
							{
								case Config::LINK_TYPE_VIRTUAL:
									$u['file_link'] = Config::DIR_PATH . "/{$board['name']}/img/{$u['file']}";
									$u['file_name'] = $u['file'];
									if($u['is_image'])
										$u['file_thumbnail_link'] = Config::DIR_PATH . "/{$board['name']}/thumb/{$u['thumbnail']}";
									else
										$u['file_thumbnail_link'] = Config::DIR_PATH . "/img/{$u['thumbnail']}";
									break;
								case Config::LINK_TYPE_URL:
									$u['file_link'] = $u['file'];
									$u['file_name'] = $u['file'];
									$u['file_thumbnail_link'] = $u['thumbnail'];
									break;
								case Config::LINK_TYPE_CODE:
									$u['is_embed'] = true;
									$smarty->assign('code', $u['file']);
									$u['file_link'] = $smarty->fetch('youtube.tpl');
									break;
								default:
									throw new CommonException('Not supported.');
									break;
							}
							array_push($simple_uploads, $u);
						}
				}
			$p['ip'] = long2ip($p['ip']);
			$smarty->assign('simple_post', $p);
			$smarty->assign('simple_uploads', $simple_uploads);
			$view_posts_html .= $smarty->fetch('post_simple.tpl');
			$simple_uploads = array();
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