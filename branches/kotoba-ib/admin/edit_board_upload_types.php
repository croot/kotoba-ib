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

/* admin/board-filetypes.php: manage board supported files */

require_once("../database_connect.php");
require_once("../common.php");

function get_filetypes($link) {
	$types = array();
	$st = mysqli_prepare($link, "call sp_get_filetypes()");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id, $extension, $handler, $thumbnail_image);
	while(mysqli_stmt_fetch($st)) {
		array_push($types, array('id'=>$id, 'extension' => $extension, 
			'handler' => $handler, 'thumbnail_image' => $thumbnail_image));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $types;
}
function link_board($link, $id, $types) {
	$st = mysqli_prepare($link, "call sp_delete_board_filetypes(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "i", $id)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	if(count($types) == 0) {
		return;
	}
	foreach($types as $type) {
		$st = mysqli_prepare($link, "call sp_add_board_filetype(?, ?)");
		if(! $st) {
			kotoba_error(mysqli_error($link));
		}
		if(! mysqli_stmt_bind_param($st, "ii", $id, intval($type))) {
			kotoba_error(mysqli_stmt_error($st));
		}
		if(! mysqli_stmt_execute($st)) {
			kotoba_error(mysqli_stmt_error($st));
		}
		mysqli_stmt_close($st);
		cleanup_link($link);
	}

}


if(isset($_GET['board_id'])) {
	$id = intval($_GET['board_id']);
}
if(isset($_POST['action'])) {
	$action = strval($_POST['action']);
}

if(! isset($id) || $id < 1) {
	kotoba_error("id not set");
}

$link = dbconn();
if(isset($action) && $action == 'link') {
	$types = $_POST['types'];
	link_board($link, $id, $types);
	header("Location: boards.php");
}
else {
	$types = get_filetypes($link);
	$smarty = new SmartyKotobaSetup();
	$smarty->assign('id', $id);
	$smarty->assign('types', $types);
	$smarty->display('adm_boardtypesview.tpl');
}
