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

// Скрипт скрытия нитей.

require 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';

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

    if (is_guest()) {
        throw new PermissionException(PermissionException::$messages['GUEST']);
    }

    // Проверка входных параметров и скрытие нити.
    if (isset($_POST['thread']) && isset($_POST['board_name'])) {
        $thread_id = threads_check_id($_POST['thread']);
        $board_name = boards_check_name($_POST['board_name']);
    } elseif (isset($_GET['thread']) && isset($_GET['board_name'])) {
        $thread_id = threads_check_id($_GET['thread']);
        $board_name = boards_check_name($_GET['board_name']);
    } else {
        header('Location: http://z0r.de/?id=114');
        DataExchange::releaseResources();
        exit;
    }
    hidden_threads_add($thread_id, $_SESSION['user']);

    // Перенаправление.
    DataExchange::releaseResources();
    header('Location: ' . Config::DIR_PATH . "/$board_name/");
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
