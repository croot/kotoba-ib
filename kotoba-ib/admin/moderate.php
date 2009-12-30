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
// Основной скрипт модератора.
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
	$smarty = new SmartyKotobaSetup($_SESSION['language'],
		$_SESSION['stylesheet']);
	// Возможно завершение работы скрипта.
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
	$is_admin = false;
	if(is_admin())
	{
		$is_admin = true;
	}
	if(!$is_admin && !is_mod())
	{
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']
			 . ' ' . PremissionException::$messages['NOT_MOD']);
	}
	Logging::write_message(sprintf(Logging::$messages['MOD_FUNCTIONS_MODERATE'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$boards = ($is_admin == true)
		? boards_get_all() : boards_get_moderatable($_SESSION['user']);
	$output = '';
	$smarty->assign('is_admin', $is_admin);
	$smarty->assign('boards', $boards);
	$output .= $smarty->fetch('moderate_header.tpl');
	date_default_timezone_set(Config::DEFAULT_TIMEZONE);
	if(isset($_POST['filter'])
		&& isset($_POST['filter_board'])
		&& isset($_POST['filter_date_time'])
		&& isset($_POST['filter_number'])
		&& $_POST['filter_board'] != ''
		&& ($_POST['filter_date_time'] != '' || $_POST['filter_number'] != ''))
	{
		if($_POST['filter_board'] == 'all')
		{
			if($is_admin)
				$filter_boards = $boards;
			else
				throw new PremissionException(PremissionException::$messages['NOT_ADMIN']);
		}
		else
		{
			$filter_boards = array();
			foreach($boards as $board)
				if($_POST['filter_board'] == $board['id'])
				{
					array_push($filter_boards, $board);
					break;	// Пока выбрать можно только одну.
				}
		}
		if($_POST['filter_date_time'] != '')
		{
			$filter_date_time = date_format(date_create($_POST['filter_date_time']),
				'U');
			$fileter = function($boards, $filter_date_time, $post)
			{
				date_default_timezone_set(Config::DEFAULT_TIMEZONE);
				foreach($boards as $board)
					if($post['board'] == $board['id']
						&& (date_format(date_create($post['date_time']), 'U') >= $filter_date_time))
					{
						return true;
					}
				return false;
			};
			$posts = posts_get_filtred($fileter, $filter_boards,
				$filter_date_time);
		}
		elseif($_POST['filter_number'] != '')
		{
			$filter_number = posts_check_number($_POST['filter_number']);
			$fileter = function($boards, $filter_number, $post)
			{
				foreach($boards as $board)
					if($post['board'] == $board['id']
						&& $post['number'] >= $filter_number)
					{
						return true;
					}
				return false;
			};
			$posts = posts_get_filtred($fileter, $filter_boards,
				$filter_number);
		}
		$posts_uploads = posts_uploads_get_posts($posts);
		$uploads = uploads_get_posts($posts);
		foreach($posts as $post)
		{
			$post['with_files'] = false;
			$post_uploads = array();
			foreach($posts_uploads as $pu)
				if($pu['post'] == $post['id'])
				{
					foreach($uploads as $upload)
						if($upload['id'] == $pu['upload'])
						{
							$post['with_files'] = true;
							$upload['is_embed'] = false;
							switch($upload['link_type'])
							{
								case Config::LINK_TYPE_VIRTUAL:
									$upload['file_link'] = Config::DIR_PATH . "/{$post['board_name']}/img/{$upload['file']}";
									$upload['file_name'] = $upload['file'];
									if($upload['is_image'])
										$upload['file_thumbnail_link'] = Config::DIR_PATH . "/{$post['board_name']}/thumb/{$upload['thumbnail']}";
									else
										$upload['file_thumbnail_link'] = Config::DIR_PATH . "/res/{$upload['thumbnail']}";
									break;
								case Config::LINK_TYPE_URL:
									$upload['file_link'] = $upload['file'];
									$upload['file_name'] = $upload['file'];
									$upload['file_thumbnail_link'] = $upload['thumbnail'];
									break;
								case Config::LINK_TYPE_CODE:
									$upload['is_embed'] = true;
									$smarty->assign('code', $upload['file']);
									$upload['file_link'] = $smarty->fetch('youtube.tpl');
									break;
								default:
									throw new CommonException('Not supported.');
									break;
							}
							array_push($post_uploads, $upload);
							break;
						}
					break;	// У сообщения пока может быть только 1 файл.
				}
			$smarty->assign('post', $post);
			$smarty->assign('uploads', $post_uploads);
			$output .= $smarty->fetch('moderate_post.tpl');
		}
	}
	if(isset($_POST['action'])
		&& isset($_POST['ban_type'])
		&& isset($_POST['del_type'])
		&& ($_POST['ban_type'] != 'none' || $_POST['del_type'] != 'none'))
	{
		$posts = posts_get_by_boards($boards);
		foreach($posts as $p)
			if(isset($_POST["mark_{$p['id']}"]))
			{
				switch($_POST['ban_type'])
				{
					case 'simple':
						// Пока что бан на час.
						bans_add(ip2long($p['ip']), ip2long($p['ip']), '',
							date('Y-m-d H:i:s', time() + (60 * 60 * 24)));
						break;
					case 'hard':
						break;
				}
				switch($_POST['del_type'])
				{
					case 'post':
						posts_delete($p['id']);
						break;
					case 'file':
						posts_uploads_delete_post($p['id']);
						break;
					case 'last':
						// Удалить посты за последний час.
						posts_delete_last($p['id'],
							date(Config::DATETIME_FORMAT, time() - (60 * 60)));
						break;
				}
			}
	}
	DataExchange::releaseResources();
	$output .= $smarty->fetch('moderate_footer.tpl');
	echo $output;
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>