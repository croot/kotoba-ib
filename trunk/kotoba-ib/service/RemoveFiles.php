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

require '../common.php';

$smarty = new SmartyKotobaSetup();

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', KOTOBA_SESSION_LIFETIME);
ini_set('session.cookie_lifetime', KOTOBA_SESSION_LIFETIME);
session_start();

if(!isset($_SESSION['isLoggedIn']))
{
	$smarty->assign('error', 'Ошибка. Вы не вошли в систему');
	die($smarty->fetch('RemoveFiles.tpl'));
}

require $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/databaseconnect.php';

if(($result = mysql_query('select `User Settings` from `users` where SID = \'' . session_id() . '\'')) != false)
{
	if(mysql_num_rows($result) > 0)
	{
		$user = mysql_fetch_array($result, MYSQL_ASSOC);
		$userSettings = GetSettings('user', $user['User Settings']);
		mysql_free_result($result);

		if($userSettings['ADMIN'] !== 'Y')
		{
			$smarty->assign('error', 'Ошибка. Вы не являетесь администратором');
			die($smarty->fetch('RemoveFiles.tpl'));
		}
	}
	else
	{
		mysql_free_result($result);
		$smarty->assign('error', 'Ошибка. Пользователь с SID ' . session_id() . ' не найден в базе');
		die($smarty->fetch('RemoveFiles.tpl'));
    }
}
else
{
	$smarty->assign('error', 'Ошибка. Невозможно получить данные пользователя. Причина: ' . mysql_error());
	die($smarty->fetch('RemoveFiles.tpl'));
}

if(($result = mysql_query('select b.`Name` `board`, p.`Post Settings` from `posts` p join `boards` b on p.`board` = b.`id`')) != false)
{
	if(mysql_num_rows($result) === 0 || mysql_num_rows($result) === false)
	{
		mysql_free_result($result);
		$smarty->assign('error', 'Ошибка. Не найдено ни одного сообщения');
		die($smarty->fetch('RemoveFiles.tpl'));
	}
}
else
{
	$smarty->assign('error', 'Ошибка. Не удалось получить настройки сообщений');
	die($smarty->fetch('RemoveFiles.tpl'));
}

$realFiles = array();

while(($row = mysql_fetch_array($result, MYSQL_ASSOC)) != false)
{
	$postSettings = GetSettings('post', $row['Post Settings']);

	if(isset($postSettings['IMGNAME']) && isset($postSettings['IMGEXT']))
	{
		if(!is_array($realFiles[$row['board']]))
			$realFiles[$row['board']] = array();

		array_push($realFiles[$row['board']], "{$postSettings['IMGNAME']}.{$postSettings['IMGEXT']}");
    }
}

mysql_free_result($result);

foreach(array_keys($realFiles) as $board)
{
	$allFiles[$board] = scandir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$board/img");

	// TODO Надо проверить, как это будет работать в *nix.
	unset($allFiles[$board][0]); // .
	unset($allFiles[$board][1]); // ..
}

$zombieFiles = array();

foreach(array_keys($allFiles) as $board)
{
	foreach($allFiles[$board] as $fileName)
		if(!in_array($fileName, $realFiles[$board]))
		{
			if(!is_array($zombieFiles[$board]))
				$zombieFiles[$board] = array();

			array_push($zombieFiles[$board], $fileName);
		}

	//reset($allFiles[$board]);
}

$actionList = '';
$actionCount = 0;

foreach(array_keys($zombieFiles) as $board)
{
	@mkdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/oldfiles/$board/img", 0777, true);
	@mkdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/oldfiles/$board/thumb", 0777, true);

	foreach($zombieFiles[$board] as $fileName)
		if(@rename($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$board/img/$fileName", $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/oldfiles/$board/img/$fileName"))
		{
			// Если существует картинка, то, возможно, существует и уменьшенная копия.
			$thumbName = substr($fileName, 0, strrpos($fileName, '.')) . 't.' . substr($fileName, strrpos($fileName, '.') + 1);
			@rename($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$board/thumb/$thumbName", $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/oldfiles/$board/thumb/$thumbName");

			$actionList .= "С доски $board перемещен файл $fileName<br>\n<img src=\"" . KOTOBA_DIR_PATH . "/oldfiles/$board/thumb/$thumbName\"><br>\n<br>\n";
			$actionCount++;
        }

	//reset($zombieFiles[$board]);
}

$smarty->assign('actionList', $actionList);
$smarty->assign('actionCount', $actionCount);
$smarty->display('RemoveFiles.tpl');
?>