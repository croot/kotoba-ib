<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

// Скрипт главной страницы имейджборды.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

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

    $boards = boards_get_visible($_SESSION['user']);
    if (count($boards) > 0) {
        $smarty->assign('boards_exist', true);
        $smarty->assign('boards', $boards);
    }

    $news_html = "";
    foreach ($boards as $board) {
        if ($board['name'] == Config::NEWS_BOARD) {
            $smarty->assign('board', $board);

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

            $news_thread_html = '';
            $news_posts_html = '';

            foreach ($threads as $t) {
                $smarty->assign('thread', $t);
                foreach ($posts as $p) {

                    // Сообщение принадлежит текущей нити.
                    if ($t['id'] == $p['thread']) {

                        $author_admin = false;
                        foreach ($admins as $admin) {
                            if ($p['user'] == $admin['id']) {
                                $author_admin = true;
                                break;
                            }
                        }

                        // Имя отправителя по умолчанию.
                        if (!$board['force_anonymous'] && $board['default_name'] && !$p['name']) {
                            $p['name'] = $board['default_name'];
                        }

                        if ($t['original_post'] == $p['number']) {

                            // Оригинальное сообщение.
                            $news_thread_html .= post_original_generate_html($smarty,
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
                                    $author_admin);
                        } else {

                            // Ответ в нить.
                            $news_posts_html .= post_simple_generate_html($smarty,
                                    $board,
                                    $t,
                                    $p,
                                    $posts_attachments,
                                    $attachments,
                                    false,
                                    null,
                                    $author_admin);
                        }
                    }
                }
                $news_thread_html .= $news_posts_html;
                $news_thread_html .= $smarty->fetch('board_thread_footer.tpl');
                $news_html .= $news_thread_html;
                $news_thread_html = '';
                $news_posts_html = '';
            }
            break;
        }
    }

    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('news_html', $news_html);
    $smarty->assign('version', '$Revision$');
    $smarty->assign('date', '$Date$');
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->display('index.tpl');

    DataExchange::releaseResources();
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
