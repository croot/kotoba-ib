<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit stylesheets.

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
    call_user_func(Logging::$f['EDIT_STYLESHEETS_USE']);

    $stylesheets = stylesheets_get_all();
    $reload_stylesheets = FALSE;

    // Add stylesheet.
    if (isset($_POST['new_stylesheet']) && $_POST['new_stylesheet'] !== '') {
        $_ = stylesheets_check_name($_POST['new_stylesheet']);
        if ($_ === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        stylesheets_add($_);
        $reload_stylesheets = true;
    }

    // Delete stylesheet.
    foreach ($stylesheets as $stylesheet) {
        if (isset($_POST['delete_' . $stylesheet['id']])) {
            stylesheets_delete($stylesheet['id']);
            $reload_stylesheets = true;
        }
    }

    if ($reload_stylesheets) {
        $stylesheets = stylesheets_get_all();
    }

    // Generate html code of page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
    $smarty->assign('stylesheets', $stylesheets);
    $smarty->display('edit_stylesheets.tpl');

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
