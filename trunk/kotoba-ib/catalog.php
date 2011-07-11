<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Catalog of threads.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/events.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === FALSE) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if ( ($ban = bans_check($ip)) !== FALSE) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Fix for Firefox.
    header("Cache-Control: private");

    $board_name = boards_check_name($_REQUEST['board']);
    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);

    $page = 1;
    $pages = array();
    $page_max = 1;
    if (isset($_REQUEST['page'])) {
        if ( ($page = check_page($_REQUEST['page'], FALSE)) === NULL) {
            DataExchange::releaseResources();
            $ERRORS['KOTOBA_INTVAL']($smarty);
        }
    }

    // Make category-boards tree for navigation panel.
    foreach ($categories as &$c) {
        $c['boards'] = array();
        foreach ($boards as $b) {
            if ($b['category'] == $c['id']
                    && !in_array($b['name'], Config::$INVISIBLE_BOARDS)) {

                array_push($c['boards'], $b);
            }
        }
    }

    $board = NULL;
    foreach ($boards as $b) {
        if ($b['name'] == $board_name) {
            $board = $b;
        }
    }
    if ($board == NULL) {
        DataExchange::releaseResources();
        $ERRORS['BOARD_NOT_FOUND']($smarty, $board_name);
    }

    $threads_count = threads_get_visible_count($_SESSION['user'], $board['id']);
    $page_max = ($threads_count % 100 == 0
        ? (int)($threads_count / 100)
        : (int)($threads_count / 100) + 1);
    if ($page_max == 0) {
        $page_max = 1;  // Important for empty boards.
    }
    if ($page > $page_max) {
        DataExchange::releaseResources();
        $ERRORS['MAX_PAGE']($smarty, $page);
    }

    $threads = threads_get_visible_by_page($_SESSION['user'],
                                           $board['id'],
                                           $page,
                                           100);

    $posts = posts_get_original_by_threads($threads);

    $posts_attachments = array();
    $attachments = array();
    if ($board['with_attachments']) {
        $posts_attachments = posts_attachments_get_by_posts($posts);
        $attachments = attachments_get_by_posts($posts);
    }

    // Generate html code of page and display it.
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('categories', $categories);
    $smarty->assign('boards', $boards);

    $threads_html = '';
    foreach ($posts as $post) {
        $post_attachments = wrappers_attachments_get_by_post($smarty,
                                                             $post['board'],
                                                             $post,
                                                             $posts_attachments,
                                                             $attachments);
        $smarty->assign('post', $post);
        $smarty->assign('attachments', $post_attachments);
        $threads_html .= $smarty->fetch('catalog_thread.tpl');
    }

    $smarty->assign('threads_html', $threads_html);
    $smarty->assign('page', $page);
    $smarty->assign('pages', range(1, $page_max));
    $smarty->assign('board', $board);
    $smarty->display('catalog.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
