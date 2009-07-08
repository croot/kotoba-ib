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

/* removepost.php - remove post from kotoba by user */

require 'config.php';
require 'common.php';
require 'session_processing.php';
require 'events.php';
require 'error_processing.php';
require 'database_connect.php';

kotoba_setup();

function get_post_userinfo($board, $postnumber) {
	$link = dbconn();
	$st = mysqli_prepare($link, "call sp_get_post_userinfo(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "si", $board, $postnumber)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $password, $userid, $sessionid);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return null;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	mysqli_close($link);

	return array('password' => $password, 'userid' => $userid, 'sessionid' => $sessionid);
}

function delete_post($board, $postnumber) {
	$link = dbconn();
//	echo "sp_delete_post($board, $postnumber)\n";
	$st = mysqli_prepare($link, "call sp_delete_post(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "si", $board, $postnumber)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	mysqli_close($link);
}

if(isset($_GET['b']))
{
	if(($BOARD_NAME = CheckFormat('board', strval($_GET['b']))) == false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_BOARD_BAD_FORMAT);
		kotoba_error(ERR_BOARD_BAD_FORMAT);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_BOARD_NOT_SPECIFED);
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

// get post number
if(isset($_GET['r']))
{
	if(($POST_NUM = CheckFormat('thread', intval($_GET['r']))) === false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_POST_BAD_FORMAT);

		kotoba_stat(ERR_POST_BAD_FORMAT);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_POST_NOT_SPECIFED);
	kotoba_stat(ERR_POST_NOT_SPECIFED);
}

$settings = get_post_userinfo($BOARD_NAME, $POST_NUM);

$sessionid = sess_id();

// session checking
if($sessionid == $settings['sessionid']) {
	delete_post($BOARD_NAME, $POST_NUM);
	home($BOARD_NAME);
}
// userid checking
if(isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] > 0) {
	$userid = sess_get_user_id();
	if($userid == $settings['userid']) {
		delete_post($BOARD_NAME, $POST_NUM);
		home($BOARD_NAME);
	}
}
// password checking
if(isset($_POST['action']) && $_POST['action'] == "password") {
	if(($REM_PASS = CheckFormat('pass', $_POST['Message_pass'])) === false) {
		kotoba_error(ERR_PASS_BAD_FORMAT);
	}
	if($REM_PASS == $settings['password']) {
		delete_post($BOARD_NAME, $POST_NUM);
		home($BOARD_NAME);
	}
	else {
		kotoba_error(ERR_WRONG_PASSWORD);
	}
}

if(isset($_COOKIE['rempass']))
{
	if(($cookiepass = CheckFormat('pass', $_COOKIE['rempass'])) === false)
	{
		kotoba_error(ERR_PASS_BAD_FORMAT);
	}
}
else {
	$cookiepass = "";
}

$smarty = new SmartyKotobaSetup();
$smarty->assign('pass', $cookiepass);
$smarty->assign('board', $BOARD_NAME);
$smarty->assign('post', $POST_NUM);
$smarty->display('removepost.tpl');

?>
