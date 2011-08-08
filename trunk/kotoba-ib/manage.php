<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/* 
 * Manage page.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
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

    // Check permission and write message to log file.
    if (!is_admin() && !is_mod()) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new NotModError());
        exit(1);
    }
    call_user_func(Logging::$f['MANAGE_USE']);

    // Get boards and categories and make tree for navbar.
    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);
    make_category_boards_tree($categories, $boards);

    // Create html-code of manage page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('categories', $categories);
    $smarty->assign('boards', $boards);
    if (is_mod()) {
        $smarty->assign('mod_panel', true);
    } elseif (is_admin()) {
        $smarty->assign('adm_panel', true);
    }
    $smarty->display('manage.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

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
