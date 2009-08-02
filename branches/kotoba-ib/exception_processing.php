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
/* 
 * Скрипт обработки исключительных ситуаций.
 */

require_once 'config.php';
require_once 'common.php';

/*
 * kotoba_error: show error message and exit
 * returns nothing
 * arguments:
 * $error_message is custom error message
 */
function kotoba_error($error_message) {
	$smarty = new SmartyKotobaSetup();
	if(isset($error_message) && mb_strlen($error_message) > 0)
		$smarty->assign('error_message', $error_message);   //error message not empty
	else
		$smarty->assign('error_message', 'Unknown error');
	die($smarty->fetch('error.tpl'));
}

/*
 * Выводит сообщение $errmsg в файл статистики $stat_file и закрывает его.
 */
function kotoba_stat($errmsg, $stat_file)
{
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
    fclose($stat_file);
}
?>
