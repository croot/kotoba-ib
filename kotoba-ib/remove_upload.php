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
 * Remove attachments of specified post.
 * @package userscripts
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
    // Initialization.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Check if remote host was banned.
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

    // Retrieve post id and password.
    if (isset($_GET['post'])) {
        $post_id = posts_check_id($_GET['post']);
        $password = isset($_GET['password']) ? posts_check_password($_GET['password']) : $_SESSION['password'];
    } elseif (isset($_POST['post'])) {
        $post_id = posts_check_id($_POST['post']);
        $password = isset($_POST['password']) ? posts_check_password($_POST['password']) : $_SESSION['password'];
    } else {
        header('Location: http://z0r.de/?id=114');
        DataExchange::releaseResources();
        exit;
    }
    $post = posts_get_visible_by_id($post_id, $_SESSION['user']);

    // Remove attachments.
    if (is_admin() || ($post['password'] !== null && $post['password'] === $password)) {
        posts_attachments_delete_by_post($post['id']);
    } else {
        throw new Exception('You cannot into remove attachment.');
    }

    // Redirection.
    header('Location: ' . Config::DIR_PATH . "/{$post['board_name']}/");
    DataExchange::releaseResources();
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
