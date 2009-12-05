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
// Скрипт редактирования банов.
require '../config.php';
require Config::ABS_PATH . '/modules/errors.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/modules/logging.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/modules/db.php';
require Config::ABS_PATH . '/modules/cache.php';
require Config::ABS_PATH . '/modules/common.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_BANS'],
					$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
				Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	elseif(in_array(Config::MOD_GROUP_NAME, $_SESSION['groups']))
		Logging::write_message(sprintf(Logging::$messages['MOD_FUNCTIONS_EDIT_BANS'],
					$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
				Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	else
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']
		 . PremissionException::$messages['NOT_MOD']);
	$bans = bans_get_all();
	date_default_timezone_set(Config::DEFAULT_TIMEZONE);
	$bans_decoded = array();
	foreach($bans as $ban)
		array_push($bans_decoded, array('id' => $ban['id'],
				'range_beg' => long2ip($ban['range_beg']),
				'range_end' => long2ip($ban['range_end']),
				'reason' => $ban['reason'],
				'untill' => $ban['untill']));
	$reload_bans = false;	// Были ли произведены изменения.
	if(isset($_POST['submited']))
	{
		// Добавление нового бана.
		if(isset($_POST['new_range_beg']) && isset($_POST['new_range_end'])
			&& isset($_POST['new_reason']) && isset($_POST['new_untill'])
			&& $_POST['new_range_beg'] != ''
			&& $_POST['new_range_end'] != ''
			&& $_POST['new_untill'] != '')
		{
			$new_range_beg = bans_check_range_beg($_POST['new_range_beg']);
			$new_range_end = bans_check_range_end($_POST['new_range_end']);
			if($_POST['new_reason'] === '')
				$new_reason = null;
			else
				$new_reason = bans_check_reason($_POST['new_reason']);
			$new_untill = bans_check_untill($_POST['new_untill']);
			bans_add($new_range_beg, $new_range_end, $new_reason,
				date('Y-m-d H:i:s', time() + $new_untill));
			$reload_bans = true;
		}
		// Удаление банов.
		foreach($bans as $ban)
			if(isset($_POST['delete_' . $ban['id']]))
			{
				bans_delete_byid($ban['id']);
				$reload_bans = true;
			}
		// Разбан заданного ip.
		if(isset($_POST['unban']) && $_POST['unban'] !== '')
		{
			// Так как начало и конец диапазона такие же ip адреса как и все.
			$ip = bans_check_range_beg($_POST['unban']);
			bans_delete_byip($ip);
			$reload_bans = true;
		}
	}
	// Вывод формы редактирования.
	if($reload_bans)
		$bans = bans_get_all();
	$bans_decoded = array();
	foreach($bans as $ban)
		array_push($bans_decoded, array('id' => $ban['id'],
				'range_beg' => long2ip($ban['range_beg']),
				'range_end' => long2ip($ban['range_end']),
				'reason' => $ban['reason'],
				'untill' => $ban['untill']));
	DataExchange::releaseResources();
	$smarty->assign('bans_decoded', $bans_decoded);
	$smarty->display('edit_bans.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>