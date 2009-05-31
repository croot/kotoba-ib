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

/* admin/filetypes.php: manage supported filetypes */

require_once("../database_connect.php");
require_once("../common.php");
require_once("../events.php");
require_once("../post_processing.php");

function get_filetypes($link) {
	$types = array();
	$st = mysqli_prepare($link, "call sp_get_filetypes_ex()");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id, $image, $extension, $store_extension, $handler, $thumbnail_image);
	while(mysqli_stmt_fetch($st)) {
		array_push($types, array('id'=>$id, 'image' => $image, 'extension' => $extension, 
			'store_extension' => $store_extension, 
			'handler' => $handler, 'thumbnail_image' => $thumbnail_image));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $types;
}
function save_filetype($link, &$params_array, &$files) {
	$virtual_path = sprintf("%s/img/unknown.png", KOTOBA_DIR_PATH);
	list($id, $image, $extension, $store_extension, $handler) = 
		array(intval($params_array['id']), intval($params_array['image']),
			strval($params_array['extension']),
			strval($params_array['store_extension']), intval($params_array['handler']));
	if($handler == 1) {
		$error_message = "";
		if(!post_check_image_upload_error($files['thumbnail_image']['error'], false, "echo",
        $error_message))
		{ // upload of image failed
			kotoba_error($error_message);
		}

		$uploaded_file = $files['thumbnail_image']['tmp_name'];
		$uploaded_name = $files['thumbnail_image']['name'];
		$uploaded_parts = pathinfo($uploaded_name);
		$store_file = sprintf("%s-preview.%s", $extension, $uploaded_parts['extension']);
		$base_path = sprintf("img/%s", $store_file);
		$store_path = sprintf("%s/%s/%s", $_SERVER['DOCUMENT_ROOT'], KOTOBA_DIR_PATH,
			$base_path);
		$virtual_path = sprintf("%s/%s", KOTOBA_DIR_PATH, $base_path);
		if(!move_uploaded_file($uploaded_file, $store_path)) {
			kotoba_error(ERR_FILE_NOT_SAVED);
		}
		echo "$uploaded_file, $uploaded_name, $store_file";
	}
//	echo "$board_name, $board_description, $board_title, $bump_limit,
	//		$rubber, $visible_threads, $same_upload";
	$st = mysqli_prepare($link, "call sp_change_filetype(?, ?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	mysqli_stmt_bind_param($st, "iissis", $id, $image, $extension, $store_extension,
		$handler, $virtual_path);
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
}
/*
 * TODO: file upload error handling
 */
function add_new_filetype($link, &$params_array, &$files) {
	$virtual_path = sprintf("%s/img/unknown.png", KOTOBA_DIR_PATH);
	list($image, $extension, $store_extension, $handler) = 
		array(intval($params_array['image']), strval($params_array['extension']),
			strval($params_array['store_extension']), intval($params_array['handler']));
	if($handler == 1) {
		$error_message = "";
		if(!post_check_image_upload_error($files['thumbnail_image']['error'], false, "echo",
        $error_message))
		{ // upload of image failed
			kotoba_error($error_message);
		}

		$uploaded_file = $files['thumbnail_image']['tmp_name'];
		$uploaded_name = $files['thumbnail_image']['name'];
		$uploaded_parts = pathinfo($uploaded_name);
		$store_file = sprintf("%s-preview.%s", $extension, $uploaded_parts['extension']);
		$base_path = sprintf("img/%s", $store_file);
		$store_path = sprintf("%s/%s/%s", $_SERVER['DOCUMENT_ROOT'], KOTOBA_DIR_PATH,
			$base_path);
		$virtual_path = sprintf("%s/%s", KOTOBA_DIR_PATH, $base_path);
		if(!move_uploaded_file($uploaded_file, $store_path)) {
			kotoba_error(ERR_FILE_NOT_SAVED);
		}
		echo "$uploaded_file, $uploaded_name, $store_file";
	}
//	echo "$board_name, $board_description, $board_title, $bump_limit,
	//		$rubber, $visible_threads, $same_upload";
	$st = mysqli_prepare($link, "call sp_add_filetype(?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	mysqli_stmt_bind_param($st, "issis", $image, $extension, $store_extension, $handler, $virtual_path);
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
}
$link = dbconn();

$action = strval($_POST['action']);

if($action == 'new') {
	add_new_filetype($link, $_POST, $_FILES);
	header("Location: filetypes.php");
}
elseif($action == 'save') {
	save_filetype($link, $_POST, $_FILES);
	header("Location: filetypes.php");
}
else {
	$types = get_filetypes($link);

	// for new board string with empty fields
	$smarty = new SmartyKotobaSetup();
	$smarty->assign('types', $types);
	$smarty->display('adm_filetypes.tpl');
}
mysqli_close($link);
