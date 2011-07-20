<?php
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

// Script of imageboard main page.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
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
    if (!isset($_SERVER['REMOTE_ADDR'])
            || ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === FALSE) {

        DataExchange::releaseResources();
        $ERRORS['REMOTE_ADDR']($smarty);
    }
    if ( ($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);

    // Make category-boards tree for navigation panel.
    foreach ($categories as &$c) {
        $c['boards'] = array();
        foreach ($boards as $b) {
            if ($b['category'] == $c['id'] && !in_array($b['name'], Config::$INVISIBLE_BOARDS)) {
                array_push($c['boards'], $b);
            }
        }
    }

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
            $threads = threads_get_visible_filtred_by_board($board['id'], $_SESSION['user'], $tfilter);

            // Pass all posts.
            $pfilter = function($thread, $post) {
                return true;
            };
            $posts = posts_get_visible_filtred_by_threads($threads, $_SESSION['user'], $pfilter);

            $posts_attachments = posts_attachments_get_by_posts($posts);
            $attachments = attachments_get_by_posts($posts);

            $admins = users_get_admins();

            $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
            $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
            $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
            $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);

            $simple_posts_html = '';

            foreach ($threads as $t) {
                foreach ($posts as $p) {
                    if ($t['id'] == $p['thread']['id']) {

                        // Find if author of this post is admin.
                        $author_admin = false;
                        foreach ($admins as $admin) {
                            if ($p['user'] == $admin['id']) {
                                $author_admin = true;
                                break;
                            }
                        }

                        // Set default post author name if enabled.
                        if (!$board['force_anonymous'] && $board['default_name'] && !$p['name']) {
                            $p['name'] = $board['default_name'];
                        }

                        // Original post or reply.
                        if ($t['original_post'] == $p['number']) {
                            $original_post_html = post_original_generate_html($smarty,
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
                                    false);
                        } else {
                            $simple_posts_html .= post_simple_generate_html($smarty,
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
                                    false);
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
    $smarty->assign('categories', $categories);
    $smarty->assign('boards', $boards);
    $smarty->assign('news_html', $news_html);
    $smarty->assign('version', '$Revision$');
    $smarty->assign('last_modification', '$Date$');
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->display('index.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
