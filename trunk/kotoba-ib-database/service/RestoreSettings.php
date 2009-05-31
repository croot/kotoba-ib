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

/*
 * Этот скрипт удаляет дублирующиеся параметры в настройках постов,
 * которые могут появится в результате экспериментов или ошибок.
 *
 * !!!ПЕРЕД ЗАПУСКОМ СКРИПТА ОБЯЗАТЕЛЬНО СДЕЛАЙТЕ РЕЗЕРВНУЮ КОПИЮ БАЗЫ ДАННЫХ!!!
 */

// TODO Сделать автоматический бекап базы или по кр. мере предупреждение.

require_once('../common.php');
require_once($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/databaseconnect.php');
$smarty = new SmartyKotobaSetup();

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

// Только для зарегистрированных пользователей.
if(!isset($_SESSION['isLoggedIn']))
{
	$smarty->assign('error', 'Ошибка. Вы не вошли в систему');
	die($smarty->fetch('RestoreSettings.tpl'));
}

if(($result = mysql_query('select `User Settings` from `users` where SID = \'' . session_id() . '\'')) != false)
{
	if(@mysql_num_rows($result) > 0)
	{
		$user = mysql_fetch_array($result, MYSQL_ASSOC);
		$User_Settings = GetSettings('user', $user['User Settings']);
		mysql_free_result($result);
	}
	else
	{
		@mysql_free_result($result);
		$smarty->assign('error', 'Ошибка. Пользователь с SID ' . session_id() . ' не найден в базе');
		die($smarty->fetch('RestoreSettings.tpl'));
    }
}
else
{
	$smarty->assign('error', 'Ошибка. Невозможно получить данные пользователя. Причина: ' . mysql_error());
	die($smarty->fetch('RestoreSettings.tpl'));
}

// Только для администраторов.
if($User_Settings['ADMIN'] !== 'Y')
{
	$smarty->assign('error', 'Ошибка. Вы не являетесь администратором');
	die($smarty->fetch('RestoreSettings.tpl'));
}

// Выберем все посты с непустыми настройками.
if(($result = mysql_query('select `id`, `thread`, `board`, `Post Settings` from `posts` where `Post Settings` is not null and `Post Settings` <> \'\'')) != false)
{
	if(@mysql_num_rows($result) === 0 || @mysql_num_rows($result) === false)
	{
		$smarty->assign('postsCount', 0);
		$smarty->assign('affectedCount', 0);
		@mysql_free_result($result);
		die($smarty->fetch('RestoreSettings.tpl'));
	}
}
else
{
	$smarty->assign('error', 'Ошибка. Неудалось получить настройки постов. Причина: ' . mysql_error());
	die($smarty->fetch('RestoreSettings.tpl'));
}

$posts_to_update = array();
$need_update = false;
$j = 0;
$temp = array();

// Для каждого поста проверим, нет ли в его настройках дублирующихся параметров.
// Если есть, то данных этих постов и уникальный набор параметров из настроек
// будут сохранены в $posts_to_update.
while(($post = mysql_fetch_array($result, MYSQL_ASSOC)) != false)
{
	$posts_to_update[$j]['id'] = $post['id'];
	$posts_to_update[$j]['thread'] = $post['thread'];
	$posts_to_update[$j]['board'] = $post['board'];
	$posts_to_update[$j]['settings'] = '';
	$settings_array = preg_split("/\n/", $post['Post Settings'], -1, PREG_SPLIT_NO_EMPTY);
	
	for($i = 0; $i < count($settings_array); $i++)
	{
		$pair = explode(':', $settings_array[$i]);
		
		if(!in_array($pair[0], array_values($temp)))
		{
			$temp[] = $pair[0];
			$posts_to_update[$j]['settings'] .= "$pair[0]:$pair[1]\n";
		}
		else
			$need_update = true;
	}

	unset($temp);
	$temp = array();
	
	if(!$need_update)
		unset($posts_to_update[$j]);
	else
	{
		$need_update = false;
		$j++;
	}
}

$affectedRows = 0;

if(count($posts_to_update) > 0)
{
	if(mysql_query('start transaction') == false)
	{
		$smarty->assign('error', 'Ошибка. Невозможно начать транзакцию. Причина: ' . mysql_error());
		die($smarty->fetch('RestoreSettings.tpl'));
	}

	for($i = 0; $i < count($posts_to_update); $i++)
	{
		if(mysql_query("update `posts` set `Post Settings` = '{$posts_to_update[$i]['settings']}' where `id` = {$posts_to_update[$i]['id']}" . 
			" and `thread` = {$posts_to_update[$i]['thread']} and `board` = {$posts_to_update[$i]['board']}") == false)
		{
			$temp = mysql_error();
			mysql_query('rollback');
			$smarty->assign('error', "Ошибка. Невозможно обновить настройки поста. Причина: $temp");
			die($smarty->fetch('RestoreSettings.tpl'));
        }
		
		$affectedRows += mysql_affected_rows();
	}

	if(mysql_query('commit') == false)
	{
		$temp = mysql_error();
		mysql_query('rollback');
		$smarty->assign('error', "Ошибка. Невозможно завершить транзакцию. Причина: $temp");
		die($smarty->fetch('RestoreSettings.tpl'));
	}
}

$smarty->assign('postsCount', @mysql_num_rows($result));
$smarty->assign('affectedCount', $affectedRows);
@mysql_free_result($result);
die($smarty->fetch('RestoreSettings.tpl'));
?>