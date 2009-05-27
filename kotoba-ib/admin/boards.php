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

/* admin/boards.php: manage boards */

require_once("../database_connect.php");
require_once("../common.php");

function get_boards($link) {
	$boards = array();
	$st = mysqli_prepare($link, "call sp_get_boards_ex()");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id, $board_name, $board_description, $board_title, $threads, 
	$bump_limit, $rubber_board, $visible_threads, $same_upload);
	while(mysqli_stmt_fetch($st)) {
		array_push($boards, array('id' => $id,
			'board_name' => $board_name, 'board_description' => $board_description,
			'board_title' => $board_title, 'threads' => $threads, 
			'bump_limit' => $bump_limit, 'rubber_board' => $rubber_board,
			'visible_threads' => $visible_threads, 'same_upload' => $same_upload));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $boards;
}
$link = dbconn();

$boards = get_boards($link);

// for new string
$smarty = new SmartyKotobaSetup();
$smarty->assign('boards', $boards);
$smarty->display('adm_boardsview.tpl');
mysqli_close($link);

?>
<pre>
</pre>
