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
// Скприпт архивирования нитей.
require '../config.php';
require Config::ABS_PATH . '/modules/errors.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/modules/logging.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/modules/db.php';
require Config::ABS_PATH . '/modules/cache.php';
require Config::ABS_PATH . '/modules/common.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_ARCHIVE'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$threads = threads_get_all_archived();
	foreach($threads as $thread)
	{
		// Получение данных.
		$board = boards_get_specifed($thread['board']);
		$posts = posts_get_thread($thread['id']);
		$posts_uploads = posts_uploads_get_posts($posts);
		$uploads = uploads_get_posts($posts);
		// Формирование вывода.
		$smarty->assign('board_name', $board['name']);
		$smarty->assign('thread', array($thread));
		$smarty->assign('thread_num', $thread['original_post']);
		$smarty->assign('with_files', $board['with_files'] || $thread['with_files']);
		$smarty->assign('force_anonymous', $board['force_anonymous']);
		$view_html = $smarty->fetch('header.tpl');
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
								switch($u['link_type'])
								{
									case Config::LINK_TYPE_VIRTUAL:
										$smarty->assign('original_file_link', Config::DIR_PATH . "/{$board['name']}/img/{$u['file']}");
										$smarty->assign('original_file_name', $u['file']);
										$smarty->assign('original_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/{$u['thumbnail']}");
										break;
									case Config::LINK_TYPE_URL:
										$smarty->assign('original_file_link', $u['file']);
										$smarty->assign('original_file_name', $u['file']);
										$smarty->assign('original_file_thumbnail_link', $u['thumbnail']);
										break;
									case Config::LINK_TYPE_CODE:
									default:
										throw new CommonException('Not supported.');
										break;
								}
								$smarty->assign('original_file_size', $u['size']);
								$smarty->assign('original_file_width', $u['file_w']);
								$smarty->assign('original_file_heigth', $u['file_h']);
								$smarty->assign('original_file_thumbnail_width', $u['thumbnail_w']);
								$smarty->assign('original_file_thumbnail_heigth', $u['thumbnail_h']);
							}
					}
				$view_thread_html = $smarty->fetch('post_original.tpl');
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
								switch($u['link_type'])
								{
									case Config::LINK_TYPE_VIRTUAL:
										$smarty->assign('simple_file_link', Config::DIR_PATH . "/{$board['name']}/img/{$u['file']}");
										$smarty->assign('simple_file_name', $u['file']);
										$smarty->assign('simple_file_thumbnail_link', Config::DIR_PATH . "/{$board['name']}/thumb/{$u['thumbnail']}");
										break;
									case Config::LINK_TYPE_URL:
										$smarty->assign('simple_file_link', $u['file']);
										$smarty->assign('simple_file_name', $u['file']);
										$smarty->assign('simple_file_thumbnail_link', $u['thumbnail']);
										break;
									case Config::LINK_TYPE_CODE:
									default:
										throw new CommonException('Not supported.');
										break;
								}
								$smarty->assign('simple_file_size', $u['size']);
								$smarty->assign('simple_file_width', $u['file_w']);
								$smarty->assign('simple_file_heigth', $u['file_h']);
								$smarty->assign('simple_file_thumbnail_width', $u['thumbnail_w']);
								$smarty->assign('simple_file_thumbnail_heigth', $u['thumbnail_h']);
							}
					}
				$view_posts_html .= $smarty->fetch('post_simple.tpl');
			}
		$view_html .= $view_thread_html . $view_posts_html;
		$view_html .= $smarty->fetch('threads_footer.tpl');
		// Создание архивной копии.
		$file = fopen(Config::ABS_PATH . "/{$board['name']}/arch/"
			. "{$thread['original_post']}.html", 'w');
		if($file)
			fwrite($file, $view_html);
		fclose($file);
		// Удаление данных из базы.
		echo "Thread {$thread['original_post']} saved.<br>";
	}
	DataExchange::releaseResources();
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>