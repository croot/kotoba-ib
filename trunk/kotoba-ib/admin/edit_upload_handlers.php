<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit upload handlers script.

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/errors.php";
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/logging.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if ( ($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Check permission and write message to log file.
    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    call_user_func(Logging::$f['EDIT_UPLOAD_HANDLERS_USE']);

    $upload_handlers = upload_handlers_get_all();
    $reload_upload_handlers = false;

    // Add new handler.
    if (isset($_POST['new_upload_handler']) && $_POST['new_upload_handler'] !== '') {
        upload_handlers_add(upload_handlers_check_name($_POST['new_upload_handler']));
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
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>