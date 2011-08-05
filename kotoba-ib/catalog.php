<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Catalog of threads.
 *
 * Parameters:
 * board - board name.
 * page - page number.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';

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
        require Config::ABS_PATH
            . "/locale/{$_SESSION['language']}/messages.php";
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

    // Fix for Firefox.
    header("Cache-Control: private");

    // Check for requied parameters.
    foreach (array('board') as $param) {
        if (!isset($_REQUEST[$param])) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, new RequiedParamError($param));
            exit(1);
        }
    }

    // Check board name.
    $board_name = boards_check_name($_REQUEST['board']);
    if ($board_name === FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    // Check page.
    $page = 1;
    $pages = array();
    $page_max = 1;
    if (isset($_REQUEST['page'])) {
        $page = check_page($_REQUEST['page']);
    }

    // Get categories, boards and make tree for navigation panel (navbar).
    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);
    make_category_boards_tree($categories, $boards);

    // Check if board exists.
    $board = NULL;
    foreach ($boards as $b) {
        if ($b['name'] == $board_name) {
            $board = $b;
        }
    }
    if ($board == NULL) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new BoardNotFoundError($board_name));
        exit(1);
    }

    // Calculate maximum page number.
    $threads_count = threads_get_visible_count($_SESSION['user'], $board['id']);
    $page_max = ceil($threads_count / 100);
    if ($page_max == 0) {
        $page_max = 1;  // Important for empty boards.
    }
    if ($page > $page_max) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new MaxPageError($page));
        exit(1);
    }

    // Get threads, original posts and attachments.

    $threads = threads_get_visible_by_page($_SESSION['user'],
                                           $board['id'],
                                           $page,
                                           100);

    $posts = posts_get_original_by_threads($threads);

    $posts_attachments = array();
    $attachments = array();
    if (is_attachments_enabled($board)) {
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
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
