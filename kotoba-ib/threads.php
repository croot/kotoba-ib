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

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/events.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/errors.php";
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

    // Fix for Firefox.
    header("Cache-Control: private");

    $board_name = boards_check_name($_GET['board']);
    $thread_number = threads_check_original_post($_GET['thread']);

    $password = null;
    if (isset($_SESSION['password'])) {
        $password = $_SESSION['password'];
    }

    $boards = boards_get_visible($_SESSION['user']);
    $board = null;
    $banners_board_id = null;
    foreach ($boards as $b) {
        if ($b['name'] == $board_name) {
            $board = $b;
        }

        if ($b['name'] == Config::BANNERS_BOARD) {
            $banners_board_id = $b['id'];
        }
    }
    if (!$board) {
        throw new NodataException(NodataException::$messages['BOARD_NOT_FOUND']);
    }

    $thread = threads_get_visible_by_original_post($board['id'], $thread_number, $_SESSION['user']);

    // Redirection to archived thread.
    if ($thread['archived']) {
        DataExchange::releaseResources();
        header('Location: ' . Config::DIR_PATH . "/{$board['name']}/arch/" . "{$thread['original_post']}.html");
        exit(0);
    }

    if (threads_get_moderatable_by_id($thread['id'], $_SESSION['user']) === NULL) {
        $is_moderatable = false;
    } else {
        $is_moderatable = true;
    }

    $pfilter = function($posts_per_thread, $thread, $post) {
        static $recived = 0;
        static $prev_thread = null;

        if ($prev_thread !== $thread) {
            $recived = 0;
            $prev_thread = $thread;
        }

        if ($thread['original_post'] == $post['post_number']) {
            return true;
        }
        $recived++;
        if ($recived >= $thread['posts_count'] - $posts_per_thread) {
            return true;
        }
        return false;
    };
    $posts = posts_get_visible_filtred_by_threads(array($thread),
                                                  $_SESSION['user'],
                                                  $pfilter,
                                                  $thread['posts_count']);

    $posts_attachments = posts_attachments_get_by_posts($posts);
    $attachments = attachments_get_by_posts($posts);

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
    if ($board['enable_macro'] === null ? Config::ENABLE_MACRO : $board['enable_macro']) {
        $macrochan_tags = macrochan_tags_get_all();
    } else {
        $macrochan_tags = array();
    }

    $admins = users_get_admins();

    $smarty->assign('thread', $thread);
    $smarty->assign('enable_translation', ($board['enable_translation'] === null) ? Config::ENABLE_TRANSLATION : $board['enable_translation']);
    $smarty->assign('show_control', is_admin() || is_mod());
    $board['annotation'] = $board['annotation'] ? html_entity_decode($board['annotation'], ENT_QUOTES, Config::MB_ENCODING) : $board['annotation'];
    $smarty->assign('boards', $boards);
    if ($banners_board_id) {
        $banners = images_get_by_board($banners_board_id);
        if (count($banners) > 0) {
            $smarty->assign('banner', $banners[rand(0, count($banners) - 1)]);
        }
    }
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('MAX_FILE_SIZE', Config::MAX_FILE_SIZE);
    $smarty->assign('board', $board);
    $smarty->assign('name', $_SESSION['name']);
    isset($_GET['quote']) && $smarty->assign('quote', kotoba_intval($_GET['quote']));
    isset($_SESSION['oekaki']) && $smarty->assign('oekaki', $_SESSION['oekaki']);
    $enable_macro = $board['enable_macro'] === null ? Config::ENABLE_MACRO : $board['enable_macro'];
    $smarty->assign('enable_macro', $enable_macro);
    if ($enable_macro) {
        $smarty->assign('macrochan_tags', $macrochan_tags);
    }
    $smarty->assign('enable_youtube', $board['enable_youtube'] === null ? Config::ENABLE_YOUTUBE : $board['enable_youtube']);
    $smarty->assign('enable_captcha', !is_admin() && (($board['enable_captcha'] === null && Config::ENABLE_CAPTCHA) || $board['enable_captcha']));
    $smarty->assign('password', $password);
    $smarty->assign('goto', $_SESSION['goto']);
    $smarty->assign('upload_types', $upload_types);
    $smarty->assign('enable_shi', ($board['enable_shi'] === null) ? Config::ENABLE_SHI : $board['enable_shi']);
    $smarty->assign('is_moderatable', $is_moderatable);
    $smarty->assign('threads', array($thread));
    
    $enable_geoip = $board['enable_geoip'] === null ? Config::ENABLE_GEOIP : $board['enable_geoip'];
    $smarty->assign('enable_geoip', $enable_geoip);
    $enable_postid = $board['enable_postid'] === null ? Config::ENABLE_POSTID : $board['enable_postid'];
    $smarty->assign('enable_postid', $enable_postid);

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
        $author_admin = false;
        foreach ($admins as $admin) {
            if ($p['user'] == $admin['id']) {
                $author_admin = true;
                break;
            }
        }

        // Set default post author name if enabled.
        if (!$board['force_anonymous'] && $board['default_name'] !== null && $p['name'] === null) {
            $p['name'] = $board['default_name'];
        }

        // Original post or reply.
        if ($thread['original_post'] == $p['number']) {
            $original_post_html = post_original_generate_html($smarty,
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
                    $enable_geoip,
                    $enable_postid);
        } else {
            $simple_posts_html .= post_simple_generate_html($smarty,
                    $board,
                    $thread,
                    $p,
                    $posts_attachments,
                    $attachments,
                    false,
                    null,
                    $author_admin,
                    $enable_geoip,
                    $enable_postid);
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
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>