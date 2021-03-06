<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit bans.

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
    call_user_func(Logging::$f['EDIT_BANS_USE']);

    $bans = bans_get_all();
    date_default_timezone_set(Config::DEFAULT_TIMEZONE);
    $bans_decoded = array();
    foreach ($bans as $ban) {
        array_push($bans_decoded,
                   array('id' => $ban['id'],
                         'range_beg' => long2ip($ban['range_beg']),
                         'range_end' => long2ip($ban['range_end']),
                         'reason' => $ban['reason'],
                         'untill' => $ban['untill']));
    }

    $reload_bans = false;

    if (isset($_POST['submit'])) {

        // Add new ban.
        if (isset($_POST['new_range_beg']) && isset($_POST['new_range_end'])
                && isset($_POST['new_reason']) && isset($_POST['new_untill'])
                && $_POST['new_range_beg'] != ''
                && $_POST['new_range_end'] != ''
                && $_POST['new_untill'] != '') {

            $new_range_beg = bans_check_range_beg($_POST['new_range_beg']);
            $new_range_end = bans_check_range_end($_POST['new_range_end']);
            if ($new_range_beg === FALSE || $new_range_end === FALSE) {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            if ($_POST['new_reason'] === '') {
                $new_reason = null;
            }
            else {
                $new_reason = bans_check_reason($_POST['new_reason']);
                if ($new_reason === FALSE) {

                    // Cleanup.
                    DataExchange::releaseResources();
                    Logging::close_log();

                    display_error_page($smarty, kotoba_last_error());
                    exit(1);
                }
            }
            $new_untill = bans_check_untill($_POST['new_untill']);
            bans_add($new_range_beg,
                     $new_range_end,
                     $new_reason,
                     date('Y-m-d H:i:s', time() + $new_untill));
            $reload_bans = true;
            if (isset($_POST['post'])) {
                if (isset($_POST['add_text'])) {
                    posts_add_text_by_id(
                        posts_check_id($_POST['post']),
                        $smarty->fetch('uwb4tp.tpl')
                    );
                } elseif (isset($_POST['del_post'])) {
                    posts_delete(posts_check_id($_POST['post']));
                } elseif (isset($_POST['del_all'])) {
                    posts_delete_last(
                        posts_check_id($_POST['post']),
                        date(Config::DATETIME_FORMAT, time() - (60 * 60))
                    );
                }
            }
        }

        // Delete bans.
        foreach ($bans as $ban) {
            if (isset($_POST['delete_' . $ban['id']])) {
                bans_delete_by_id($ban['id']);
                $reload_bans = true;
            }
        }

        // Unban specified ip.
        if (isset($_POST['unban']) && $_POST['unban'] !== '') {

            $ip = bans_check_range_beg($_POST['unban']);
            if ($ip === FALSE) {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            bans_delete_by_ip($ip);
            $reload_bans = true;
        }
    }

    if ($reload_bans) {
        $bans = bans_get_all();
    }
    $bans_decoded = array();
    foreach ($bans as $ban) {
        array_push($bans_decoded,
                   array('id' => $ban['id'],
                         'range_beg' => long2ip($ban['range_beg']),
                         'range_end' => long2ip($ban['range_end']),
                         'reason' => $ban['reason'],
                         'untill' => $ban['untill']));
    }

    // Generate html and display.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_all());
    $smarty->assign('bans_decoded', $bans_decoded);
    $smarty->display('edit_bans.tpl');

    // Clean up.
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
