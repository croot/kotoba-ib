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
// Скрипт удаления закреплений файлов за сообщением.
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
// Проверка входных параметров и получение данных.
	if(isset($_GET['post']))
	{
		$post_id = posts_check_id($_GET['post']);
		$password = isset($_GET['password'])
			? posts_check_password($_GET['password']) : $_SESSION['password'];
	}
	elseif(isset($_POST['post']))
	{
		$post_id = posts_check_id($_POST['post']);
		$password = isset($_POST['password'])
			? posts_check_password($_POST['password']) : $_SESSION['password'];
	}
	else
	{
		header('Location: http://z0r.de/?id=114');	// Это шутка.
		DataExchange::releaseResources();
		exit;
	}
	$post = posts_get_visible_by_id($post_id, $_SESSION['user']);
// Удаление.
	if(is_admin()
		|| ($post['password'] !== null && $post['password'] === $password))
	{
		posts_uploads_delete_by_post($post['id']);
	}
	header('Location: ' . Config::DIR_PATH . "/{$post['board_name']}/");
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