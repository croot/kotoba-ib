<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Favorites management script.
 *
 * Parameters:
 * action - favorites action. add, delete, mark_readed, mark_all_readed
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
    foreach (array('action') as $param) {
        if (!isset($_REQUEST[$param])) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, new RequiedParamError($param));
            exit(1);
        }
    }

    // Guests cannot have favorites.
    if (is_guest()) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    // Perform action.
    $action = $_REQUEST['action'];
    $thread = isset($_REQUEST['thread']) ? $_REQUEST['thread'] : NULL;
    switch ($action) {
        case 'add':
            favorites_add($_SESSION['user'], threads_check_id($thread));
            break;
        case 'delete':
            favorites_delete($_SESSION['user'], threads_check_id($thread));
            break;
        case 'mark_readed':
            favorites_mark_readed($_SESSION['user'], threads_check_id($thread));
            break;
        case 'mark_all_readed':
            favorites_mark_readed($_SESSION['user']);
            break;
        default:
            break;
    }

    // Cleanup.
    DataExchange::releaseResources();

    // Redirection.
    header('Location: ' . Config::DIR_PATH . '/edit_settings.php');

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
