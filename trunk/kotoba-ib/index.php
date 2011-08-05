<?php
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

/*
 * Script of imageboard main page.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';

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

    // Generate main page html-code and display it.
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->display('index.tpl');

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
