<?php
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

/*
 * Script what shows menu, used in left frame of main page.
 *
 * Prameters:
 * no parameters.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
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

    // Get boards and categories and make tree for navbar.
    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);
    make_category_boards_tree($categories, $boards);

    list ($style_name, $_) = preg_split('/\./', $_SESSION['stylesheet']);
    $smarty->assign('style_name', $style_name);
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('categories', $categories);
    $smarty->display('menu.tpl');

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
