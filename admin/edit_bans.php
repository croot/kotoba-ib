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

require '../kwrapper.php';
require_once Config::ABS_PATH . '/lang/' . Config::LANGUAGE . '/logging.php';

kotoba_setup($link, $smarty);
/*
 * Редактирование банов только для администраторов и модераторов.
 */
if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'],
			Logmsgs::$messages['EDIT_BANS'], $_SESSION['user'],
			$_SERVER['REMOTE_ADDR']), Logmsgs::open_logfile(Config::ABS_PATH .
				'/log/' . basename(__FILE__) . '.log'));
}
elseif(in_array(Config::MOD_GROUP_NAME, $_SESSION['groups']))
{
	kotoba_log(sprintf(Logmsgs::$messages['MOD_FUNCTIONS'],
			Logmsgs::$messages['EDIT_BANS'], $_SESSION['user'],
			$_SERVER['REMOTE_ADDR']), Logmsgs::open_logfile(Config::ABS_PATH .
				'/log/' . basename(__FILE__) . '.log'));
}
else
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'] . ' ' .
		Errmsgs::$messages['NOT_MOD'], $smarty,
		basename(__FILE__) . ' ' . __LINE__);
}
$bans = db_bans_get($link, $smarty);	// Баны.
date_default_timezone_set('Europe/Moscow');
$bans_decoded = array();
foreach($bans as $ban)
	array_push($bans_decoded, array('id' => $ban['id'],
			'range_beg' => long2ip($ban['range_beg']),
			'range_end' => long2ip($ban['range_end']),
			'reason' => $ban['reason'],
			'untill' => $ban['untill']));
$reload_bans = false;					// Были ли произведены изменения.
/*
 * Добавление бана.
 */
if(isset($_POST['submited']) &&
	isset($_POST['new_range_beg']) &&
	isset($_POST['new_range_end']) &&
	isset($_POST['new_reason']) &&
	isset($_POST['new_untill']) &&
	$_POST['new_range_beg'] !== '' &&
	$_POST['new_range_end'] !== '' &&
	$_POST['new_untill'] !== '')
{
	if(($new_range_beg = ip2long($_POST['new_range_beg'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['RANGE_BEG'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_range_end = ip2long($_POST['new_range_end'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['RANGE_END'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	if($_POST['new_reason'] === '')
		$new_reason = '';
	elseif(($new_reason = check_format('reason', $_POST['new_reason'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BAN_REASON'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_untill = check_format('id', $_POST['new_untill'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['BAN_UNTILL'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	db_bans_add($new_range_beg, $new_range_end, $new_reason, date('Y-m-d H:i:s', time() + $new_untill), $link, $smarty);
	$reload_bans = true;
}
/*
 * Удаление банов.
 */
if(isset($_POST['submited']))
	foreach($bans as $ban)
		if(isset($_POST['delete_' . $ban['id']]))
		{
			db_bans_delete($ban['id'], $link, $smarty);
			$reload_bans = true;
		}
/*
 * Разбан заданного ip.
 */
if(isset($_POST['submited']) && isset($_POST['unban']) && $_POST['unban'] !== '')
{
	if(($ip = ip2long($_POST['unban'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['IP'],
			$smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	db_bans_unban($ip, $link, $smarty);
	$reload_bans = true;
}
/*
 * Получение обновлённого списка банов, если нужно. Вывод формы редактирования.
 */
if($reload_bans)
	$bans = db_bans_get($link, $smarty);
$bans_decoded = array();
foreach($bans as $ban)
	array_push($bans_decoded, array('id' => $ban['id'],
			'range_beg' => long2ip($ban['range_beg']),
			'range_end' => long2ip($ban['range_end']),
			'reason' => $ban['reason'],
			'untill' => $ban['untill']));
mysqli_close($link);
$smarty->assign('bans_decoded', $bans_decoded);
$smarty->display('edit_bans.tpl');
?>