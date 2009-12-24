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
	$board_name = boards_check_name($_GET['board']);
	$page = 1;
	if(isset($_GET['page']))
		$page = check_page($_GET['page']);
	$password = !isset($_SESSION['password']) || $_SESSION['password'] === null
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
// Получние нитей, сообщений и другой необходимой информации.
	$threads_count = threads_get_view_threadscount($_SESSION['user'],
		$board['id']);
	$page_max = ($threads_count % $_SESSION['threads_per_page'] == 0
		? (int)($threads_count / $_SESSION['threads_per_page'])
		: (int)($threads_count / $_SESSION['threads_per_page']) + 1);
	if($page_max == 0)
		$page_max = 1;		// Important for empty boards.
	if($page > $page_max)
		throw new LimitException(LimitException::$messages['MAX_PAGE']);
	$is_admin = false;
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$is_admin = true;
	$threads = threads_get_board_view($board['id'], $page, $_SESSION['user'],
		$_SESSION['threads_per_page']);
	$posts = posts_get_threads_view($threads, $_SESSION['user'],
		$_SESSION['posts_per_thread']);
	$posts_uploads = posts_uploads_get_posts($posts);
	$uploads = uploads_get_posts($posts);
	$hidden_threads = hidden_threads_get_board($board['id'], $_SESSION['user']);
	$upload_types = upload_types_get_board($board['id']);
	$macrochan_tags = array('orgasm_face');
// Формирование вывода.
	$smarty->assign('board', $board);
	$smarty->assign('boards', $boards);
	$smarty->assign('is_admin', $is_admin);
	$smarty->assign('password', $password);
	$smarty->assign('upload_types', $upload_types);
	$pages = array();
	for($i = 1; $i <= $page_max; $i++)
		array_push($pages, $i);
	$smarty->assign('pages', $pages);
	$smarty->assign('page', $page);
	$smarty->assign('goto', $_SESSION['goto']);
	$smarty->assign('macrochan_tags', $macrochan_tags);
	//event_daynight($smarty);	// EVENT HERE! (not default kotoba function)
	$boards_html = $smarty->fetch('board_header.tpl');
	$boards_thread_html = '';		// Код предпросмотра нити.
	$boards_posts_html = '';		// Код сообщений из препдпросмотра нитей.
	$recived_posts_count = 0;		// Количество показанных сообщений в предпросмотре нити.
	$original_post = null;			// Оригинальное сообщение с допольнительными полями.
	$original_uploads = array();	// Массив файлов, прикрепленных к оригинальному сообщению.
	$simple_uploads = array();		// Массив файлов, прикрепленных к сообщению.
	foreach($threads as $t)
	{
		$smarty->assign('thread', $t);
		foreach($posts as $p)
			// Сообщение принадлежит текущей нити.
			if($t['id'] == $p['thread'])
			{
				$recived_posts_count++;
				// Имя отправителя по умолчанию.
				if(!$board['force_anonymous'] && $board['default_name']
					&& !$p['name'])
				{
					$p['name'] = $board['default_name'];
				}
				// Оригинальное сообщение.
				if($t['original_post'] == $p['number'])
				{
					$p['with_files'] = false;
					$p['text'] = posts_corp_text($p['text'],
						$_SESSION['lines_per_post'], $is_cutted);
					$p['text_cutted'] = $is_cutted;
					// В данной версии 1 сообщение = 1 файл
					foreach($posts_uploads as $pu)
						if($p['id'] == $pu['post'])
						{
							// В данной версии 1 сообщение = 1 файл
							foreach($uploads as $u)
								if($pu['upload'] == $u['id'])
								{
									$p['with_files'] = true;
									$u['is_embed'] = false;
									switch($u['link_type'])
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
					/*
					 * Код оригинального сообщения не может быть сформирован
					 * сразу, потому что ещё не подсчитано число выведенных
					 * сообщений.
					 */
					$original_post = $p;
				}
				else
				{
					$p['with_files'] = false;
					$p['text'] = posts_corp_text($p['text'],
						$_SESSION['lines_per_post'], $is_cutted);
					$p['text_cutted'] = $is_cutted;
					foreach($posts_uploads as $pu)
						if($p['id'] == $pu['post'])
						{
							foreach($uploads as $u)
								if($pu['upload'] == $u['id'])
								{
									$p['with_files'] = true;
									$u['is_embed'] = false;
									switch($u['link_type'])
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
					$smarty->assign('thread', array($t));	// TODO post_simple.tpl требует нити завёрнутой в массив, хотя на самом деле можно и просто нить.
					$boards_posts_html .= $smarty->fetch('post_simple.tpl');
					$smarty->assign('thread', $t);
					$simple_uploads = array();
				}// Оригинальное или простое сообщение.
			}// Сообщение принадлежит текущей нити.
		$smarty->assign('sticky', $t['sticky']);
		$smarty->assign('skipped', ($t['posts_count'] - $recived_posts_count));
		$smarty->assign('original_post', $original_post);
		$smarty->assign('original_uploads', $original_uploads);
		$boards_thread_html .= $smarty->fetch('board_thread_header.tpl');
		$boards_thread_html .= $boards_posts_html;
		$boards_thread_html .= $smarty->fetch('board_thread_footer.tpl');
		$boards_html .= $boards_thread_html;
		$boards_thread_html = '';
		$boards_posts_html = '';
		$recived_posts_count = 0;
		$original_post = null;
		$original_uploads = array();
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