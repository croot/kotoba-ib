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

// Скрипт жалоб на сообщения.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/upload_handlers.php';
require_once Config::ABS_PATH . '/lib/mark.php';
include Config::ABS_PATH . '/securimage/securimage.php';

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

    if (isset($_GET['submit']) && $_GET['submit'] == '1' && isset($_GET['post'])) {
        $post_id = posts_check_id($_GET['post']);
    } elseif (isset($_POST['submit']) && $_POST['submit'] == '1' && isset($_POST['post'])) {
        $post_id = posts_check_id($_POST['post']);
    } else {
        header('Location: http://z0r.de/?id=114');
        DataExchange::releaseResources();
        exit;
    }

    $post = posts_get_visible_by_id($post_id, $_SESSION['user']);

    $found = false;
    foreach (reports_get_all() as $report) {
        if ($report['post'] == $post_id) {
            $found = true;
            break;
        }
    }

    // На это сообщение уже жаловались.
    if ($found) {
        header('Location: ' . Config::DIR_PATH . "/{$post['board_name']}/");
    }

    if (is_admin()) {
        reports_add($post['id']);
        header('Location: ' . Config::DIR_PATH . "/{$post['board_name']}/");
    } else {
        $securimage = new Securimage();
        if (!isset($_POST['captcha_code']) || $securimage->check($_POST['captcha_code']) == false) {
            // Вывод формы ввода капчи.
            $smarty->assign('id', $post['id']);
            $smarty->display('report.tpl');
        } else {
            reports_add($post['id']);
            header('Location: ' . Config::DIR_PATH . "/{$post['board_name']}/");
        }
    }

    DataExchange::releaseResources();
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>