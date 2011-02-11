<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit threads script.

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

    // Check permission, get correspond data and write message to log file.
    if (is_admin()) {
        $threads = threads_get_all();
    } else {
        $threads = threads_get_moderatable($_SESSION['user']);
    }
    if (count($threads) <= 0) {
        throw new NodataException(NodataException::$messages['THREADS_EDIT']);
    }
    call_user_func(Logging::$f['EDIT_THREADS_USE']);

    $boards = boards_get_all();
    $reload_threads = false;

    // Change threads attributes.
    if (isset($_POST['submited'])) {
        foreach ($threads as $thread) {

            // Is bumplimit changes?
            $param_name = "bump_limit_{$thread['id']}";
            $new_bump_limit = $thread['bump_limit'];
            if (isset($_POST[$param_name]) && $_POST[$param_name] != $thread['bump_limit']) {
                if ($_POST[$param_name] == '') {
                    $new_bump_limit = NULL;
                } else {
                    $new_bump_limit = threads_check_bump_limit($_POST[$param_name]);
                }
            }

            // Is sticky flag changes?
            $param_name = "sticky_{$thread['id']}";
            $new_sticky = $thread['sticky'];
            if (isset($_POST[$param_name]) && $_POST[$param_name] != $thread['sticky']) {
                $new_sticky = TRUE;
            }
            if (!isset($_POST[$param_name]) && $thread['sticky']) {
                $new_sticky = FALSE;
            }

            // Is sage flag changes?
            $param_name = "sage_{$thread['id']}";
            $new_sage = $thread['sage'];
            if (isset($_POST[$param_name]) && $_POST[$param_name] != $thread['sage']) {
                $new_sage = TRUE;
            }
            if (!isset($_POST[$param_name]) && $thread['sage']) {
                $new_sage = FALSE;
            }

            // Is attachments flag changes?
            $param_name = "with_attachments_{$thread['id']}";
            $new_with_attachments = $thread['with_attachments'];
            if(isset($_POST[$param_name]) && $_POST[$param_name] != $thread['with_attachments']) {
                switch($_POST[$param_name]) {
                    case '':
                        $new_with_attachments = NULL;
                        break;
                    case '1':
                        foreach ($boards as $board) {
                            if ($board['id'] == $thread['board']) {
                                if ($board['with_attachments']) {
                                    /*
                                     * Attachments enabled on board so we dont
                                     * need to additionally allow it on thread.
                                     */
                                    $new_with_attachments = NULL;
                                } else {
                                    /*
                                     * Attachments disabled on board but allow
                                     * in this thread.
                                     */
                                    $new_with_attachments = TRUE;
                                }
                                break;
                            }
                        }
                        break;
                    case '0':
                        foreach ($boards as $board) {
                            if ($board['id'] == $thread['board']) {
                                if ($board['with_attachments']) {
                                    /*
                                     * Attachments enabled on this board but
                                     * disabled in this thread.
                                     */
                                    $new_with_attachments = FALSE;
                                } else {
                                    /*
                                     * Загрузка файлов на доске запрещена,
                                     * поэтому дополнительно запрещать её в этой
                                     * нити не нужно.
                                     */
                                    $new_with_attachments = NULL;
                                }
                                break;
                            }
                        }
                        break;
                }
            }

            // Any changes?
            if ($new_bump_limit != $thread['bump_limit']
                    || $new_sticky != $thread['sticky']
                    || $new_sage != $thread['sage']
                    || $new_with_attachments != $thread['with_attachments']) {

                threads_edit($thread['id'], $new_bump_limit, $new_sticky,
                $new_sage, $new_with_attachments);
                $reload_threads = true;
            }
        }
    }

    if ($reload_threads) {
        if (is_admin ()) {
            $threads = threads_get_all();
        } else {
            $threads = threads_get_moderatable($_SESSION['user']);
        }
        if (count($threads) <= 0) {
            throw new NodataException(NodataException::$messages['THREADS_EDIT']);
        }
    }

    // Generate html code of edit threads page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('threads', $threads);
    $smarty->display('edit_threads.tpl');

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
