<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Board view script.

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

    $page = 1;
    if (isset($_GET['page'])) {
        $page = check_page($_GET['page']);
    }

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

    $threads_count = threads_get_visible_count($_SESSION['user'], $board['id']);
    $page_max = ($threads_count % $_SESSION['threads_per_page'] == 0
        ? (int)($threads_count / $_SESSION['threads_per_page'])
        : (int)($threads_count / $_SESSION['threads_per_page']) + 1);
    if ($page_max == 0) {
        $page_max = 1; // Important for empty boards.
    }
    if ($page > $page_max) {
        throw new LimitException(LimitException::$messages['MAX_PAGE']);
    }

    $tfilter = function($thread, $page, $threads_per_page) {
        // Количество нитей, которое нужно пропустить.
        $skip = $threads_per_page * ($page - 1);

        // Номер записи с не закреплённой нитью. Начинается с 1.
        static $number = 0;

        // Число выбранных не закреплённых нитей.
        static $received = 0;

        if ($thread['sticky']) {

            // Sticky threads shows only on 1st page.
            if ($page == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            $number++;
            if ($number > $skip && $received < $threads_per_page) {
                $received++;
                return true;
            } else {
                return false;
            }
        }
    };
    $threads = threads_get_visible_filtred_by_board($board['id'],
                                                    $_SESSION['user'],
                                                    $tfilter,
                                                    $page,
                                                    $_SESSION['threads_per_page']);
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
    $posts = posts_get_visible_filtred_by_threads($threads,
                                                  $_SESSION['user'],
                                                  $pfilter,
                                                  $_SESSION['posts_per_thread']);

    // TODO: What if attachments disabled on this board?
    $posts_attachments = posts_attachments_get_by_posts($posts);
    $attachments = attachments_get_by_posts($posts);

    $htfilter = function ($hidden_thread, $user) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards(array($board), $htfilter, $_SESSION['user']);

    $upload_types = upload_types_get_by_board($board['id']);
    if ($board['enable_macro'] === null ? Config::ENABLE_MACRO : $board['enable_macro']) {
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

    $admins = users_get_admins();

    $smarty->assign('show_control', is_admin() || is_mod());
    $board['annotation'] = $board['annotation'] ? html_entity_decode($board['annotation'], ENT_QUOTES, Config::MB_ENCODING) : $board['annotation'];
    $smarty->assign('board', $board);
    $smarty->assign('boards', $boards);
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('password', $password);
    $smarty->assign('upload_types', $upload_types);
    $pages = array();
    for ($i = 1; $i <= $page_max; $i++) {
        array_push($pages, $i);
    }
    $smarty->assign('pages', $pages);
    $smarty->assign('page', $page);
    $smarty->assign('goto', $_SESSION['goto']);
    $smarty->assign('macrochan_tags', $macrochan_tags);
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('enable_macro', $board['enable_macro'] === null ? Config::ENABLE_MACRO : $board['enable_macro']);
    $smarty->assign('enable_youtube', $board['enable_youtube'] === null ? Config::ENABLE_YOUTUBE : $board['enable_youtube']);
    $smarty->assign('enable_search', Config::ENABLE_SEARCH);
    $smarty->assign('enable_captcha', !is_admin() && (($board['enable_captcha'] === null && Config::ENABLE_CAPTCHA) || $board['enable_captcha']));
    $smarty->assign('enable_translation', ($board['enable_translation'] === null) ? Config::ENABLE_TRANSLATION : $board['enable_translation']);
    $enable_geoip = $board['enable_geoip'] === null ? Config::ENABLE_GEOIP : $board['enable_geoip'];
    $smarty->assign('enable_geoip', $enable_geoip);
    $smarty->assign('enable_shi', ($board['enable_shi'] === null) ? Config::ENABLE_SHI : $board['enable_shi']);
    $enable_postid = $board['enable_postid'] === null ? Config::ENABLE_POSTID : $board['enable_postid'];
    $smarty->assign('enable_postid', $enable_postid);
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
                            true,
                            $_SESSION['lines_per_post'],
                            true,
                            $_SESSION['posts_per_thread'],
                            true,
                            $author_admin,
                            $enable_geoip,
                            $enable_postid);
                } else {
                    $simple_posts_html .= post_simple_generate_html($smarty,
                            $board,
                            $t,
                            $p,
                            $posts_attachments,
                            $attachments,
                            true,
                            $_SESSION['lines_per_post'],
                            $author_admin,
                            $enable_geoip,
                            $enable_postid);
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
    //$boards_html .= $smarty->fetch('board_footer.tpl');
    //echo $boards_html;
    $smarty->display('board.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    if (isset($smarty)) {
        $smarty->assign('msg', $e->__toString());
        DataExchange::releaseResources();
        die($smarty->fetch('error.tpl'));
    } else {
        die($e->__toString());
    }
}
?>
