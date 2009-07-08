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

require_once('config.php');
require_once('common.php');


/* session_processing.php - session related routines */

// fucking php with register_globals. shame on you.

function sess_setup_user($id, $posts, $lines, $threads, $pages) {
	$_SESSION['isLoggedIn'] = 1;
	$_SESSION['sess_userid'] = $id;
	$_SESSION['sess_preview_lines'] = $lines;
	$_SESSION['sess_preview_posts'] = $posts;
	$_SESSION['sess_preview_threads'] = $threads;
	$_SESSION['sess_preview_pages'] = $pages;
}

function sess_get_user_settings() {
	return array(
		'userid' => $_SESSION['sess_userid'],
		'preview_lines' => $_SESSION['sess_preview_lines'],
		'preview_posts' => $_SESSION['sess_preview_posts'],
		'preview_threads' => $_SESSION['sess_preview_threads'],
		'preview_pages' => $_SESSION['sess_preview_pages'],
	);
}
function sess_get_user_id() {
	return $_SESSION['sess_userid'];
}
function sess_id() {
	return session_id();
}

function home($board) {
	header(sprintf("Location: %s/%s", KOTOBA_DIR_PATH, $board));
}
