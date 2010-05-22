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
// Скрипт страницы административных фукнций и фукнций модераторов.
// Java CC Done.
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

    // Возможно завершение работы скрипта.
    bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));

    if (is_admin()) {
        Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_BANS'],
                $_SESSION['user'], $_SERVER['REMOTE_ADDR']),
            Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
    } elseif (is_mod()) {
        Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_BANS'],
                $_SESSION['user'], $_SERVER['REMOTE_ADDR']),
            Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
    } else {
        throw new PremissionException(PremissionException::$messages['NOT_ADMIN']
             . ' ' . PremissionException::$messages['NOT_MOD']);
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