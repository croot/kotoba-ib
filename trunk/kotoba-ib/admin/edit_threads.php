<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit threads.

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

    call_user_func(Logging::$f['EDIT_THREADS_USE']);

    $page = 1;
    $page_max = 1;
    if (isset($_REQUEST['page'])) {
        $page = check_page($_REQUEST['page']);
    }

    $is_admin = is_admin();

    if ($is_admin) {
        list($count, $threads) = threads_get_all($page, 100);
    } else {
        list($count, $threads) = threads_get_moderatable(
            $_SESSION['user'],
            $page,
            100
        );
    }
    if (count($threads) <= 0) {

        // Cleanup.
        DataExchange::releaseResources();
        Logging::close_log();

        display_error_page($smarty, new NoEditableThreadsError());
        exit(1);
    }

    // We already select threads but anyway we need to calculate
    // pages count and check what page was correct.
    $page_max = ceil($count / 100);
    if ($page_max == 0) {
        $page_max = 1;
    }
    if ($page > $page_max) {

        // Cleanup.
        DataExchange::releaseResources();
        Logging::close_log();

        display_error_page($smarty, new MaxPageError($page));
        exit(1);
    }

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
                                     * Attachments allow on this board so we
                                     * dont need to allow it in this thread.
                                     */
                                    $new_with_attachments = NULL;
                                }
                                break;
                            }
                        }
                        break;
                }
            }

            // Is thread closed flag changes?
            $param_name = "closed_{$thread['id']}";
            $new_closed = $thread['closed'];
            if(isset($_POST[$param_name]) && $_POST[$param_name] != $thread['closed']) {
                switch($_POST[$param_name]) {
                    case '1':
                        $new_closed = TRUE;
                        break;
                    case '0':
                        $new_closed = FALSE;
                        break;
                }
            }

            // Any changes?
            if ($new_bump_limit != $thread['bump_limit']
                    || $new_sticky != $thread['sticky']
                    || $new_sage != $thread['sage']
                    || $new_with_attachments != $thread['with_attachments']
                    || $new_closed != $thread['closed']) {

                threads_edit($thread['id'],
                             $new_bump_limit,
                             $new_sticky,
                             $new_sage,
                             $new_with_attachments,
                             $new_closed);
                $reload_threads = true;
            }
        }
    }

    if ($reload_threads) {
        if ($is_admin) {
            list($count, $threads) = threads_get_all($page, 100);
        } else {
            list($count, $threads) = threads_get_moderatable(
                $_SESSION['user'],
                $page,
                100
            );
        }
        if (count($threads) <= 0) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, new NoEditableThreadsError());
            exit(1);
        }

        // We already select threads but anyway we need to calculate
        // pages count and check what page was correct.
        $page_max = ceil($count / 100);
        if ($page_max == 0) {
            $page_max = 1;
        }
        if ($page > $page_max) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, new MaxPageError($page));
            exit(1);
        }
    }

    // Generate html code of edit threads page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('threads', $threads);

    $pages = array();
    if (isset($page_max)) {
        $pages = range(1, $page_max);
    }

    $smarty->assign('pages', $pages);
    $smarty->assign('page', $page);
    $smarty->display('edit_threads.tpl');

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
