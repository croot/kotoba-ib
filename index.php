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

require 'kwrapper.php';

kotoba_setup($link, $smarty);
$boards = db_boards_get_allowed($_SESSION['user'], $link, $smarty);
if(count($boards) > 0)
{
	$categories = db_categories_get($link, $smarty);
	foreach($categories as $category)
		foreach($boards as &$board)
			if($board['category'] == $category['id'])
				$board['category'] = $category['name'];
	$smarty->assign('boards_exist', true);
	$smarty->assign('boards', $boards);
}
mysqli_close($link);
if(in_array(Config::MOD_GROUP_NAME, $_SESSION['groups']))
    $smarty->assign('mod_panel', true);
elseif(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
    $smarty->assign('adm_panel', true);
$smarty->assign('stylesheet', $_SESSION['stylesheet']);
$smarty->assign('version', '$Revision$');
$smarty->assign('date', '$Date$');
$smarty->display('index.tpl');
?>