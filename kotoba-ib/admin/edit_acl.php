<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit ACL script.

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

        DataExchange::releaseResources();
        $ERRORS['REMOTE_ADDR']($smarty);
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
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    call_user_func(Logging::$f['EDIT_ACL_USE']);

    $groups = groups_get_all();
    $boards = boards_get_all();
    if (count($acl = acl_get_all()) <= 0) {
        DataExchange::releaseResources();
        Logging::close_log();
        $ERRORS['ACL_NO_RULES']($smarty);
    }
    $reload_acl = false;

    if (isset($_POST['submited'])) {

        // Add rule.
        if ((isset($_POST['new_group']) && isset($_POST['new_board'])
                && isset($_POST['new_thread']) && isset($_POST['new_post']))
                && ( $_POST['new_group'] !== '' || $_POST['new_board'] !== ''
                || $_POST['new_thread'] !== '' || $_POST['new_post'] !== '')) {

            $new_group = ($_POST['new_group'] === '') ? null : groups_check_id($_POST['new_group']);
            $new_board = ($_POST['new_board'] === '') ? null : boards_check_id($_POST['new_board']);
            $new_thread = ($_POST['new_thread'] === '') ? null : threads_check_id($_POST['new_thread']);
            $new_post = ($_POST['new_post'] === '') ? null : posts_check_id($_POST['new_post']);
            $new_view = (isset($_POST['new_view'])) ? 1 : 0;
            $new_change = (isset($_POST['new_change'])) ? 1 : 0;
            $new_moderate = (isset($_POST['new_moderate'])) ? 1 : 0;

            /*
             * Board, Thread or Post id is unique. If we know one we dont need
             * know more.
             */
            $_ = array($new_board, $new_thread, $new_post);
            if (count(array_filter($_, 'is_null')) != 2) {
                DataExchange::releaseResources();
                Logging::close_log();
                $ERRORS['ACL_RULE_EXCESS']($smarty);
            }

            /*
             * If view denied then change and moderate has no sense. If change
             * denyed then moderate has no sense.
             */
            if (($new_view == 0 && ($new_change != 0 || $new_moderate != 0))
                    || ($new_change == 0 && $new_moderate != 0)) {

                DataExchange::releaseResources();
                Logging::close_log();
                $ERRORS['ACL_RULE_CONFLICT']($smarty);
            }

            // Take a look if we already have that rule.
            $found = false;
            foreach ($acl as $record) {
                if ((($record['group'] === null && $new_group === null) || ($record['group'] == $new_group))
                    && (($record['board'] === null && $new_board === null) || ($record['board'] == $new_board))
                    && (($record['thread'] === null && $new_thread === null) || ($record['thread'] == $new_thread))
                    && (($record['post'] === null && $new_post === null) || ($record['post'] == $new_post))) {

                    acl_edit($new_group,
                             $new_board,
                             $new_thread,
                             $new_post,
                             $new_view,
                             $new_change,
                             $new_moderate);
                    $reload_acl = true;
                    $found = true;
                }
            }
            if (!$found) {
                acl_add($new_group,
                        $new_board,
                        $new_thread,
                        $new_post,
                        $new_view,
                        $new_change,
                        $new_moderate);
                $reload_acl = true;
            }
        }

        // Change rule.
        foreach($acl as $record) {

            // View permission changed.
            if ($record['view'] == 1 && !isset($_POST["view_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // View permission removed.
                acl_edit($record['group'],
                         $record['board'],
                         $record['thread'],
                         $record['post'],
                         0,
                         0,
                         0);
                $reload_acl = true;
                continue;
            }
            if ($record['view'] == 0 && isset($_POST["view_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // View permission added. Check for anoter permissions.
                if ($record['change'] == 0 && isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                    // Change permission added. Check for anoter permissions.
                    if ($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                        // Moderation permission added.
                        acl_edit($record['group'],
                                 $record['board'],
                                 $record['thread'],
                                 $record['post'],
                                 1,
                                 1,
                                 1);
                        $reload_acl = true;
                        continue;
                    } else {
                        acl_edit($record['group'],
                                 $record['board'],
                                 $record['thread'],
                                 $record['post'],
                                 1,
                                 1,
                                 0);
                        $reload_acl = true;
                        continue;
                    }
                } else {

                    // Mod. permission without Change permission has no sense.
                    acl_edit($record['group'],
                             $record['board'],
                             $record['thread'],
                             $record['post'],
                             1,
                             0,
                             0);
                    $reload_acl = true;
                    continue;
                }
            }

            // View permission unchanged.
            if ($record['change'] == 1 && !isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Change permission removed.
                acl_edit($record['group'],
                         $record['board'],
                         $record['thread'],
                         $record['post'],
                         $record['view'],
                         0,
                         0);
                $reload_acl = true;
                continue;
            }
            if ($record['change'] == 0 && isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Change permission added.
                if ($record['view'] == 0) {

                    // If we have no view permission ignore chages.
                    continue;
                } else {

                    // Check if Mod. permission added.
                    if ($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                        // Mod. permission added.
                        acl_edit($record['group'],
                                 $record['board'],
                                 $record['thread'],
                                 $record['post'],
                                 1,
                                 1,
                                 1);
                        $reload_acl = true;
                        continue;
                    } else {
                        acl_edit($record['group'],
                                 $record['board'],
                                 $record['thread'],
                                 $record['post'],
                                 1,
                                 1,
                                 0);
                        $reload_acl = true;
                        continue;
                    }
                }
            }

            // View and Change permission wasn't changed.
            if ($record['moderate'] == 1 && !isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Mod. permission removed.
                acl_edit($record['group'],
                         $record['board'],
                         $record['thread'],
                         $record['post'],
                         $record['view'],
                         $record['change'],
                         0);
                $reload_acl = true;
                continue;
            }
            if ($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Mod. permission added.
                acl_edit($record['group'],
                         $record['board'],
                         $record['thread'],
                         $record['post'],
                         $record['view'],
                         $record['change'],
                         1);
                $reload_acl = true;
                continue;
            }
        }

        // Remove rules from ACL.
        foreach ($acl as $record) {
            if (isset($_POST["delete_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {
                acl_delete($record['group'],
                           $record['board'],
                           $record['thread'],
                           $record['post']);
                $reload_acl = true;
            }
        }
    }

    if ($reload_acl) {
        if (count($acl = acl_get_all()) <= 0) {
            DataExchange::releaseResources();
            Logging::close_log();
            $ERRORS['ACL_NO_RULES']($smarty);
        }
    }

    // Generate html and display.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('groups', $groups);
    $smarty->assign('acl', $acl);
    $smarty->display('edit_acl.tpl');

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