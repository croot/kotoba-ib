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

/**
 * Скрипт просмотра лога.
 */

/***/
require '../config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Инициализация.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'],
                                    $_SESSION['stylesheet']);

    // Проверка, не заблокирован ли клиент.
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

    // Проверка доступа и запись в лог.
    if (!is_admin()) {
        //throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    //call_user_func(Logging::$f['LOG_VIEW']);
    $func = Logging::$f['LOG_VIEW'];
    $func();
    Logging::close_log();

    $i = 10;    // 10 последних записей.
    $log = array();
    $logf = fopen(Config::ABS_PATH . '/log/actions.log', 'r');
    while (($line = fgets($logf)) && $i--) {
        array_push($log, preg_split('/\|/', $line, -1, PREG_SPLIT_NO_EMPTY));
    }

    // Формирование кода страницы и вывод.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('log', $log);
    $smarty->assign('boards', boards_get_all());
    $smarty->display('log_view.tpl');

    // Освобождение ресурсов и очиска.
    DataExchange::releaseResources();

    exit(0);
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
