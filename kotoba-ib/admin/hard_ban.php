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
// Скрипт для блокировки диапазона IP адресов в фаерволе.

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    if (($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if (($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    if (is_admin()) {
        Logging::write_msg(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log',
            Logging::$messages['ADMIN_FUNCTIONS_EDIT_BANS'],
            $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
    } elseif (is_mod()) {
        Logging::write_msg(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log',
            Logging::$messages['MOD_FUNCTIONS_EDIT_BANS'],
            $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
    } else {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN'] . PermissionException::$messages['NOT_MOD']);
    }

    $new_range_beg = bans_check_range_beg($_POST['new_range_beg']);
    $new_range_end = bans_check_range_end($_POST['new_range_end']);
    hard_ban_add(long2ip($new_range_beg), long2ip($new_range_end));

    DataExchange::releaseResources();
	$smarty->display('hard_ban.tpl');
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>