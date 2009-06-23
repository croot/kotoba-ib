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


/* session_processing.php - session related routines */

function sess_setup_user($id, $posts, $lines, $threads, $pages) {
	$_SESSION['userid'] = $id;
	$_SESSION['preview_lines'] = $lines;
	$_SESSION['preview_posts'] = $posts;
	$_SESSION['preview_threads'] = $threads;
	$_SESSION['preview_pages'] = $pages;
}
