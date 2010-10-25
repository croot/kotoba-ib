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

try {
    // Инициализация.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

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

    // Проверка входных параметров.
    $REQUEST = "_{$_SERVER['REQUEST_METHOD']}";
    $REQUEST = $$REQUEST;
    if (!isset($REQUEST['post'])) {
        header('Location: http://z0r.de/?id=114');
        DataExchange::releaseResources();
        exit;
    }

    $post = posts_get_visible_by_id(posts_check_id($REQUEST['post']), $_SESSION['user']);

    $found = false;
    foreach (reports_get_all() as $report) {
        if ($report['post'] == $post['id']) {
            $found = true;
            break;
        }
    }

    // На это сообщение уже жаловались.
    if ($found) {
        header('Location: ' . Config::DIR_PATH . "/{$post['board']['name']}/");
    }

    if (is_admin()) {
        reports_add($post['id']);
        header('Location: ' . Config::DIR_PATH . "/{$post['board']['name']}/");
    } else {
        if (isset($REQUEST['captcha_code'])
                && isset($_SESSION['captcha_code'])
                && mb_strtolower($REQUEST['captcha_code'], Config::MB_ENCODING) === $_SESSION['captcha_code']) {

            reports_add($post['id']);
            header('Location: ' . Config::DIR_PATH . "/{$post['board']['name']}/");
        } else {

            // Вывод формы ввода капчи.
            $smarty->assign('id', $post['id']);
            $smarty->display('report.tpl');
        }
    }

    // Освобождение ресурсов и очистка.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>