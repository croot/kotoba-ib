<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Remove post.
 *
 * Parameters:
 * post - post id.
 * password (optional) - password.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';

require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH
                . "/locale/{$_SESSION['language']}/messages.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ban = bans_check(get_remote_addr())) !== FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        $smarty->display('banned.tpl');

        session_destroy();
        exit(1);
    }

    // Check post id and get post.
    if (isset($_REQUEST['post'])) {
        $post = posts_get_visible_by_id(posts_check_id($_REQUEST['post']),
                                        $_SESSION['user']);
        if ($post === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
    } else {

        // Troll redirect.
        header('Location: http://z0r.de/?id=114');

        // Cleanup.
        DataExchange::releaseResources();

        exit(1);
    }

    // Check password.
    $password = NULL;
    if (isset($_REQUEST['password']) && $_REQUEST['password'] != '') {
        $password = posts_check_password($_REQUEST['password']);
        if ($password === FALSE) {

            // Cleanup
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }

        if (!isset($_SESSION['password'])
                || $_SESSION['password'] != $password) {

            $_SESSION['password'] = $password;
        }
    }

    // Remove post.
    if (is_admin()
            || ($post['password'] !== NULL
                && $post['password'] === $password)) {

        posts_attachments_delete_by_post($post['id']);
        posts_delete($post['id']);

        // Redirection.
        header('Location: ' . Config::DIR_PATH . "/{$post['board']['name']}/");
    } else {

        // Display password request.
        $smarty->assign('show_control', is_admin() || is_mod());
        $smarty->assign('boards', boards_get_visible($_SESSION['user']));
        $smarty->assign('post', $post);
        $smarty->assign('password', $password);
        $smarty->assign('_SERVER', $_SERVER);
        $smarty->display('remove_post.tpl');
    }

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch (KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
