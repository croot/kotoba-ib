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
// TODO При установке прав для определённого треда задание доски не имеет смысла
// так как id треда уникален для всех досок. В этом случае board должно быть
// null. Аналогично для постов.

require_once '../kwrapper.php';
require_once Config::ABS_PATH . '/lang/' . Config::LANGUAGE . '/logging.php';

kotoba_setup($link, $smarty);
if(! in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'], $smarty, basename(__FILE__) . ' ' . __LINE__);
}
// TODO локализация действий, записывающихся в лог.
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'], 'Редактирование списка контроля доступа', $_SESSION['user'], $_SERVER['REMOTE_ADDR']), Logmsgs::open_logfile(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log'));
$groups = db_group_get($link, $smarty);
$boards = db_boards_get_all($link, $smarty);
$acl = db_acl_get($link, $smarty);
$reload_acl = false;	// Были ли произведены изменения.
/*
 * Добавим новую запись в список контроля доступа.
 */
if(isset($_POST['submited']) &&
	(	isset($_POST['new_group']) &&
		isset($_POST['new_board']) &&
		isset($_POST['new_thread']) &&
		isset($_POST['new_post'])) &&
	(	$_POST['new_group'] !== '' ||
		$_POST['new_board'] !== '' ||
		$_POST['new_thread'] !== '' ||
		$_POST['new_post'] !== ''))
{
	if($_POST['new_group'] === '')
		$new_group = -1;
	else
	{
		if(($new_group = check_format('group', $_POST['new_group'])) == false)
		{
			mysqli_close($link);
			kotoba_error(Errmsgs::$messages['GROUP_NAME'], $smarty, basename(__FILE__) . ' ' . __LINE__);
		}
		/* Определим идентификатор группы */
		$found = false;
		foreach($groups as $group)
			if($group['name'] == $new_group)
			{
				$found = true;
				$new_group = $group['id'];
			}
		if(! $found)
		{
			mysqli_close($link);
			kotoba_error(sprintf(Errmsgs::$messages['GROUP_NOT_FOUND'], $new_group), $smarty, basename(__FILE__) . ' ' . __LINE__);
		}
	}
	if($_POST['new_board'] === '')
		$new_board = -1;
	else
	{
		if(($new_board = check_format('board', $_POST['new_board'])) == false)
		{
			mysqli_close($link);
			kotoba_error(Errmsgs::$messages['BOARD_NAME'], $smarty, basename(__FILE__) . ' ' . __LINE__);
		}
		/* Определим идентификатор доски */
		$found = false;
		foreach($boards as $board)
			if($board['name'] == $new_board)
			{
				$found = true;
				$new_board = $board['id'];
			}
		if(! $found)
		{
			mysqli_close($link);
			kotoba_error(sprintf(Errmsgs::$messages['BOARD_NOT_FOUND'], $new_board), $smarty, basename(__FILE__) . ' ' . __LINE__);
		}
	}
	if($_POST['new_thread'] === '')
		$new_thread = -1;
	else
	{
		if(($new_thread = check_format('board', $_POST['new_thread'])) == false)
		{
			mysqli_close($link);
			kotoba_error(Errmsgs::$messages['THREAD_NUM'], $smarty, basename(__FILE__) . ' ' . __LINE__);
		}
	}
	if($_POST['new_post'] === '')
		$new_post = -1;
	else
	{
		if(($new_post = check_format('post', $_POST['new_post'])) == false)
		{
			mysqli_close($link);
			kotoba_error(Errmsgs::$messages['POST_NUM'], $smarty, basename(__FILE__) . ' ' . __LINE__);
		}
	}
	if(isset($_POST['new_view']))
		$new_view = 1;
	else
		$new_view = 0;
	if(isset($_POST['new_change']))
		$new_change = 1;
	else
		$new_change = 0;
	if(isset($_POST['new_moderate']))
		$new_moderate = 1;
	else
		$new_moderate = 0;
	/*
	 * Если запрещен просмотр, то редактирование и модерирование не имею смысла.
	 * Если запрещено редактирование, то модерирование не имеет смысла.
	 */
	if($new_view == 0)
	{
		$new_change = 0;
		$new_moderate = 0;
	}
	elseif($new_change == 0)
		$new_moderate = 0;
	/*
	 * Поищем, нет ли уже такого правила. Если есть, то надо только изменить
	 * существующее.
	 */
	$found = false;
	foreach($acl as $record)
	{
		if(	(($record['group'] == null && $new_group == -1) || ($record['group'] == $new_group)) &&
			(($record['board'] == null && $new_board == -1) || ($record['board'] == $new_board)) &&
			(($record['thread'] == null && $new_thread == -1) || ($record['thread'] == $new_thread)) &&
			(($record['post'] == null && $new_post == -1) || ($record['post'] == $new_post)))
		{
			db_acl_edit($new_group, $new_board, $new_thread, $new_post, $new_view, $new_change, $new_moderate, $link, $smarty);
			$reload_acl = true;
			$found = true;
		}
						
	}
	if(! $found)
	{
		db_acl_add($new_group, $new_board, $new_thread, $new_post, $new_view, $new_change, $new_moderate, $link, $smarty);
		$reload_acl = true;
	}
}
/*
 * Изменим права в существующих записях в списке контроля доступа.
 */
if(isset($_POST['submited']))
	foreach($acl as $record)
	{
		if($record['view'] == 1 && !isset($_POST["view_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
		{
			/*
			 * Сняли право просмотра в этой записи. Следовательно права
			 * редактирования и модерирования не имею смысла. Обновляем запись
			 * и переходим к следующей.
			 */
			db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], 0, 0, 0, $link, $smarty);
			$reload_acl = true;
			continue;
		}
		if($record['view'] == 0 && isset($_POST["view_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
		{
			/*
			 * Добавили право просмотра в этой записи. Поскольку права просмотра
			 * небыло, то и других прав не должно было быть. Проверим, не добавили
			 * ли других прав в этой записи.
			 */
			if($record['change'] == 0 && isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
			{
				/*
				 * Ещё добавили право редактирования. Проверим, не добавили ли ещё
				 * права модерирования.
				 */
				if($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
				{
					/*
					 * Ещё добавили право модерирования.
					 */
					db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], 1, 1, 1, $link, $smarty);
					$reload_acl = true;
					continue;
				}
				else
				{
					/*
					 * Право модерирования не добавили. Значит только просмотр и
					 * редактирование.
					 */
					db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], 1, 1, 0, $link, $smarty);
					$reload_acl = true;
					continue;
				}
			}
			else
			{
				/*
				 * Поскольку без права редактирования право модерирования не имеет
				 * смысла, то и проверять добавили его или нет без права
				 * редактирования не нужно.
				 */
				db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], 1, 0, 0, $link, $smarty);
				$reload_acl = true;
				continue;
			}
		}
		/*
		 * Право просмотра не меняли.
		 */
		if($record['change'] == 1 && !isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
		{
			/*
			 * Сняли право редактирования. Следовательно, право модерирования не
			 * имеет смысла. Обновим запись и перейдём к следующей.
			 */
			db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], $record['view'], 0, 0, $link, $smarty);
			$reload_acl = true;
			continue;
		}
		if($record['change'] == 0 && isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
		{
			/*
			 * Добавили право редактирования. Если права просмотра не было, то
			 * игнориуем это изменение и переходим к следующей записи, если было,
			 * то проверим, не было ли установлено ещё право модерирования.
			 */
			if($record['view'] == 0)
				continue;
			else
			{
				if($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
				{
					/*
					 * Добавили ещё право модерирования.
					 */
					db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], 1, 1, 1, $link, $smarty);
					$reload_acl = true;
					continue;
				}
				else
				{
					/*
					 * Поскольку права редактривания небыло, но его добавили, то
					 * права модерирования не могло быть.
					 */
					db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], 1, 1, 0, $link, $smarty);
					$reload_acl = true;
					continue;
				}
			}
		}
		/*
		 * Право просмотра и редактрирования не меняли.
		 */
		if($record['moderate'] == 1 && !isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
		{
			/*
			 * Сняли право модерирования.
			 */
			db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], $record['view'], $record['change'], 0, $link, $smarty);
			$reload_acl = true;
			continue;
		}
		if($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
		{
			/*
			 * Добавили право модерирования.
			 */
			db_acl_edit($record['group'], $record['board'], $record['thread'], $record['post'], $record['view'], $record['change'], 1, $link, $smarty);
			$reload_acl = true;
			continue;
		}
	}
/*
 * Удалим правила из списка контроля доступа.
 */
if(isset($_POST['submited']))
	foreach($acl as $record)
		if(isset($_POST["delete_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"]))
		{
			db_acl_delete($record['group'], $record['board'], $record['thread'], $record['post'], $link, $smarty);
			$reload_acl = true;
		}
/*
 * Вывод формы редактирования.
 */
if($reload_acl)
	$acl = db_acl_get($link, $smarty);
mysqli_close($link);
$smarty->assign('groups', $groups);
$smarty->assign('boards', $boards);
$smarty->assign('acl', $acl);
$smarty->display('edit_acl.tpl');
?>