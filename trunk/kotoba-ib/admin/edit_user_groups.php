<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit user groups script.

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
        if (isset($_POST["group_{$user_group['user']}_{$user_group['group']}"])
                && $_POST["group_{$user_group['user']}_{$user_group['group']}"] != $user_group['group']) {
            $new_group_id = groups_check_id($_POST["group_{$user_group['user']}_{$user_group['group']}"]);
            foreach ($groups as $group) {
                if ($group['id'] == $new_group_id) {
                    user_groups_edit($user_group['user'], $user_group['group'], $new_group_id);
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
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
