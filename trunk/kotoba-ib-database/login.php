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

/* login.php - login user */

require 'config.php';
require 'common.php';
require 'error_processing.php';
require 'database_connect.php';
require 'events.php';
require 'session_processing.php';

kotoba_setup();
/* login - check user in database and get user settings if it exsist
 * returns null if not registered, otherwise return array with settings
 * arguments: 
 * $authkey - auth user key (hash)
 */
function login($authkey) {
//	echo $authkey . "<br>";
	$link = dbconn();
	$st = mysqli_prepare($link, "call sp_login_user(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "s", $authkey)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_bind_result($st, $id, $preview_posts, $preview_lines, $preview_threads, $preview_pages)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		mysqli_close($link);
		return null;
	}

	mysqli_stmt_close($st);

	cleanup_link($link);
	mysqli_close($link);
	return array($id, $preview_posts, $preview_lines, $preview_threads, $preview_pages);
}

$smarty = new SmartyKotobaSetup();

if(isset($_SESSION['isLoggedIn']) && isset($_SESSION['userid'])) 
{ // user already logged in
	$smarty->assign('form', 0);
	$smarty->assign('message', LOGIN_ALREADY);
	$smarty->display('login.tpl');
	exit;
}
if(isset($_POST['Keyword']))
{ // user sent register info
	$keyword_code   = RawUrlEncode($_POST['Keyword']);
	$keyword_length = strlen($keyword_code);
	if($keyword_length >= 16 && $keyword_length <= 32 && strpos($keyword_code, '%') === false)
	{ // keyword length not too small and not too big
		$keyword_hash = md5($keyword_code);
		if($login = login($keyword_hash)) { // user exists
			list($id, $posts, $lines, $threads, $pages) = $login;
			sess_setup_user($id, $posts, $lines, $threads, $pages);
			$smarty->assign('message', LOGIN_SUCCESSFULY);
			$smarty->assign('form', 0);
			$smarty->display('login.tpl');
			exit;
		}
		else { // user not registered
			$smarty->assign('message', ERR_LOGIN_NOTREGISTERED);
			$smarty->assign('form', 1);
			$smarty->display('login.tpl');
			exit;
		}
	}
	else { // bad keywoard length
		$smarty->assign('message', ERR_BADKEYWORD);
		$smarty->assign('form', 1);
		$smarty->display('login.tpl');
		exit;
	}
}
else {
	$smarty->assign('form', 1);
	$smarty->display('login.tpl');
}
// vim: set encoding=utf-8:
?>
