<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Report script.
 *
 * Parameters:
 * post - reported post id.
 */

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/errors.php";
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

    // Check input parameters.
    $REQUEST = "_{$_SERVER['REQUEST_METHOD']}";
    $REQUEST = $$REQUEST;
    if (!isset($REQUEST['post'])) {
        header('Location: http://z0r.de/?id=114');
        DataExchange::releaseResources();
        exit(1);
    }

    $post = posts_get_visible_by_id(posts_check_id($REQUEST['post']), $_SESSION['user']);

    // Report.
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

            // Show captcha request.
            $smarty->assign('show_control', is_admin() || is_mod());
            $smarty->assign('boards', boards_get_visible($_SESSION['user']));
            $smarty->assign('id', $post['id']);
            $smarty->display('report.tpl');
        }
    }

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>