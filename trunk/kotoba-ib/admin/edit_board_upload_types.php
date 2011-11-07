<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit board upload types relations script.

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
    call_user_func(Logging::$f['EDIT_BOARD_UPLOAD_TYPES_USE']);

    $upload_types = upload_types_get_all();
    $boards = boards_get_all();
    $board_upload_types = board_upload_types_get_all();
    $reload_board_upload_types = false;

    if (isset($_POST['submited'])) {

        // Add new relation.
        if (isset($_POST['new_bind_board'])
                && isset($_POST['new_bind_upload_type'])
                && $_POST['new_bind_board'] !== ''
                && $_POST['new_bind_upload_type'] !== '') {

            board_upload_types_add(
                boards_check_id($_POST['new_bind_board']),
                upload_types_check_id($_POST['new_bind_upload_type'])
            );
            $reload_board_upload_types = true;
        }

        // Delete relations.
        foreach ($board_upload_types as $board_upload_type) {
            $_ = "delete_{$board_upload_type['board']}"
                 . "_{$board_upload_type['upload_type']}";
            if (isset($_POST[$_])) {
                board_upload_types_delete(
                    $board_upload_type['board'],
                    $board_upload_type['upload_type']
                );
                $reload_board_upload_types = true;
            }
        }
    }

    $reload_board_upload_types && ($board_upload_types = board_upload_types_get_all());

    // Generate html code of edit board upload types relations page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('upload_types', $upload_types);
    $smarty->assign('board_upload_types', $board_upload_types);
    $smarty->display('edit_board_upload_types.tpl');

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