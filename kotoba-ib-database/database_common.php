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


/* database_common.php: common functions may used by all kotoba modules */
function db_get_boards($link) {
	$boards = array();
	$st = mysqli_prepare($link, "call sp_get_boards()");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id, $board_name, $board_description);
	while(mysqli_stmt_fetch($st)) {
		array_push($boards, array('id' => $id,
			'board_name' => $board_name, 'board_description' => $board_description
			));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $boards;
}

function db_get_board_id($link, $board_name) {
	$boards = array();
	$st = mysqli_prepare($link, "call sp_get_board_id(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "s", $board_name)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $id;
}
function db_get_post_count($link, $board_id) {
	$boards = array();
	$st = mysqli_prepare($link, "call sp_get_board_post_count(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "i", $board_id)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $count);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $count;
}
function db_board_bumplimit($link, $board_id) {
	$st = mysqli_prepare($link, "call sp_get_board_bumplimit(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "i", $board_id)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $bumplimit);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $bumplimit;
}
?>
