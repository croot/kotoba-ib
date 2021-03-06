<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Removes dangling attachments.

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
    call_user_func(Logging::$f['DELETE_DANGLING_ATTACHMENTS_USE']);

    $attachments = attachments_get_dangling();
    $boards = boards_get_all();
    $delete_count = 0;

    foreach ($attachments as &$a) {
        switch ($a['attachment_type']) {
            case Config::ATTACHMENT_TYPE_FILE:
                foreach ($boards as $b) {
                    $full_path = Config::ABS_PATH
                        . "/{$b['name']}/other/{$a['name']}";
                    if (file_exists($full_path)) {
                        if ($a['hash'] === null
                                || $a['hash'] == calculate_file_hash($full_path)) {
                            if (isset($_POST['submit'])
                                    && (isset($_POST["delete_file_{$a['id']}"])
                                            || isset($_POST['delete_all']))) {

                                // Actually delete.
                                // TODO Uncomment
                                //unlink($full_path);
                                //files_delete($a['id']);
                                $delete_count++;
                            } elseif(!isset($_POST['submit'])) {

                                // For show list of dangling attachments.
                                $a['flag'] = true;
                                $a['link'] = Config::DIR_PATH
                                    . "/{$b['name']}/other/{$a['name']}";
                                $a['thumbnail'] = Config::DIR_PATH
                                    . "/img/{$a['thumbnail']}";
                            }
                        }
                    }
                }
                break;
            case Config::ATTACHMENT_TYPE_IMAGE:
                foreach ($boards as $b) {
                    $full_path = Config::ABS_PATH
                        . "/{$b['name']}/img/{$a['name']}";
                    if (file_exists($full_path)) {
                        if ($a['hash'] === null
                                || $a['hash'] == calculate_file_hash($full_path)) {
                            if (isset($_POST['submit'])
                                    && (isset($_POST["delete_image_{$a['id']}"])
                                            || isset($_POST['delete_all']))) {

                                // Actually delete.
                                // TODO Uncomment
                                //unlink($full_path);
                                //images_delete($a['id']);
                                $delete_count++;
                            } elseif(!isset($_POST['submit'])) {

                                // For show list of dangling attachments.
                                $a['flag'] = true;
                                $a['link'] = Config::DIR_PATH
                                    . "/{$b['name']}/img/{$a['name']}";
                                $a['thumbnail'] = Config::DIR_PATH
                                    . "/{$b['name']}/thumb/{$a['thumbnail']}";
                            }
                        }
                    }
                }
                break;
            case Config::ATTACHMENT_TYPE_LINK:
                if (isset($_POST['submit'])
                        && (isset($_POST["delete_link_{$a['id']}"])
                                || isset($_POST['delete_all']))) {

                    // TODO Uncomment
                    //links_delete($a['id']);
                    $delete_count++;
                } elseif (!isset($_POST['submit'])) {
                    $a['flag'] = true;
                }
                break;
            case Config::ATTACHMENT_TYPE_VIDEO:
                if (isset($_POST['submit'])
                        && (isset($_POST["delete_video_{$a['id']}"])
                                || isset($_POST['delete_all']))) {

                    // TODO Uncomment
                    //videos_delete($a['id']);
                    $delete_count++;
                } elseif (!isset($_POST['submit'])) {
                    $a['flag'] = true;
                    $a['is_embed'] = true;
                    $smarty->assign('code', $a['code']);
                    $a['link'] = $smarty->fetch('youtube.tpl');
                }
                break;
        }
    }

    // Generate code of page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('attachments', $attachments);
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    if (isset($_POST['submit'])) {
        $smarty->assign('delete_count', $delete_count);
    }
    $smarty->display('delete_dangling_files.tpl');

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
