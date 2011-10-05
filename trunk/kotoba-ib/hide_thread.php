<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Hide thread script.
 *
 * Parameters:
 * thread - thread id.
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
    foreach (array('thread') as $param) {
        if (!isset($_REQUEST[$param])) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, new RequiedParamError($param));
            exit(1);
        }
    }

    // Guests cannot hide threads.
    if (is_guest()) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new GuestError());
        exit(1);
    }

    // Check thread id and get thread.
    $thread_id = threads_check_id($_REQUEST['thread']);
    if ( ($thread = threads_get_by_id($thread_id)) === NULL) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new ThreadNotFoundIdError($thread_id));
        exit(0);
    }

    hidden_threads_add($thread['id'], $_SESSION['user']);

    // Redirect back to board.
    header('Location: ' . Config::DIR_PATH . "/{$thread['board']['name']}/");

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
