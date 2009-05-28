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
function add_new_board($link, &$params_array) {
	$rubber = strval($params_array['rubberboard']) == 'on' ? 1 : 0;
	list($board_name, $board_description, $board_title, $bump_limit, $visible_threads, $same_upload) = 
		array(strval($params_array['board_name']), strval($params_array['board_description']),
	strval($params_array['board_title']), intval($params_array['bump_limit']),
	intval($params_array['visible_threads']), strval($params_array['same_upload']));
//	echo "$board_name, $board_description, $board_title, $bump_limit,
	//		$rubber, $visible_threads, $same_upload";
	$st = mysqli_prepare($link, "call sp_create_board(?, ?, ?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	mysqli_stmt_bind_param($st, "sssiiii", $board_name, $board_description, $board_title,
		$bump_limit, $rubber, $visible_threads, $same_upload);
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	header("Location: boards.php");
}
function save_board($link, &$params_array) {
	$rubber = strval($params_array['rubberboard']) == 'on' ? 1 : 0;
	list($id, $board_name, $board_description, $board_title, $bump_limit,
		$visible_threads, $same_upload) = 
		array(strval($params_array['id']), strval($params_array['board_name']),
			strval($params_array['board_description']),
			strval($params_array['board_title']), intval($params_array['bump_limit']),
			intval($params_array['visible_threads']), strval($params_array['same_upload']));
	$st = mysqli_prepare($link, "call sp_save_board(?, ?, ?, ?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	mysqli_stmt_bind_param($st, "isssiiii", $id, $board_name, $board_description, $board_title,
		$bump_limit, $rubber, $visible_threads, $same_upload);
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	header("Location: boards.php");
}
$link = dbconn();

$action = strval($_GET['action']);

if($action == 'new') {
	add_new_board($link, $_GET);
}
elseif($action == 'save') {
	save_board($link, $_GET);
}
else {
	$boards = get_boards($link);

	// for new board string with empty fields
	$smarty = new SmartyKotobaSetup();
	$smarty->assign('boards', $boards);
	$smarty->display('adm_boardsview.tpl');
}
mysqli_close($link);

?>
<pre>
</pre>
