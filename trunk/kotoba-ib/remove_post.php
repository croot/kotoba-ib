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
// Скрипт удаления сообщений.
// TODO Капча на странице ввода пароля для удаления сообщения.
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
	$smarty = new SmartyKotobaSetup($_SESSION['language'],
		$_SESSION['stylesheet']);
	// Возможно завершение работы скрипта.
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
// Проверка входных параметров.
	if(isset($_GET['post']))
	{
		$post_id = $_GET['post'];
		if(isset($_GET['password']))
		{
			$password = $_GET['password'];
		}
	}
	elseif(isset($_POST['post']))
	{
		$post_id = $_POST['post'];
		if(isset($_POST['password']))
		{
			$password = $_POST['password'];
		}
	}
	else
	{
		header('Location: http://z0r.de/?id=114');	// Это шутка.
		DataExchange::releaseResources();
		exit;
	}
	$post = posts_get_by_id_view(posts_check_number($post_id),
		$_SESSION['user']);
	$password = isset($password)
		? posts_check_password($password) : $_SESSION['password'];
// Удаление.
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups'])
		|| ($post['password'] !== null && $post['password'] === $password))
	{
		posts_delete($post['id']);
		$board = boards_get_by_id($post['board']);
		header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
	}
	else
	{
// Вывод формы ввода пароля.
		$smarty->assign('id', $post['id']);
		$smarty->assign('password', $password);
		$smarty->display('remove_post.tpl');
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