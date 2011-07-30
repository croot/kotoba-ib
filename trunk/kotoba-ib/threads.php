<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Thread view script.
 *
 * Parameters:
 * board - board name.
 * thread - thread number.
 * quote (optional) - post number what will be added to reply form.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/events.php';

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
    $thread_number = threads_check_original_post($_REQUEST['thread']);

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

    $thread = threads_get_visible_by_original_post($board['id'],
                                                   $thread_number,
                                                   $_SESSION['user']);
    if ($thread === FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    // If thread archived, redirection to archived thread.
    if ($thread['archived']) {

        // Cleanup.
        DataExchange::releaseResources();

        header('Location: ' . Config::DIR_PATH . "/{$board['name']}/arch/"
               . "{$thread['original_post']}.html");
        exit(0);
    }

    $_ = threads_get_moderatable_by_id($thread['id'], $_SESSION['user']);
    $is_moderatable = ($_ === NULL ? FALSE : TRUE);

    $pfilter = function($posts_per_thread, $thread, $post) {
        static $recived = 0;
        static $prev_thread = NULL;

        if ($prev_thread !== $thread) {
            $recived = 0;
            $prev_thread = $thread;
        }

        if ($thread['original_post'] == $post['post_number']) {
            return TRUE;
        }
        $recived++;
        if ($recived >= $thread['posts_count'] - $posts_per_thread) {
            return TRUE;
        }
        return FALSE;
    };
    $posts = posts_get_visible_filtred_by_threads(array($thread),
                                                  $_SESSION['user'],
                                                  $pfilter,
                                                  $thread['posts_count']);

    if (is_attachments_enabled($board)) {
        $posts_attachments = posts_attachments_get_by_posts($posts);
        $attachments = attachments_get_by_posts($posts);
    }

    $ht_filter = function($hidden_thread, $user) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards(array($board),
                                                           $ht_filter,
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

    $smarty->assign('thread', $thread);
    $smarty->assign('enable_translation', is_translation_enabled($board));
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('categories', $categories);
    $board['annotation'] = $board['annotation'] ? html_entity_decode($board['annotation'], ENT_QUOTES, Config::MB_ENCODING) : $board['annotation'];
    $smarty->assign('boards', $boards);
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('MAX_FILE_SIZE', Config::MAX_FILE_SIZE);
    $smarty->assign('board', $board);
    $smarty->assign('name', $_SESSION['name']);
    if (isset($_REQUEST['quote'])) {
        $smarty->assign('quote', kotoba_intval($_REQUEST['quote']));
    }
    if (isset($_SESSION['oekaki'])) {
        $smarty->assign('oekaki', $_SESSION['oekaki']);
    }
    $smarty->assign('enable_macro', is_macrochan_enabled($board));
    $smarty->assign('macrochan_tags', $macrochan_tags);
    $smarty->assign('enable_youtube', is_youtube_enabled($board));
    $smarty->assign('enable_captcha', is_captcha_enabled($board));
    $smarty->assign('captcha', Config::CAPTCHA);
    $smarty->assign('password', $password);
    $smarty->assign('goto', $_SESSION['goto']);
    $smarty->assign('upload_types', $upload_types);
    $smarty->assign('enable_shi', is_shi_enabled($board));
    $smarty->assign('is_moderatable', $is_moderatable);
    $smarty->assign('threads', array($thread));
    $smarty->assign('enable_geoip', is_geoip_enabled($board));
    $smarty->assign('enable_postid', is_postid_enabled($board));
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    $smarty->assign('show_favorites', TRUE);
    $smarty->assign('is_board_view', FALSE);

    $simple_posts_html = '';
    foreach ($posts as $p) {

        // Find if author of this post is admin.
        $author_admin = posts_is_author_admin($p['user']);

        // Set default post author name if enabled.
        if (!$board['force_anonymous']
                && $board['default_name'] !== null
                && $p['name'] === null) {

            $p['name'] = $board['default_name'];
        }

        // Original post or reply.
        if ($thread['original_post'] == $p['number']) {
            $original_post_html = post_original_generate_html(
                $smarty,
                $board,
                $thread,
                $p,
                $posts_attachments,
                $attachments,
                false,
                null,
                false,
                null,
                false,
                $author_admin,
                is_geoip_enabled($board),
                is_postid_enabled($board)
            );
        } else {
            $simple_posts_html .= post_simple_generate_html(
                $smarty,
                $board,
                $thread,
                $p,
                $posts_attachments,
                $attachments,
                false,
                null,
                $author_admin,
                is_geoip_enabled($board),
                is_postid_enabled($board)
            );
        }
    }

    favorites_mark_readed($_SESSION['user'], $thread['id']);

    $smarty->assign('original_post_html', $original_post_html);
    $smarty->assign('simple_posts_html', $simple_posts_html);
    $smarty->assign('threads_html', $smarty->fetch('thread.tpl'));
    $smarty->assign('hidden_threads', $hidden_threads);
    $smarty->display('thread_view.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
