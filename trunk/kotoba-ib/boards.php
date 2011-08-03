<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Board view script.
 *
 * Parameters:
 * board - board name.
 * page - page number.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
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

    $board_name = boards_check_name($_REQUEST['board']);
    if ($board_name === FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    $page = 1;
    if (isset($_REQUEST['page'])) {
        $page = check_page($_REQUEST['page']);
    }

    $password = NULL;
    if (isset($_SESSION['password'])) {
        $password = $_SESSION['password'];
    }

    $board = NULL;
    $banners_board_id = NULL;
    $posts_attachments = array();
    $attachments = array();

    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);
    make_category_boards_tree($categories, $boards);

    foreach ($boards as $b) {
        if ($b['name'] == $board_name) {
            $board = $b;
        }

        if ($b['name'] == Config::BANNERS_BOARD) {
            $banners_board_id = $b['id'];
        }
    }
    if ($board == NULL) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new BoardNotFoundError($board_name));
        exit(1);
    }

    $threads_count = threads_get_visible_count($_SESSION['user'], $board['id']);
    $page_max = ceil($threads_count / $_SESSION['threads_per_page']);
    if ($page_max == 0) {
        $page_max = 1; // Important for empty boards.
    }
    if ($page > $page_max) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new MaxPageError($page));
        exit(1);
    }

    $threads = threads_get_visible_by_page($_SESSION['user'],
                                           $board['id'],
                                           $page,
                                           $_SESSION['threads_per_page']);

    $posts = posts_get_visible_by_threads_preview(
        $board['id'],
        $threads,
        $_SESSION['user'],
        $_SESSION['posts_per_thread']
    );

    if (is_attachments_enabled($board)) {
        $posts_attachments = posts_attachments_get_by_posts($posts);
        $attachments = attachments_get_by_posts($posts);
    }

    $htfilter = function ($hidden_thread, $user) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards(array($board),
                                                           $htfilter,
                                                           $_SESSION['user']);

    $upload_types = upload_types_get_by_board($board['id']);
    if (is_macrochan_enabled($board)) {
        $macrochan_tags = macrochan_tags_get_all();
    } else {
        $macrochan_tags = array();
    }

    if ($banners_board_id) {
        $banners = images_get_by_board($banners_board_id);
        if (count($banners) > 0) {
            $smarty->assign('banner', $banners[rand(0, count($banners) - 1)]);
        }
    }

    $favorites = favorites_get_by_user($_SESSION['user']);

    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('categories', $categories);
    $board['annotation'] = $board['annotation'] ? html_entity_decode($board['annotation'], ENT_QUOTES, Config::MB_ENCODING) : $board['annotation'];
    $smarty->assign('board', $board);
    $smarty->assign('boards', $boards);
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('password', $password);
    $smarty->assign('upload_types', $upload_types);
    $smarty->assign('pages', ($pages = range(1, $page_max)));
    $smarty->assign('pages_count', count($pages));
    $smarty->assign('page', $page);
    $smarty->assign('goto', $_SESSION['goto']);
    $smarty->assign('macrochan_tags', $macrochan_tags);
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('enable_macro', is_macrochan_enabled($board));
    $smarty->assign('enable_youtube', is_youtube_enabled($board));
    $smarty->assign('enable_search', Config::ENABLE_SEARCH);
    $smarty->assign('enable_captcha', is_captcha_enabled($board));
    $smarty->assign('captcha', Config::CAPTCHA);
    $smarty->assign('enable_translation', is_translation_enabled($board));
    $smarty->assign('enable_geoip', is_geoip_enabled($board));
    $smarty->assign('enable_shi', is_shi_enabled($board));
    $smarty->assign('enable_postid', is_postid_enabled($board));
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    $smarty->assign('name', $_SESSION['name']);
    isset($_SESSION['oekaki']) && $smarty->assign('oekaki', $_SESSION['oekaki']);
    $smarty->assign('is_board_view', true);
    $smarty->assign('MAX_FILE_SIZE', Config::MAX_FILE_SIZE);

    //event_daynight($smarty);

    $threads_html = '';
    $simple_posts_html = '';
    foreach ($threads as $t) {
        $smarty->assign('thread', $t);

        $smarty->assign('show_favorites', true);
        foreach ($favorites as $f) {
            if ($t['id'] == $f['thread']['id']) {
                $smarty->assign('show_favorites', false);
                break;
            }
        }

        foreach ($posts as $p) {
            if ($t['id'] == $p['thread']['id']) {

                // Find if author of this post is admin.
                $author_admin = posts_is_author_admin($p['user']);

                // Set default post author name if enabled.
                if (!$board['force_anonymous'] && $board['default_name'] && !$p['name']) {
                    $p['name'] = $board['default_name'];
                }

                // Original post or reply.
                if ($t['original_post'] == $p['number']) {
                    $original_post_html = post_original_generate_html(
                        $smarty,
                        $board,
                        $t,
                        $p,
                        $posts_attachments,
                        $attachments,
                        true,
                        $_SESSION['lines_per_post'],
                        true,
                        $_SESSION['posts_per_thread'],
                        true,
                        $author_admin,
                        is_geoip_enabled($board),
                        is_postid_enabled($board)
                    );
                } else {
                    $simple_posts_html .= post_simple_generate_html(
                        $smarty,
                        $board,
                        $t,
                        $p,
                        $posts_attachments,
                        $attachments,
                        true,
                        $_SESSION['lines_per_post'],
                        $author_admin,
                        is_geoip_enabled($board),
                        is_postid_enabled($board)
                    );
                }
            }
        }
        $smarty->assign('original_post_html', $original_post_html);
        $smarty->assign('simple_posts_html', $simple_posts_html);
        $threads_html .= $smarty->fetch('thread.tpl');
        $simple_posts_html = '';
    }

    $smarty->assign('threads_html', $threads_html);
    $smarty->assign('hidden_threads', $hidden_threads);
    $smarty->display('board_view.tpl');

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
