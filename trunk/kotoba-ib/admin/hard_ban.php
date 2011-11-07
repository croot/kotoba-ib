<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Bans in firewall.

require_once dirname(dirname(__FILE__)) . '/config.php';
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
        require Config::ABS_PATH
                . "/locale/{$_SESSION['language']}/logging.php";
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
    $is_admin = FALSE;
    if (is_admin()) {
        $is_admin = TRUE;
    } elseif (!is_mod()) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new NotModError());
        exit(1);
    }
    call_user_func(Logging::$f['HARD_BAN_USE']);

    if (isset($_POST['new_range_beg']) && isset($_POST['new_range_end'])) {
        $new_range_beg = bans_check_range_beg($_POST['new_range_beg']);
        if ($new_range_beg === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $new_range_end = bans_check_range_end($_POST['new_range_end']);
        if ($new_range_end === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        hard_ban_add(long2ip($new_range_beg), long2ip($new_range_end));
    }

    // Display page.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
	$smarty->display('hard_ban.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>