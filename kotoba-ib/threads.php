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

// Скрипт просмотра нити.

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
    $original_post = threads_check_original_post($_GET['thread']);

    $password = null;
    if (isset($_SESSION['password'])) {
        $password = $_SESSION['password'];
    }

    /*
     * Доски нужны для вывода списка досок, поэтому получим все и среди них
     * будем искать запрашиваемую.
     */
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

    $thread = threads_get_visible_by_original_post($board['id'], $original_post,
            $_SESSION['user']);

    // Нить была заархивирована.
    if ($thread['archived']) {
        DataExchange::releaseResources();
        header('Location: ' . Config::DIR_PATH . "/{$board['name']}/arch/" . "{$thread['original_post']}.html");
        exit;
    }

    if (threads_get_moderatable_by_id($thread['id'], $_SESSION['user']) === null) {
        $is_moderatable = false;
    } else {
        $is_moderatable = true;
    }

    $filter = function($posts_per_thread, $thread, $post) {
        static $recived = 0;
        static $prev_thread = null;

        if ($prev_thread !== $thread) {
            $recived = 0;
            $prev_thread = $thread;
        }

        if ($thread['original_post'] == $post['number']) {
            return true;
        }
        $recived++;
        if ($recived >= $thread['posts_count'] - $posts_per_thread) {
            return true;
        }
        return false;
    };
    $posts = posts_get_visible_filtred_by_threads(array($thread), $_SESSION['user'], $filter, $thread['posts_count']);

    $posts_attachments = posts_attachments_get_by_posts($posts);
    $attachments = attachments_get_by_posts($posts);

    $ht_filter = function($user, $hidden_thread) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards(array($board), $ht_filter, $_SESSION['user']);

    $upload_types = upload_types_get_by_board($board['id']);
    $macrochan_tags = macrochan_tags_get_all();

    if ($banners_board_id) {
        $banners = images_get_by_board($banners_board_id);
        $smarty->assign('banner', $banners[rand(0, count($banners) - 1)]);
    }

    $board['annotation'] = html_entity_decode($board['annotation'], ENT_QUOTES, Config::MB_ENCODING);
    $smarty->assign('board', $board);
    $smarty->assign('boards', $boards);
    $smarty->assign('threads', array($thread));
    $smarty->assign('thread', $thread);
    $smarty->assign('is_moderatable', $is_moderatable);
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('password', $password);
    $smarty->assign('upload_types', $upload_types);
    $smarty->assign('goto', $_SESSION['goto']);
    $smarty->assign('macrochan_tags', $macrochan_tags);
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('enable_macro', $board['enable_macro'] === null ? Config::ENABLE_MACRO : $board['enable_macro']);
    $smarty->assign('enable_youtube', $board['enable_youtube'] === null ? Config::ENABLE_YOUTUBE : $board['enable_youtube']);
    $smarty->assign('enable_captcha', !is_admin() && (($board['enable_captcha'] === null && Config::ENABLE_CAPTCHA) || $board['enable_captcha']));
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    $smarty->assign('name', $_SESSION['name']);

    //event_daynight($smarty);

    $view_html = $smarty->fetch('threads_header.tpl');
    $view_thread_html = '';
    $view_posts_html = '';
    $original_post = null; // Оригинальное сообщение с допольнительными полями.
    $original_attachments = array(); // Массив вложений оригинального сообщения.
    $simple_attachments = array(); // Массив вложений сообщения.
    foreach ($posts as $p) {

        // Имя отправителя по умолчанию.
        if (!$board['force_anonymous'] && $board['default_name'] !== null && $p['name'] === null) {
            $p['name'] = $board['default_name'];
        }

        if ($thread['original_post'] == $p['number']) {

            // Оригинальное сообщение.
            $view_thread_html = post_original_generate_html($smarty,
                    $board,
                    $thread,
                    $p,
                    $posts_attachments,
                    $attachments,
                    false,
                    null,
                    false,
                    null,
                    false);
        } else {

            // Ответ в нить.
            $view_posts_html .= post_simple_generate_html($smarty,
                    $board,
                    $thread,
                    $p,
                    $posts_attachments,
                    $attachments,
                    false,
                    null);
        }
    }
    $view_html .= $view_thread_html . $view_posts_html;
    $smarty->assign('hidden_threads', $hidden_threads);
    $view_html .= $smarty->fetch('threads_footer.tpl');
    DataExchange::releaseResources();
    echo $view_html;
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>