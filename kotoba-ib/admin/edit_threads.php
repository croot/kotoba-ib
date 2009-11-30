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
	$boards = boards_get_all();	// TODO Даже модерировать треды на доске, которая не видна?
	$reload_threads = false;	// Были ли произведены изменения.
	// Изменение параметров существующих нитей.
	if(isset($_POST['submited']))
		foreach($threads as $thread)
		{
			// Был ли изменён бампилимит?
			$param_name = "bump_limit_{$thread['id']}";
			$new_bump_limit = $thread['bump_limit'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $thread['bump_limit'])
			{
				if($_POST[$param_name] === '')
					$new_bump_limit = null;
				else
					$new_bump_limit = threads_check_bump_limit($_POST[$param_name]);
			}
			// Был ли измен флаг поднятия нити при ответе?
			$param_name = "sage_{$thread['id']}";
			$new_sage = $thread['sage'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $thread['sage'])
			{
				$new_sage = threads_check_sage($_POST[$param_name]);
			}
			if($thread['sage'] && !isset($_POST[$param_name]))
				$new_sage = 0;
			// Был ли изменен флаг прикрепления файлов к ответам в нить?
			$param_name = "with_images_{$thread['id']}";
			$new_with_images = $thread['with_images'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $thread['with_images'])
			{
				$new_with_images = threads_check_with_images($_POST[$param_name]);
			}
			if($thread['with_images'] && !isset($_POST[$param_name]))
				$new_with_images = 0;
			// Были ли произведены какие-либо изменения?
			if($new_bump_limit != $thread['bump_limit']
				|| $new_sage != $thread['sage']
				|| $new_with_images != $thread['with_images'])
			{
				threads_edit($thread['id'], $new_bump_limit, $new_sage,
						$new_with_images);
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