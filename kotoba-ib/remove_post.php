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
// Скрипт удаления сообщений и нитей.
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
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	header("Cache-Control: private");						// Fix for Firefox.
// Проверка входных параметров.
	if(isset($_GET['board']) && isset($_GET['post']))
	{
		$board = boards_get_specifed_byname(boards_check_name($_GET['board']));
		$post = posts_get_specifed_view_bynumber($board['id'],
			posts_check_number($_GET['post']), $_SESSION['user']);
		$password = $_SESSION['rempass'];
	}
	elseif(isset($_POST['board']) && isset($_POST['post'])
		&& isset($_POST['message_pass']))
	{
		$board = boards_get_specifed_byname(boards_check_name($_POST['board']));
		$post = posts_get_specifed_view_bynumber($board['id'],
			posts_check_number($_POST['post']), $_SESSION['user']);
		$password = posts_check_password($_POST['message_pass']);
	}
	else
	{
		// Вы троллите нас, мы троллим вас :3
		header('Location: http://z0r.de/?id=114');
		DataExchange::releaseResources();
		exit;
	}
	if($post['password'] !== null && $post['password'] === $password)
	{
		posts_delete($post['id']);
		header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
		DataExchange::releaseResources();
		exit;
	}
// Вывод формы ввода пароля.
	DataExchange::releaseResources();
	$smarty->assign('board_name', $board['name']);
	$smarty->assign('post_num', $post['number']);
	$smarty->assign('password', $password);
	$smarty->display('remove_post.tpl');
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>