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
require 'events.php';


if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/login.stat', 'a')) == false)
        kotoba_error("Ошибка. Неудалось открыть или создать файл статистики.");

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

$smarty = new SmartyKotobaSetup();
if(isset($_SESSION['isLoggedIn'])) {
	$smarty->assign('form', 0);
	$smarty->assign('message', LOGIN_ALREADY);
	$smarty->display('login.tpl');
	exit;
}

if(isset($_POST['Keyword']))
{
	$keyword_code   = RawUrlEncode($_POST['Keyword']);
	$keyword_length = strlen($keyword_code);

	if($keyword_length >= 16 && $keyword_length <= 32 && strpos($keyword_code, '%') === false)
	{
		$keyword_hash = md5($keyword_code);
		require 'databaseconnect.php';
		$sql = sprintf("select id from users where `Key` = '%s'", $keyword_hash);
		if(($result = mysql_query($sql)) != false)
		{
			if(@mysql_num_rows($result) == 0) {
				$smarty->assign('message', ERR_LOGIN_NOTREGISTERED);
				$smarty->assign('form', 0);
				$smarty->display('login.tpl');
				exit;
			}
			else
			{
				$sql = sprintf("update users set SID = '%s' where `Key` = '%s'",
					session_id(), $keyword_hash);
				if(mysql_query($sql) == false)
				{
					if(KOTOBA_ENABLE_STAT)
						kotoba_stat(sprintf(ERR_UPDATE_USER_SID, mysql_error()));

					kotoba_error(sprintf(ERR_UPDATE_USER_SID, mysql_error()));
				}
				else
				{
					$_SESSION['isLoggedIn'] = session_id();
					$smarty->assign('message', LOGIN_SUCCESSFULY);
					$smarty->assign('form', 0);
					$smarty->display('login.tpl');
					exit;
				}
			}
		}
		else
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(sprintf(ERR_USER_DATA, mysql_error()));

			kotoba_error(sprintf(ERR_USER_DATA, mysql_error()));
		}
	}
	else {
		$smarty->assign('message', ERR_BADKEYWORD);
		$smarty->assign('form', 0);
		$smarty->display('login.tpl');
		exit;
	}
}

//$smarty->assign('message', 'Ошибка. Вы не зарегистрированы.');
$smarty->assign('form', 1);
$smarty->display('login.tpl');
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
