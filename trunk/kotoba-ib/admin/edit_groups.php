<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit groups.

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
    call_user_func(Logging::$f['EDIT_GROUPS_USE']);

    $groups = groups_get_all();
    $delete_list = array();
    $reload_groups = false;

    // Add new group.
    if (isset($_POST['new_group']) && $_POST['new_group'] !== '') {
        $new_group = groups_check_name($_POST['new_group']);
        if ($new_group === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        } else {
            groups_add($new_group);
            $reload_groups = true;
        }
    }

    // Delete group.
    foreach($groups as $group) {
        if (isset($_POST['delete_' . $group['id']])) {
            array_push($delete_list, $group['id']);
        }
    }
    if (count($delete_list) > 0) {
        groups_delete($delete_list);
        $reload_groups = true;
    }

    if ($reload_groups) {
        $groups = groups_get_all();
    }

    // Generate html code of edit groups page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
    $smarty->assign('groups', $groups);
    $smarty->display('edit_groups.tpl');

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