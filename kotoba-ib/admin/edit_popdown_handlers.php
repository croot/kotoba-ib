<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit popdown handlers script.

require_once '../config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/logging.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if (!isset($_SERVER['REMOTE_ADDR'])
            || ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === FALSE) {

        throw new RemoteAddressException();
    }
    if ( ($ban = bans_check($ip)) !== FALSE) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Check permission and write message to log file.
    if (!is_admin()) {

        // Cleanup.
        DataExchange::releaseResources();

        $ERRORS['NOT_ADMIN']($smarty);
        exit(1);
    }
    call_user_func(Logging::$f['EDIT_POPDOWN_HANDLERS_USE']);

    $popdown_handlers = popdown_handlers_get_all();
    $reload_popdown_handlers = false;

    // Add popdown handler.
    if (isset($_POST['new_popdown_handler']) && $_POST['new_popdown_handler'] !== '') {
        popdown_handlers_add(popdown_handlers_check_name($_POST['new_popdown_handler']));
        $reload_popdown_handlers = true;
    }

    // Delete popdown handlers.
    foreach ($popdown_handlers as $handler) {
        if (isset($_POST['delete_' . $handler['id']])) {
            popdown_handlers_delete($handler['id']);
            $reload_popdown_handlers = true;
        }
    }

    if ($reload_popdown_handlers) {
        $popdown_handlers = popdown_handlers_get_all();
    }

    // Generate html code of edit popdown handlers page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
    $smarty->assign('popdown_handlers', $popdown_handlers);
    $smarty->display('edit_popdown_handlers.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>