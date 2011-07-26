<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Mass ban script.
 *
 * Parameters:
 * file - File what contains addresses ranges what need to be banned. Every range
 * must ends with \n sign.
 */

/***/
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
    call_user_func(Logging::$f['MASS_BAN_USE']);

    
    if (isset($_FILES['file'])) {
        check_upload_error($_FILES['file']['error']);
        $list = split("\n", file_get_contents($_FILES['file']['tmp_name']));
        foreach ($list as $range) {
            if ($range) {
                list($range_beg, $range_end) = split(' ', $range);

                // Ban for a month.
                $reason = 'Mass ban utility';
                $until = date(Config::DATETIME_FORMAT, time() + 60 * 60 * 24 * 30);
                bans_add(ip2long($range_beg), ip2long($range_end), $reason, $until);
                call_user_func(Logging::$f['MASS_BAN_ADD'],
                               $range_beg,
                               $range_end,
                               $reason,
                               $until);
            }
        }
    }

    // Generate html code of mass ban page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_all());
    $smarty->display('mass_ban.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
