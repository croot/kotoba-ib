<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Mass ban script.

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
    call_user_func(Logging::$f['MASS_BAN_USE']);

    if (isset($_FILES['file'])) {
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:

                // Cleanup
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, new UploadIniSizeError());
                exit(1);
                break;
            case UPLOAD_ERR_FORM_SIZE:

                // Cleanup
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, new UploadFormSizeError());
                exit(1);
                break;
            case UPLOAD_ERR_PARTIAL:

                // Cleanup
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, new UploadPartialError());
                exit(1);
                break;
            case UPLOAD_ERR_NO_TMP_DIR:

                // Cleanup
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, new UploadNoTmpDirError());
                exit(1);
                break;
            case UPLOAD_ERR_CANT_WRITE:

                // Cleanup
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, new UploadCantWriteError());
                exit(1);
                break;
            case UPLOAD_ERR_EXTENSION:

                // Cleanup
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, new UploadExtensionError());
                exit(1);
                break;
        }
        $list = split("\n", file_get_contents($_FILES['file']['tmp_name']));
        foreach ($list as $range) {
            if ($range) {
                list($range_beg, $range_end) = split(' ', $range);

                // Ban for a month.
                $reason = 'Mass ban utility';
                $until = date(Config::DATETIME_FORMAT, time() + 60 * 60 * 24 * 30);
                bans_add(ip2long($range_beg), ip2long($range_end), $reason, $until);
                call_user_func(
                    Logging::$f['MASS_BAN_ADD'],
                    $range_beg,
                    $range_end,
                    $reason,
                    $until
                );
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
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
