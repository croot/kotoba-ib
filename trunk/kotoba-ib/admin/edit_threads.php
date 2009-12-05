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
// Скрипт редактирования связей загружаемых типов файлов с досками.
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
	/*
	 * Если пользователь является администратором, то он может редактировать все
	 * нити, если он является модератором или просто имеет права на модерирование
	 * некоторых нитей, то он может редактировать настройки нитей в соотвествии
	 * со своими правами.
	 */
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$threads = threads_get_all();
	else
		$threads = threads_get_all_moderate($_SESSION['user']);
	if(count($threads) <= 0)
		throw new NodataException(NodataException::$messages['THREADS_EDIT']);
	Logging::write_message(sprintf(Logging::$messages['EDIT_THREADS'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$boards = boards_get_all();
	$reload_threads = false;	// Были ли произведены изменения.
// Изменение параметров существующих нитей.
	if(isset($_POST['submited']))
		foreach($threads as $thread)
		{
			// Был ли изменён специфичный для нити бампилимит?
			$param_name = "bump_limit_{$thread['id']}";
			$new_bump_limit = $thread['bump_limit'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $thread['bump_limit'])
			{
				if($_POST[$param_name] == '')
					$new_bump_limit = null;
				else
					$new_bump_limit = threads_check_bump_limit($_POST[$param_name]);
			}
			// Был ли измен флаг закрепления?
			$param_name = "sticky_{$thread['id']}";
			$new_sticky = $thread['sticky'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $thread['sticky'])
			{
				$new_sticky = '1';
			}
			if(!isset($_POST[$param_name]) && $thread['sticky'])
				$new_sticky = '0';
			// Был ли измен флаг поднятия нити при ответе?
			$param_name = "sage_{$thread['id']}";
			$new_sage = $thread['sage'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $thread['sage'])
			{
				$new_sage = '1';
			}
			if(!isset($_POST[$param_name]) && $thread['sage'])
				$new_sage = '0';
			// Был ли изменен флаг загрузки файлов?
			$param_name = "with_files_{$thread['id']}";
			$new_with_images = $thread['with_files'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $thread['with_files'])
			{
				switch($_POST[$param_name])
				{
					case '':
						$new_with_images = null;
						break;
					case '1':
						foreach($boards as $board)
							if($board['id'] == $thread['board'])
							{
								if($board['with_files'])
									/*
									 * Загрузка файлов на доске разрешена,
									 * поэтому дополнительно разрешать её в этой
									 * нити не нужно.
									 */
									$new_with_images = null;
								else
									/*
									 * Загрузка файлов на доске запрещена, а в
									 * этой нити будет разрешена.
									 */
									$new_with_images = '1';
								break;
							}
						break;
					case '0':
						foreach($boards as $board)
							if($board['id'] == $thread['board'])
							{
								if($board['with_files'])
									/*
									 * Загрузка файлов на доске разрешена, а в
									 * этой нити будет запрещена.
									 */
									$new_with_images = '0';
								else
									/*
									 * Загрузка файлов на доске запрещена,
									 * поэтому дополнительно запрещать её в этой
									 * нити не нужно.
									 */
									$new_with_images = null;
								break;
							}
						break;
				}
				
			}
			// Были ли произведены какие-либо изменения?
			if($new_bump_limit != $thread['bump_limit']
				|| $new_sticky != $thread['sticky']
				|| $new_sage != $thread['sage']
				|| $new_with_images != $thread['with_files'])
			{
				threads_edit($thread['id'], $new_bump_limit, $new_sticky,
					$new_sage, $new_with_images);
				$reload_threads = true;
			}
		}
// Вывод формы редактирования.
	if($reload_threads)
	{
		if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
			$threads = threads_get_all();
		else
			$threads = threads_get_all_moderate($_SESSION['user']);
		if(count($threads) <= 0)
			throw new NodataException(NodataException::$messages['THREADS_EDIT']);
	}
	DataExchange::releaseResources();
	$smarty->assign('boards', $boards);
	$smarty->assign('threads', $threads);
	$smarty->display('edit_threads.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>