<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit user groups.

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
    call_user_func(Logging::$f['EDIT_USER_GROUPS_USE']);

    $groups = groups_get_all();
    $users = users_get_all();
    $user_groups = user_groups_get_all();
    $reload_user_groups = false;

    // Add new relation.
    if (isset($_POST['new_bind_user'])
            && isset($_POST['new_bind_group'])
            && $_POST['new_bind_user'] != ''
            && $_POST['new_bind_group'] != '') {

        $new_bind_user = users_check_id($_POST['new_bind_user']);
        $new_bind_group = groups_check_id($_POST['new_bind_group']);
        user_groups_add($new_bind_user, $new_bind_group);
        $reload_user_groups = true;
    }

    // Change relation.
    foreach ($user_groups as $user_group) {
        $_ = "group_{$user_group['user']}_{$user_group['group']}";
        if (isset($_POST[$_]) && $_POST[$_] != $user_group['group']) {
            $new_group_id = groups_check_id($_POST[$_]);
            foreach ($groups as $group) {
                if ($group['id'] == $new_group_id) {
                    user_groups_edit($user_group['user'], $user_group['group'],
                                     $new_group_id);
                    $reload_user_groups = true;
                }
            }
        }
    }

    // Delete relation.
    foreach ($user_groups as $user_group) {
        if (isset($_POST["delete_{$user_group['user']}_{$user_group['group']}"])) {
            user_groups_delete($user_group['user'], $user_group['group']);
            $reload_user_groups = true;
        }
    }

    if ($reload_user_groups) {
        $groups = groups_get_all();
        $users = users_get_all();
        $user_groups = user_groups_get_all();
    }

    // Generate html code of edit user groups page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_all());
    $smarty->assign('groups', $groups);
    $smarty->assign('users', $users);
    $smarty->assign('user_groups', $user_groups);
    $smarty->display('edit_user_groups.tpl');

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
