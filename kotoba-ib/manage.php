<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Скрипт страницы административных фукнций и фукнций модераторов.

require 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

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
                Logging::$messages['ADMIN_FUNCTIONS_MANAGE'],
                $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
        Logging::close_log();
    } elseif (is_mod()) {
        Logging::write_msg(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log',
                Logging::$messages['MOD_FUNCTIONS_MANAGE'],
                $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
        Logging::close_log();
    } else {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']
             . ' ' . PermissionException::$messages['NOT_MOD']);
    }

    if (is_mod()) {
        $smarty->assign('mod_panel', true);
    } elseif (is_admin()) {
        $smarty->assign('adm_panel', true);
    }

    DataExchange::releaseResources();
    $smarty->display('manage.tpl');
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
