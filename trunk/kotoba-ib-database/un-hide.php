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

require 'config.php';
require 'common.php';
require 'session_processing.php';
require 'events.php';
require 'error_processing.php';
require 'database_connect.php';

kotoba_setup();

function hide($user, $board, $thread, $reason) {
	$link = dbconn();
	$st = mysqli_prepare($link, "call sp_hide_thread(?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "siis", $board, $user, $thread, $reason)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
}
function unhide($user, $board, $thread) {
	$link = dbconn();
	$st = mysqli_prepare($link, "call sp_unhide_thread(?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "sii", $board, $user, $thread)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
}

if(isset($_GET['b']))
{
	if(($BOARD_NAME = CheckFormat('board', $_GET['b'])) == false)
	{
		kotoba_error(ERR_BOARD_BAD_FORMAT);
	}
}
else
{
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}
if(isset($_GET['t']))
{
    if(($THREAD_NUM = CheckFormat('thread', intval($_GET['t']))) === false)
	{
		kotoba_error(ERR_THREAD_BAD_FORMAT);
	}
}
else {
	kotoba_error(ERR_BAD_REGISTERINFO);
}
//var_dump($login);
if(!isset($_SESSION['isLoggedIn'])) { 
	kotoba_error(ERR_LOGIN_NOTREGISTERED);
}

$login = sess_get_user_settings();
if(!isset($login['userid']) || $login['userid'] <= 0) {
	kotoba_error(ERR_LOGIN_NOTREGISTERED);
}

if($action == "hide") {
	$smarty = new SmartyKotobaSetup();
	$smarty->assign('user', $login['userid']);
	$smarty->assign('board', $BOARD_NAME);
	$smarty->assign('thread', $THREAD_NUM);
	$smarty->display('hidethread.tpl');
}
elseif($action == "dohide") {
	if(isset($_GET['reason']) && strlen($_GET['reason']) > 0) {
		$reason = htmlspecialchars(strval($_GET['reason']));
		hide($login['userid'], $BOARD_NAME, $THREAD_NUM, $reason);
		header("Location: $BOARD_NAME");
	}
	else {
		kotoba_error(ERR_BAD_REGISTERINFO);
	}
}
elseif($action == "unhide") {
	unhide($login['userid'], $BOARD_NAME, $THREAD_NUM);
	header("Location: $BOARD_NAME");
}
else {
	kotoba_error(ERR_BAD_REGISTERINFO);
}
