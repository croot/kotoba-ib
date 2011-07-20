<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Favorites script.
 *
 * Parameters:
 * action - favorites action. Add, delete, mark_readed, mark_all_readed
 * thread - thread id.
 */

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if (!isset($_SERVER['REMOTE_ADDR'])
            || ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === FALSE) {

        DataExchange::releaseResources();
        $ERRORS['REMOTE_ADDR']($smarty);
    }
    if ( ($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Guests cannot have favorites.
    if (is_guest()) {
        throw new PermissionException(PermissionException::$messages['GUEST']);
    }

    // Check input parameters.
    $REQUEST = "_{$_SERVER['REQUEST_METHOD']}";
    $REQUEST = $$REQUEST;
    $action = isset($REQUEST['action']) ? $REQUEST['action'] : null;
    $thread = isset($REQUEST['thread']) ? $REQUEST['thread'] : null;

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
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
