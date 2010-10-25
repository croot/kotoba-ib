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

/*
 * Скрипт удаления вложений. Скрипт принимает один параметр, который передаётся
 * с помощью POST или GET запроса:
 * post - Идентификатор удаляемого сообщения.
 */

require 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';

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

    $REQUEST = "_{$_SERVER['REQUEST_METHOD']}";
    $REQUEST = $$REQUEST;
    if (isset($REQUEST['post'])) {
        $post = posts_get_visible_by_id(posts_check_id($REQUEST['post']), $_SESSION['user']);
    } else {
        header('Location: http://z0r.de/?id=114');
        DataExchange::releaseResources();
        exit(1);
    }
    $password = isset($REQUEST['password']) ? posts_check_password($REQUEST['password']) : $_SESSION['password'];

    // Удаление вложения.
    if (is_admin() || ($post['password'] !== null && $post['password'] === $password)) {
        posts_attachments_delete_by_post($post['id']);
        header('Location: ' . Config::DIR_PATH . "/{$post['board']['name']}/");
    } else {

        // Вывод формы ввода пароля.
        $smarty->assign('show_control', is_admin() || is_mod());
        $smarty->assign('boards', boards_get_visible($_SESSION['user']));
        $smarty->assign('post', $post);
        $smarty->assign('password', $password);
        $smarty->assign('_SERVER', $_SERVER);
        $smarty->display('remove_upload.tpl');
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
