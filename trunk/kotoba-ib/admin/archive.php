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
// Скрипт архивирования нитей.
require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_ARCHIVE'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
// Получение данных.
	$threads = threads_get_archived();
	foreach($threads as $thread)
	{
		$board = boards_get_specifed($thread['board']);
		$posts = posts_get_thread($thread['id']);
		$posts_uploads = posts_uploads_get_posts($posts);
		$uploads = uploads_get_posts($posts);
// Формирование вывода.
		$smarty->assign('board', $board);
		$smarty->assign('thread', array($thread));
		$view_html = $smarty->fetch('header.tpl');
		$view_thread_html = '';
		$view_posts_html = '';
		$original_post = null;			// Оригинальное сообщение с допольнительными полями.
		$original_uploads = array();	// Массив файлов, прикрепленных к оригинальному сообщению.
		$simple_uploads = array();		// Массив файлов, прикрепленных к сообщению.
		foreach($posts as $p)
		{
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