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
$board_names = db_board_get($link, $smarty);
mysqli_close($link);
if(count($board_names) > 0)
{
	$smarty->assign('boards_exist', true);
	$smarty->assign('board_names', $board_names);
}
if(in_array(Config::MOD_GROUP_NAME, $_SESSION['groups']))
    $smarty->assign('mod_panel', true);
elseif(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
    $smarty->assign('adm_panel', true);
$smarty->assign('stylesheet', $_SESSION['stylesheet']);
$smarty->assign('version', '$Revision$');
$smarty->assign('date', '$Date$');
$smarty->display('index.tpl');
?>