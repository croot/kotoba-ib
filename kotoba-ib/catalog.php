<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Скрипт просмотра нитей доски.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/events.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    if (($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if (($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Fix for Firefox.
    header("Cache-Control: private");

    $board_name = boards_check_name($_GET['board']);
    $boards = boards_get_visible($_SESSION['user']);
    $board = null;
    $banners_board_id = null;
    foreach ($boards as $b) {
        if ($b['name'] == $board_name) {
            $board = $b;
        }

        if ($b['name'] == 'misc') {
            $banners_board_id = $b['id'];
        }
    }
    if (!$board) {
        throw new NodataException(NodataException::$messages['BOARD_NOT_FOUND']);
    }

    $tfilter = function($thread) {
        return true;
    };
    $threads = threads_get_visible_filtred_by_board($board['id'], $_SESSION['user'], $tfilter);
    $sticky_threads = array();
    $other_threads = array();
    foreach ($threads as $thread) {
        if ($thread['sticky']) {
            array_push($sticky_threads, $thread);
        } else {
            array_push($other_threads, $thread);
        }
    }
    $threads = array_merge($sticky_threads, $other_threads);

    // Pass only original posts.
    $pfilter = function($thread, $post) {
        static $prev_thread = null;

        if ($prev_thread !== $thread) {
            $prev_thread = $thread;
        }

        if ($thread['original_post'] == $post['number']) {
            return true;
        }
        return false;
    };
    $posts = posts_get_visible_filtred_by_threads($threads, $_SESSION['user'], $pfilter);

    if ($board['with_attachments']) {
        $smarty->assign('posts_attachments', posts_attachments_get_by_posts($posts));
        $smarty->assign('attachments', attachments_get_by_posts($posts));
    }

    $smarty->assign('boards', $boards);
    $smarty->assign('board', $board);
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    $smarty->assign('posts', $posts);
    DataExchange::releaseResources();
    $smarty->display('catalog.tpl');
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
