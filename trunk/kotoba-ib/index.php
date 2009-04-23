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

if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/index.stat', 'a')) == false)
        die($HEAD . '<span class="error">Ошибка. Неудалось открыть или создать файл статистики.</span>' . $FOOTER);

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

require 'databaseconnect.php';

$smarty = new SmartyKotobaSetup();

// Получение списка досок.
if(($result = mysql_query('select `Name`, `id` from `boards` order by `Name`')) !== false)
{
	if(mysql_num_rows($result) != 0)
	{
		$smarty->assign('BOARDS_EXIST', '');
		$boardNames = array();
		
		while (($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false)
			$boardNames[] = $row['Name'];

		$smarty->assign('boardNames', $boardNames);
    }

	mysql_free_result($result);
}
else
{
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_BOARDS_LIST, mysql_error()));

	die($HEAD . '<span class="error">Ошибка. Невозможно получить список досок. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

if(isset($_SESSION['isLoggedIn']))
	$smarty->assign('isLoggedIn', '');

$smarty->assign('version', '$Revision$');
$smarty->assign('date', '$Date$');

$smarty->display('index.tpl');
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
