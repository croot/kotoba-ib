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
require_once 'exception_processing.php';

if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/stat/index.stat', 'a')) == false)
        kotoba_error("Ошибка. Не удалось открыть или создать файл статистики");

kotoba_setup();

if(!session_start())
    exit;

require_once 'database_connect.php';
require_once 'database_common.php';

$link = dbconnect();
$smarty = new SmartyKotobaSetup();

if(($ban = db_check_banned($link, ip2long($_SERVER['REMOTE_ADDR']))) !== false)
{
    $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
    $smarty->assign('reason', $ban['reason']);
    session_destroy();
    die($smarty->fetch('banned.tpl'));
}

login();

$boardNames = db_get_boards_list($link, $_SESSION['user']);
mysqli_close($link);

if (in_array('Moderators', $_SESSION['groups']))
{
    $smarty->assign('mod_panel', true);
}
elseif (in_array('Administrators', $_SESSION['groups']))
{
    $smarty->assign('adm_panel', true);
}

$smarty->assign('stylesheet', $_SESSION['stylesheet']);

if(count($boardNames) > 0)
{
	$smarty->assign('BOARDS_EXIST', true);
	$smarty->assign('boardNames', $boardNames);
}

if(isset($_SESSION['isLoggedIn']))
	$smarty->assign('isLoggedIn', '1');

$smarty->assign('version', '$Revision$');
$smarty->assign('date', '$Date$');

$smarty->display('index.tpl');
?>
