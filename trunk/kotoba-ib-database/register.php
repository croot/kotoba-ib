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

/* register.php - register user in kotoba */

require_once('config.php');
require_once('common.php');
require_once('database_connect.php');
require_once('error_processing.php');
require_once('events.php');
require_once('session_processing.php');
//require('database_common.php');

kotoba_setup();

/* get_user_registerinfo: gather data from POST'ed registration info
* returns gathered data as array
* arguments:
*  $POST - pointer to array of POST variables
*/
function get_user_registerinfo(&$POST) {
//	var_dump($POST);
	$result = array();
	if(isset($POST['keyword']) && strlen($POST['keyword']) > 0) {
		array_push($result, RawUrlEncode($POST['keyword']));
	}
	else {
		array_push($result, '');
	}
	if(isset($POST['posts'])) { // && is_int($POST['posts'])) {
		array_push($result, intval($POST['posts']));
	}
	else {
		array_push($result, KOTOBA_POSTS_PREVIEW);
	}
	if(isset($POST['lines'])) { // && is_int($POST['lines'])) {
		array_push($result, intval($POST['lines']));
	}
	else {
		array_push($result, KOTOBA_LONGPOST_LINES);
	}
	if(isset($POST['threads'])) { // && is_int($POST['threads'])) {
		array_push($result, intval($POST['threads']));
	}
	else {
		array_push($result, KOTOBA_THREADS_ONPAGE);
	}
	if(isset($POST['pages'])) {// && is_int($POST['pages'])) {
		array_push($result, intval($POST['pages']));
	}
	else {
		array_push($result, KOTOBA_PAGES_ONBOARD);
	}

	return $result;
}

/* register_user: register user in database
 * return user id
 * arguments:
 * $keyword_hash - user keywoard hash
 * $posts - posts in preview
 * $lines - lines in poreview of big post
 * $threads - threads on page in preview
 * $pages - pages in preview
 */
function register_user($keyword_hash, $posts, $lines, $threads, $pages) {
	$link = dbconn();
	$st = mysqli_prepare($link, "call sp_add_user(?, ?, ?, ?, ?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "siiiiiiii", $keyword_hash, $posts, $lines, $threads, $pages,
		$KOTOBA_POSTS_PREVIEW, $KOTOBA_LONGPOST_LINES, $KOTOBA_THREADS_ONPAGE, $KOTOBA_PAGES_ONBOARD)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		$errno = mysqli_stmt_errno($st);
		if($errno == 1062) { // dublicate entry: user exists
			kotoba_error(ERR_USEREXISTS);
		}
		else {
			kotoba_error(mysqli_stmt_error($st));
		}
	}
	mysqli_stmt_bind_result($st, $userid);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);

	cleanup_link($link);
	mysqli_close($link);

	return $userid;
}

/* update_user: update user registration
 * return nothing
 * arguments:
 * $id - user id
 * $posts - posts in preview
 * $lines - lines in poreview of big post
 * $threads - threads on page in preview
 * $pages - pages in preview
 */
function update_user($id, $posts, $lines, $threads, $pages) {
	$link = dbconn();
	$st = mysqli_prepare($link, "call sp_change_user(?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "siiii", $id, $posts, $lines, $threads, $pages)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_close($st);

	cleanup_link($link);
	mysqli_close($link);
	return true;
}

$smarty = new SmartyKotobaSetup();

// show keyword line by default
$smarty->assign('keyword', 1);

if(isset($_SESSION['isLoggedIn']) && isset($_SESSION['userid'])) {
	$smarty->assign('keyword', 0);
	$smarty->assign('posts', $_SESSION['preview_posts']);
	$smarty->assign('lines', $_SESSION['preview_lines']);
	$smarty->assign('threads', $_SESSION['preview_threads']);
	$smarty->assign('pages', $_SESSION['preview_pages']);
}
else {
	$smarty->assign('keyword', 1);
	$smarty->assign('posts', KOTOBA_POSTS_PREVIEW);
	$smarty->assign('lines', KOTOBA_LONGPOST_LINES);
	$smarty->assign('threads', KOTOBA_THREADS_ONPAGE);
	$smarty->assign('pages', KOTOBA_PAGES_ONBOARD);
}

if(isset($_POST['keyword']) && strlen($_POST['keyword']) > 0) 
{ // keyword sent
	list($keyword, $posts, $lines, $threads, $pages) = get_user_registerinfo($_POST);
	$keyword_length = strlen($keyword);
	echo "$keyword, $posts, $lines, $threads, $pages";
	if($keyword_length >= 16 && $keyword_length <= 32) 
	{ // keywoard length is ok
		if($posts <= 0 || $lines <= 0 || $threads <= 0 || $pages <= 0) {
			$smarty->assign('form', 1);
			$smarty->assign('message', ERR_BAD_REGISTERINFO);
			$smarty->display('register.tpl');
			exit;
		}
		$keyword_hash = md5($keyword);
		$id = register_user($keyword_hash, $posts, $lines, $threads, $pages);
		if($id > 0) 
		{ // registration successful
			sess_setup_user($id, $posts, $lines, $threads, $pages);
			$smarty->assign('form', 0);
			$smarty->assign('message', LOGIN_UPDATED);
			$smarty->display('register.tpl');
			exit;
		}
	}
	else
	{ // keyword too short or too long
		$smarty->assign('form', 1);
		$smarty->assign('message', ERR_BADKEYWORD);
		$smarty->display('register.tpl');
		exit;
	}
}
elseif(isset($_POST['keyword']) && strlen($_POST['keyword']) <= 0) 
{ // keyword is sent but it too small
	$smarty->assign('form', 1);
	$smarty->assign('message', ERR_BADKEYWORD);
	$smarty->display('register.tpl');
	exit;
}
elseif(isset($_SESSION['isLoggedIn']) && isset($_SESSION['userid']) && isset($_POST['posts'])) 
{ // registered user updating info
	$smarty->assign('keyword', 0);
	$id = intval($_SESSION['userid']);
	list($stubkeyword, $posts, $lines, $threads, $pages) = get_user_registerinfo($_POST);
	if($posts <=0 || $lines <= 0 || $threads <=0 || $pages <= 0) {
		$smarty->assign('form', 1);
		$smarty->assign('message', ERR_BAD_REGISTERINFO);
		$smarty->display('register.tpl');
		exit;
	}
	update_user($id, $posts, $lines, $threads, $pages);
	sess_setup_user($id, $posts, $lines, $threads, $pages);
	$smarty->assign('form', 0);
	$smarty->assign('message', LOGIN_UPDATED);
	$smarty->display('register.tpl');
	exit;
}
else {
	$smarty->assign('form', 1);
	$smarty->display('register.tpl');
	exit;
}
// vim: set encoding=utf-8:
?>
