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

require 'config.php';
require 'common.php';
require 'error_processing.php';

if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/index.stat', 'a')) == false)
        kotoba_error("Ошибка. Не удалось открыть или создать файл статистики");

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

require 'database_connect.php';
require 'database_common.php';
require 'events.php';

$link = dbconn();
$smarty = new SmartyKotobaSetup();

// Получение списка досок.

$boardNames = db_get_boards($link);

if(count($boardNames) > 1) {
	$smarty->assign('BOARDS_EXIST', '');
	$smarty->assign('boardNames', $boardNames);
}


if(isset($_SESSION['isLoggedIn']))
	$smarty->assign('isLoggedIn', '');

$smarty->assign('version', '$Revision$');
$smarty->assign('date', '$Date$');

$smarty->display('index.tpl');
mysqli_close($link);
?>
<?php
/*
 * Выводит сообщение $errmsg в файл статистики $stat_file.
 */
function kotoba_stat($errmsg)
{
    global $stat_file;
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
    fclose($stat_file);
}
?>
