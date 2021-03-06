<?php
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

/*
 * Script of news page.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

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

    // Get boards and categories and make tree for navbar.
    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);
    make_category_boards_tree($categories, $boards);

    // Generate news html-code.
    $news_html = '';
    foreach ($boards as $board) {
        if ($board['name'] == Config::NEWS_BOARD) {

            // Requied for simple and original post templates =\
            $smarty->assign('is_board_view', TRUE);
            $smarty->assign('show_favorites', TRUE);

            // Pass all threads.
            $tfilter = function($thread) {
                return true;
            };
            $threads = threads_get_visible_filtred_by_board($board['id'],
                                                            $_SESSION['user'],
                                                            $tfilter);

            // Pass all posts.
            $pfilter = function($thread, $post) {
                return true;
            };
            $posts = posts_get_visible_filtred_by_threads($threads,
                                                          $_SESSION['user'],
                                                          $pfilter);

            $posts_attachments = posts_attachments_get_by_posts($posts);
            $attachments = attachments_get_by_posts($posts);

            $smarty->assign('ATTACHMENT_TYPE_FILE',
                            Config::ATTACHMENT_TYPE_FILE);
            $smarty->assign('ATTACHMENT_TYPE_LINK',
                            Config::ATTACHMENT_TYPE_LINK);
            $smarty->assign('ATTACHMENT_TYPE_VIDEO',
                            Config::ATTACHMENT_TYPE_VIDEO);
            $smarty->assign('ATTACHMENT_TYPE_IMAGE',
                            Config::ATTACHMENT_TYPE_IMAGE);

            $simple_posts_html = '';

            foreach ($threads as $t) {
                foreach ($posts as $p) {
                    if ($t['id'] == $p['thread']['id']) {

                        $author_admin = posts_is_author_admin($p['user']);

                        // Set default post author name if enabled.
                        if (!$board['force_anonymous']
                                && $board['default_name']
                                && !$p['name']) {

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
                                false,
                                null,
                                false,
                                null,
                                false,
                                $author_admin,
                                false,
                                false,
                                false,
                                false,
                                false
                            );
                        } else {
                            $simple_posts_html .= post_simple_generate_html(
                                $smarty,
                                $board,
                                $t,
                                $p,
                                $posts_attachments,
                                $attachments,
                                false,
                                null,
                                $author_admin,
                                false,
                                false,
                                false,
                                false,
                                false,
                                false
                            );
                        }
                    }
                }
                $smarty->assign('original_post_html', $original_post_html);
                $smarty->assign('simple_posts_html', $simple_posts_html);
                $news_html .= $smarty->fetch('thread.tpl');
                $simple_posts_html = '';
            }

            break;
        }
    }

    // Generate main page html-code and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('news_html', $news_html);
    $smarty->assign('version', '$Revision: 473 $');
    $smarty->assign('last_modification',
                    '$Date: 2011-08-03 19:34:46 +0400 (Wed, 03 Aug 2011) $');
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->display('news.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch (KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    if (!isset($smarty)) {
        $smarty = new SmartyKotobaSetup();
    }
    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
