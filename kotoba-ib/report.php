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

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

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

    // Check for requied parameters.
    foreach (array('post') as $param) {
        if (!isset($_REQUEST[$param])) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, new RequiedParamError($param));
            exit(1);
        }
    }

    // Check post id and get post.
    $post = posts_get_visible_by_id(posts_check_id($REQUEST['post']),
                                    $_SESSION['user']);
    if ($post === FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    $captcha_request = TRUE;

    // Report.
    if (is_admin()) {
        $captcha_request = FALSE;
    } else {
        switch (Config::CAPTCHA) {
            case 'captcha':
                if (is_captcha_valid()) {
                    $captcha_request = FALSE;
                }
                break;
            case 'animaptcha':
                if (is_animaptcha_valid()) {
                    $captcha_request = FALSE;
                }
                break;
            default:

                // Cleanup.
                DataExchange::releaseResources();

                $_ = 'Unknown captcha type';
                display_error_page($smarty, new CaptchaError($_));
                exit(1);
                break;
        }
    }

    if ($captcha_request) {

        // Show captcha request.
        $smarty->assign('show_control', is_admin() || is_mod());
        $smarty->assign('boards', boards_get_visible($_SESSION['user']));
        $smarty->assign('id', $post['id']);
        $smarty->assign('enable_captcha', TRUE);
        $smarty->assign('captcha', Config::CAPTCHA);
        $smarty->display('report.tpl');
    } else {
        reports_add($post['id']);

        // Redirection.
        header('Location: ' . Config::DIR_PATH . "/{$post['board']['name']}/");
    }

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch (KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    if (!isset($smarty)) {
        $smarty = new SmartyKotobaSetup();
    }
    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>