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
/*
 * en: common functions may used by all kotoba modules
 * ru: Интерфейс работы с базой данных.
 */

require_once 'config.php';
require_once 'exception_processing.php';

/*
 * Проверяет, забанен ли узел с адресом $ip.
 */
function db_check_banned($link, $ip)
{
//    mysqli_query($link, "call sp_ban($ip, $ip, 'Sorc banned', '2009-07-29 16:51:00')");
//    mysqli_query($link, "call sp_ban(" . ip2long('127.0.0.0') . ", " . ip2long('127.255.255.255') . ", 'local ban', '2009-07-07 16:58:00')");
//    echo "\n" . mysqli_error($link);
//    exit;
    if(($result = mysqli_query($link, "call sp_check_ban($ip)")) == false)
        kotoba_error(mysqli_error($link));
    
    if(($count = mysqli_affected_rows($link)) > 0)
    {
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        cleanup_link($link);
        return array('range_beg' => $row['range_beg'],'range_end' => $row['range_end'],'untill' => $row['untill'],'reason' => $row['reason']);
    }
    else
    {
        mysqli_free_result($result);
        cleanup_link($link);
        return false;
    }
}

/*
 * cleanup all results on link. useful when stored procedure used.
 * no returns
 * argumnets:
 * $link - database link
 */
function cleanup_link($link)
{
	do
    {
		if(($result = mysqli_use_result($link)) != false)
			mysqli_free_result($result);
	}
    while(mysqli_next_result($link));
}

/*
 * Возвращает доски, видимые пользователю $user.
 */
function db_get_boards_list($link, $user)
{
	$boards = array();

    if(($result = mysqli_query($link, "call sp_get_boards_list($user)")) == false)
        kotoba_error(mysqli_error($link));

    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($boards, $row);

    mysqli_free_result($result);
	cleanup_link($link);
	return $boards;
}

/* db_get_boards: get all allowed to use boards
 * returns array with fields:
 * 'id', 'board_name', 'board_description'
 * arguments:
 * $link - database link (mysqli)
 */
/*function db_get_boards($link) {
	$boards = array();
	$st = mysqli_prepare($link, "call sp_get_boards()");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $cid, $id, $board_name, $board_description);
	while(mysqli_stmt_fetch($st)) {
		array_push($boards, array('cid' => $cid, 'id' => $id,
			'board_name' => $board_name, 'board_description' => $board_description
			));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $boards;
}*/

/* db_get_board_id: get board id by name
 * returns board id (positive) on success, otherwise return -1
 * arguments:
 * $link - database link
 * $board_name - board name
 */

function db_get_board_id($link, $board_name) {
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

/* db_get_pages: get pages quantity
 * remark: on board preview threads splitted on pages
 * return number of pages (0 or more), -1 on error
 * arguments:
 * $link - database link
 * $boardid - board id
 * $threads - number of threads on page
 */

function db_get_pages($link, $userid, $boardid, $threads) {
	$st = mysqli_prepare($link, "select get_pages(?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "iii", $userid, $boardid, $threads)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $pages);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $pages;
}

/* db_get_board: get all board settings
 * return array with fields:
 * id, board_description, board_title, bump_limit, rubber_board, visible_threads, same_upload
 * arguments:
 * $link - database link
 * $board_name - board name
 */

function db_get_board($link, $board_name) {
	$st = mysqli_prepare($link, "call sp_get_board(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "s", $board_name)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id, $board_description, $board_title,
		$bump_limit, $rubber_board, $visible_threads, $same_upload);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return array();
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return array('id' => $id, 'board_description' => $board_description,
		'board_title' => $board_title, 'bump_limit' => $bump_limit,
		'rubber_board' => $rubber_board, 'visible_threads' => $visible_threads,
		'same_upload' => $same_upload);
}

/* db_get_post_count: get post count on board
 * return 0 or more on success, -1 on error
 * arguments:
 * $link - database link
 * $board_id - board id
 */

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

/* db_board_bumplimit: get board bumplimit
 * returns 0 or more if success, otherwise return -1
 * arguments:
 * $link - database link
 * $board_id - board id
 */

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

/* db_get_post: get post
 * return array of arrays:
 * [0] is post itself, array fields:
 * post_number, name, email, subject, text, date_time, sage
 * XXX: date_time formatted internally by this function
 * [1] and more is uploads linked with this post. fileds are:
 * is_image, file, size, file_name, file_w, file_h, thumbnail, thumbnail_w, thumbnail_h
 * arguments:
 * $link - database link
 * $board_id - board id
 * $post_number - post number
 */

function db_get_post($link, $board_id, $post_number) {
	$post = array();
	$uploads = array();
	$query = sprintf("call sp_get_post(%d, %d)", $board_id, $post_number);

	if(mysqli_multi_query($link, $query)) {
		$count = 0;
		do {
			if($result = mysqli_store_result($link)) {
				if($count == 0) {
					$row = mysqli_fetch_assoc($result);
					$row['date_time'] = strftime(KOTOBA_DATETIME_FORMAT,
						$row['date_time']);
					$post = $row;
				}
				else {
					while($row = mysqli_fetch_assoc($result)) {
						array_push($uploads, $row);
					}
				}
				// sometimes it throw warning
				@mysql_free_result($result);
			}
			$count ++;
		} while(mysqli_next_result($link));
	}
	else {
		kotoba_error(mysqli_error($link));
	}
	cleanup_link($link);
	return array($post, $uploads);
}

/* db_get_thread: get thread information
 * return array with fileds:
 * original_post_num, messages, bump_limit, sage
 * arguments:
 * $link - database link
 * $board_id - board id
 * $thread_num - thread number (number of open post)
 */
function db_get_thread($link, $board_id, $thread_num) {
	$st = mysqli_prepare($link, "call sp_get_thread_info(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "ii", $board_id, $thread_num)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $original_post_num, $messages, $bump_limit, $sage);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return array();
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return array('original_post_num' => $original_post_num, 'messages' => $messages,
		'bump_limit' => $bump_limit, 'sage' => $sage);
}

/* db_get_board_types: get board types (filetypes which allowed to upload on this board)
 * return array where key is name of supported extension
 * arguments:
 * $link - database link
 * $boardid - board id
 */

function db_get_board_types($link, $boardid) {
	$types = array();
	$st = mysqli_prepare($link, "call sp_get_board_filetypes(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "i", $boardid)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $extension);
	while(mysqli_stmt_fetch($st)) {
		$types[$extension] = 1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $types;
}
?>
