<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

// Скрипт главной страницы имейджборды.

require_once 'config.php';
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

    $boards = boards_get_visible($_SESSION['user']);
    if (count($boards) > 0) {
        $smarty->assign('boards_exist', true);
        $smarty->assign('boards', $boards);
    }
    $smarty->assign('version', '$Revision$');
    $smarty->assign('date', '$Date$');
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->display('index.tpl');

    DataExchange::releaseResources();
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
