<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit upload handlers.

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
    call_user_func(Logging::$f['EDIT_UPLOAD_HANDLERS_USE']);

    $upload_handlers = upload_handlers_get_all();
    $reload_upload_handlers = false;

    // Add new handler.
    if (isset($_POST['new_upload_handler']) && $_POST['new_upload_handler'] !== '') {
        $_ = upload_handlers_check_name($_POST['new_upload_handler']);
        if ($_ === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        upload_handlers_add($_);
        $reload_upload_handlers = true;
    }

    // Delete handler.
    foreach ($upload_handlers as $handler) {
        if (isset($_POST['delete_' . $handler['id']])) {
            upload_handlers_delete($handler['id']);
            $reload_upload_handlers = true;
        }
    }

    if ($reload_upload_handlers) {
        $upload_handlers = upload_handlers_get_all();
    }

    // Generate html code of edit upload handlers page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
    $smarty->assign('upload_handlers', $upload_handlers);
    $smarty->display('edit_upload_handlers.tpl');

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