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

require_once("../database_connect.php");
require_once("../database_common.php");
require_once("../common.php");

function get_categories($link) {
	$categories = array();
	$st = mysqli_prepare($link, "call sp_get_categories_ex()");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $cid, $order, $cname);

	while(mysqli_stmt_fetch($st)) {
		array_push($categories, array('cid' => $cid, 'corder' => $order, 'cname' => $cname));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $categories;
}

function add_new_category($link, &$params_array) {
	if(!isset($params_array['cname']) || strlen($params_array['cname']) == 0) {
		kotoba_error("empty data set");
	}
	list($order, $name) = array(
		intval($params_array['corder']),
		strval($params_array['cname']));

	$st = mysqli_prepare($link, "call sp_create_category(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	mysqli_stmt_bind_param($st, "is", $order, $name);
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	cleanup_link($link);
}


$link = dbconn();
if(isset($_GET['action'])) {
	$action = strval($_GET['action']);
}

if(isset($action) && $action == 'new') {
	add_new_category($link, $_GET);
	header("Location: boards.php");
}
else {
	$categories = get_categories($link);

	$smarty = new SmartyKotobaSetup();
	$smarty->assign('categories', $categories);
	$smarty->display('adm_categories.tpl');
}
mysqli_close($link);
