<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Move thread.

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

        // Cleanup.
        DataExchange::releaseResources();

        $ERRORS['NOT_ADMIN']($smarty);
        exit(1);
    }
    call_user_func(Logging::$f['MOVE_THREAD_USE']);

    // Get necessary data.
    $boards = boards_get_all();

    // Move thread.
    if (isset($_POST['submited'])) {

        // Validate input data.
        $src_board['id'] = boards_check_id($_POST['src_board']);
        $thread['original_post'] = threads_check_original_post($_POST['thread']);
        $dst_board['id'] = boards_check_id($_POST['dst_board']);

        foreach ($boards as $board) {
            if ($board['id'] == $src_board['id']) {
                $src_board = $board;
            }
            if ($board['id'] == $dst_board['id']) {
                $dst_board = $board;
            }
        }

        $thread = threads_get_by_original_post($src_board['id'], $thread['original_post']);
        threads_move_thread($thread['id'], $dst_board['id']);

        // Copy files.
        $attachments = attachments_get_by_thread($thread['id']);
        foreach ($attachments as $a) {
            switch($a['attachment_type']) {
                case Config::ATTACHMENT_TYPE_FILE:
                    copy(Config::ABS_PATH . "/{$src_board['name']}/other/{$a['name']}",
                         Config::ABS_PATH . "/{$dst_board['name']}/other/{$a['name']}");
                    break;
                case Config::ATTACHMENT_TYPE_IMAGE:
                    $res = copy(Config::ABS_PATH . "/{$src_board['name']}/img/{$a['name']}",
                         Config::ABS_PATH . "/{$dst_board['name']}/img/{$a['name']}");
                    $res = copy(Config::ABS_PATH . "/{$src_board['name']}/thumb/{$a['thumbnail']}",
                         Config::ABS_PATH . "/{$dst_board['name']}/thumb/{$a['thumbnail']}");
                    break;
                case Config::ATTACHMENT_TYPE_LINK:
                    break;
                case Config::ATTACHMENT_TYPE_VIDEO:
                    break;
                default:
                    throw new CommonException('Not supported.');
                    break;
            }
        }
    }

    // Display move thread form.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->display('move_thread.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
