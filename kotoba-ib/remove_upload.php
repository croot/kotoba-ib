<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Remove attachment script:
 *
 * Parameters:
 * post - post id.
 * password (optional) - password.
 */

require 'config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if ( ($ban = bans_check($ip)) !== false) {
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

    // Remove attachemnt
    if (is_admin() || ($post['password'] !== null && $post['password'] === $password)) {
        posts_attachments_delete_by_post($post['id']);
        header('Location: ' . Config::DIR_PATH . "/{$post['board']['name']}/");
    } else {

        // Display password request form.
        $smarty->assign('show_control', is_admin() || is_mod());
        $smarty->assign('boards', boards_get_visible($_SESSION['user']));
        $smarty->assign('post', $post);
        $smarty->assign('password', $password);
        $smarty->assign('_SERVER', $_SERVER);
        $smarty->display('remove_attachment.tpl');
    }

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
