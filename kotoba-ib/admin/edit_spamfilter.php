<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit spamfilter.

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
    if (!is_admin()) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new NotAdminError());
        exit(1);
    }
    call_user_func(Logging::$f['EDIT_SPAMFILTER_USE']);

    $patterns = spamfilter_get_all();
    $reload_patterns = false;

    if (isset($_POST['submited'])) {

        // Add new pattern.
        if(isset($_POST['new_pattern']) && $_POST['new_pattern'] != '') {
            $_ = spamfilter_check_pattern($_POST['new_pattern']);
            if ($_ === FALSE) {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            spamfilter_add($_);
            $reload_patterns = true;
        }

        // Delete patterns.
        foreach ($patterns as $p) {
            if (isset($_POST["delete_{$p['id']}"])) {
                spamfilter_delete($p['id']);
                $reload_patterns = true;
            }
        }
    }

    if ($reload_patterns) {
        $patterns = spamfilter_get_all();
    }

    // Generate html code of edit spamfilter page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
    $smarty->assign('patterns', $patterns);
    $smarty->display('edit_spamfilter.tpl');

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
