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

// Скрипт удаления сообщений.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/upload_handlers.php';
require_once Config::ABS_PATH . '/lib/mark.php';

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

    if (isset($_GET['post'])) {
        $post_id = posts_check_id($_GET['post']);
        if (isset($_GET['password'])) {
            $password = $_GET['password'];
        }
    } elseif (isset($_POST['post'])) {
        $post_id = posts_check_id($_POST['post']);
        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        }
    } else {
        header('Location: http://z0r.de/?id=114');
        DataExchange::releaseResources();
        exit;
    }

    $post = posts_get_visible_by_id($post_id, $_SESSION['user']);
    $password = isset($password) ? posts_check_password($password) : $_SESSION['password'];

    if (is_admin()) {
        posts_delete($post['id']);
        header('Location: ' . Config::DIR_PATH . "/{$post['board_name']}/");
    } elseif(($post['password'] !== null && $post['password'] === $password)) {
        $securimage = new Securimage();
        if ($securimage->check($_POST['captcha_code']) == false) {
            throw new CommonException(CommonException::$messages['CAPTCHA']);
        }
        posts_delete($post['id']);
        header('Location: ' . Config::DIR_PATH . "/{$post['board_name']}/");
    } else {
        // Вывод формы ввода пароля.
        $smarty->assign('id', $post['id']);
        $smarty->assign('is_admin', is_admin());
        $smarty->assign('password', $password);
        $smarty->display('remove_post.tpl');
    }

    DataExchange::releaseResources();
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>