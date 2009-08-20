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
require_once 'config.php';
require_once 'common.php';

kotoba_setup($link, $smarty);
$boardNames = db_get_boards_list($link, $_SESSION['user']);
mysqli_close($link);

if(count($boardNames) > 0)
{
	$smarty->assign('BOARDS_EXIST', true);
	$smarty->assign('boardNames', $boardNames);
}

if(in_array('Moderators', $_SESSION['groups']))
    $smarty->assign('mod_panel', true);
elseif(in_array('Administrators', $_SESSION['groups']))
    $smarty->assign('adm_panel', true);

$smarty->assign('stylesheet', $_SESSION['stylesheet']);
$smarty->assign('version', '$Revision$');
$smarty->assign('date', '$Date$');
$smarty->display('index.tpl');
?>