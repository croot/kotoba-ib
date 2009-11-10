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

require '../kwrapper.php';
require_once Config::ABS_PATH . '/lang/' . Config::LANGUAGE . '/logging.php';

kotoba_setup($link, $smarty);
/*
 * Если пользователь является администратором, то он может редактировать все
 * нити, если он является модератором или просто имеет права на модерирование
 * некоторых нитей, то он может редактировать настройки нитей в соотвествии
 * со своими правами.
 */
if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
	$threads = db_threads_get_all($link, $smarty);
else
	$threads = db_threads_get_mod($_SESSION['user'], $link, $smarty);
if(count($threads) <= 0)
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['THREADS_EDIT'],
		$smarty, basename(__FILE__) . ' ' . __LINE__);
}
kotoba_log(sprintf(Logmsgs::$messages['THREADS_EDIT_FUNCTIONS'],
		$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$boards = db_boards_get_all($link, $smarty);
$reload_threads = false;	// Были ли произведены изменения.
/*
 * Изменение параметров существующих нитей.
 */
if(isset($_POST['submited']))
	foreach($threads as $thread)
	{
		/* Фиксирование изменений бампилимита. */
		$param_name = "bump_limit_{$thread['id']}";
		$new_bump_limit = $thread['bump_limit'];
		if(isset($_POST[$param_name])
			&& $_POST[$param_name] != $thread['bump_limit'])
		{
			if($_POST[$param_name] === '')
				$new_bump_limit = -1;
			else
				$new_bump_limit = check_bump_limit($_POST[$param_name], $link, $smarty);
		}
		$param_name = "sage_{$thread['id']}";
		$new_sage = $thread['sage'];
		/* Сажа была включена. */
		if(isset($_POST[$param_name]) && $_POST[$param_name] != $thread['sage'])
			$new_sage = check_sage($_POST[$param_name], $link, $smarty);
		/* Сажа была выключена. */
		if($thread['sage'] && !isset($_POST[$param_name]))
			$new_sage = 0;
		$param_name = "with_images_{$thread['id']}";
		$new_with_images = $thread['with_images'];
		/* Картинки были включены. */
		if(isset($_POST[$param_name])
			&& $_POST[$param_name] != $thread['with_images'])
		{
			$new_with_images = check_with_images($_POST[$param_name], $link,
													$smarty);
		}
		/* Картинки были выключены. */
		if($thread['with_images'] && !isset($_POST[$param_name]))
			$new_with_images = 0;
		/*
		 * Если были изменения.
		 */
		if($new_bump_limit != $thread['bump_limit']
			|| $new_sage != $thread['sage']
			|| $new_with_images != $thread['with_images'])
		{
			try
			{
				db_threads_edit($thread['id'], $new_bump_limit, $new_sage,
					$new_with_images, $link);
			}
			catch(Exception $e)
			{
				$smarty->assign('msg', $e->__toString());
				if(isset($link) && $link instanceof MySQLi)
					mysqli_close($link);
				die($smarty->fetch('error.tpl'));
			}
			$reload_threads = true;
		}
	}
/*
 * Обновление списка досок, если нужно. Вывод формы редактирования.
 */
if($reload_threads)
{
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		$threads = db_threads_get_all($link, $smarty);
	else
		$threads = db_threads_get_mod($_SESSION['user'], $link, $smarty);
	if(count($threads) <= 0)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['THREADS_EDIT'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
}
mysqli_close($link);
$smarty->assign('boards', $boards);
$smarty->assign('threads', $threads);
$smarty->display('edit_threads.tpl');
?>