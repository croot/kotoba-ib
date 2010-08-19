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

/**
 * Скрипт просмотра досок.
 * @package userscripts
 */

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/events.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'],
            $_SESSION['stylesheet']);

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

    $page = 1;
    if (isset($_GET['page'])) {
        $page = check_page($_GET['page']);
    }

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
    foreach ($boards as $b) {
        if ($b['name'] == $board_name) {
            $board = $b;
            break;
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

    $threads = threads_get_visible_by_board($board['id'], $page,
            $_SESSION['user'], $_SESSION['threads_per_page']);

    $p_filter = function($posts_per_thread, $thread, $post) {
        static $recived = 0;
        if ($thread['original_post'] == $post['number']) {
            return true;
        }
        $recived++;
        if ($recived > $thread['posts_count'] - $posts_per_thread) {
            return true;
        }
        return false;
    };
    $posts = posts_get_visible_filtred_by_threads($threads, $_SESSION['user'],
            $p_filter, $_SESSION['posts_per_thread']);

    $posts_attachments = posts_attachments_get_by_posts($posts);
    $attachments = attachments_get_by_posts($posts);

    $ht_filter = function ($user, $hidden_thread) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards(array($board),
            $ht_filter, $_SESSION['user']);

    $upload_types = upload_types_get_by_board($board['id']);
    $macrochan_tags = macrochan_tags_get_all();

    $board['annotation'] = html_entity_decode($board['annotation'], ENT_QUOTES,
            Config::MB_ENCODING);
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
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);

    //event_daynight($smarty);

    $boards_html = $smarty->fetch('board_header.tpl');
    $boards_thread_html = ''; // Код предпросмотра нити.
    $boards_posts_html = ''; // Код сообщений из препдпросмотра нитей.
    $recived_posts_count = 0; // Количество показанных сообщений в предпросмотре нити.
    $original_post = null; // Оригинальное сообщение с допольнительными полями.
    $original_attachments = array(); // Массив вложений оригинального сообщения.
    $simple_attachments = array(); // Массив вложений сообщения.
    foreach ($threads as $t) {
        $smarty->assign('thread', $t);
        foreach ($posts as $p) {

            // Сообщение принадлежит текущей нити.
            if ($t['id'] == $p['thread']) {
                $recived_posts_count++;

                // Имя отправителя по умолчанию.
                if (!$board['force_anonymous'] && $board['default_name'] && !$p['name']) {
                    $p['name'] = $board['default_name'];
                }

                // Оригинальное сообщение.
                if ($t['original_post'] == $p['number']) {
                    $p['with_attachments'] = false; // Fake field.
                    $p['text'] = posts_corp_text($p['text'], $_SESSION['lines_per_post'], $is_cutted);
                    $p['text_cutted'] = $is_cutted; // Fake field.
                    foreach ($posts_attachments as $pa) {
                        if ($pa['post'] == $p['id']) {
                            foreach ($attachments as $a) {
                                if ($a['attachment_type'] == $pa['attachment_type']) {
                                    switch ($a['attachment_type']) {
                                        case Config::ATTACHMENT_TYPE_FILE:
                                            if ($a['id'] == $pa['file']) {
                                                $a['file_link'] = Config::DIR_PATH . "/{$board['name']}/other/{$a['name']}";
                                                $a['thumbnail_link'] = Config::DIR_PATH . "/img/{$a['thumbnail']}";
                                                $p['with_attachments'] = true;
                                                array_push($original_attachments, $a);
                                            }
                                            break;
                                        case Config::ATTACHMENT_TYPE_IMAGE:
                                            if ($a['id'] == $pa['image']) {
                                                $a['image_link'] = Config::DIR_PATH . "/{$board['name']}/img/{$a['name']}";
                                                $a['thumbnail_link'] = Config::DIR_PATH . "/{$board['name']}/thumb/{$a['thumbnail']}";
                                                $p['with_attachments'] = true;
                                                array_push($original_attachments, $a);
                                            }
                                            break;
                                        case Config::ATTACHMENT_TYPE_LINK:
                                            if ($a['id'] == $pa['link']) {
                                                $p['with_attachments'] = true;
                                                array_push($original_attachments, $a);
                                            }
                                            break;
                                        case Config::ATTACHMENT_TYPE_VIDEO:
                                            if ($a['id'] == $pa['video']) {
                                                $smarty->assign('code', $a['code']);
                                                $a['video_link'] = $smarty->fetch('youtube.tpl');
                                                $p['with_attachments'] = true;
                                                array_push($original_attachments, $a);
                                            }
                                            break;
                                        default:
                                            throw new CommonException('Not supported.');
                                            break;
                                    }
                                }
                            }
                        }
                    }
                    $p['ip'] = long2ip($p['ip']);
                    $original_post = $p;
                } else { // Ответ в нить.
                    $p['with_attachments'] = false; // Fake field.
                    $p['text'] = posts_corp_text($p['text'],
                            $_SESSION['lines_per_post'], $is_cutted);
                    $p['text_cutted'] = $is_cutted; // Fake field.
                    foreach ($posts_attachments as $pa) {
                        if ($pa['post'] == $p['id']) {
                            foreach ($attachments as $a) {
                                if ($a['attachment_type'] == $pa['attachment_type']) {
                                    switch ($a['attachment_type']) {
                                        case Config::ATTACHMENT_TYPE_FILE:
                                            if ($a['id'] == $pa['file']) {
                                                $a['file_link'] = Config::DIR_PATH . "/{$board['name']}/other/{$a['name']}";
                                                $a['thumbnail_link'] = Config::DIR_PATH . "/img/{$a['thumbnail']}";
                                                $p['with_attachments'] = true;
                                                array_push($simple_attachments, $a);
                                            }
                                            break;
                                        case Config::ATTACHMENT_TYPE_IMAGE:
                                            if ($a['id'] == $pa['image']) {
                                                $a['image_link'] = Config::DIR_PATH . "/{$board['name']}/img/{$a['name']}";
                                                $a['thumbnail_link'] = Config::DIR_PATH . "/{$board['name']}/thumb/{$a['thumbnail']}";
                                                $p['with_attachments'] = true;
                                                array_push($simple_attachments, $a);
                                            }
                                            break;
                                        case Config::ATTACHMENT_TYPE_LINK:
                                            if ($a['id'] == $pa['link']) {
                                                $p['with_attachments'] = true;
                                                array_push($simple_attachments, $a);
                                            }
                                            break;
                                        case Config::ATTACHMENT_TYPE_VIDEO:
                                            if ($a['id'] == $pa['video']) {
                                                $smarty->assign('code', $a['code']);
                                                $a['video_link'] = $smarty->fetch('youtube.tpl');
                                                $p['with_attachments'] = true;
                                                array_push($simple_attachments, $a);
                                            }
                                            break;
                                        default:
                                            throw new CommonException('Not supported.');
                                            break;
                                    }
                                }
                            }
                        }
                    }
                    $p['ip'] = long2ip($p['ip']);
                    $smarty->assign('simple_post', $p);
                    $smarty->assign('simple_attachments', $simple_attachments);
                    $smarty->assign('thread', array($t));
                    $boards_posts_html .= $smarty->fetch('post_simple.tpl');
                    $smarty->assign('thread', $t);
                    $simple_attachments = array();
                }
            }
        }
        $smarty->assign('sticky', $t['sticky']);
        $smarty->assign('skipped', ($t['posts_count'] - $recived_posts_count));
        $smarty->assign('original_post', $original_post);
        $smarty->assign('original_attachments', $original_attachments);
        $boards_thread_html .= $smarty->fetch('board_thread_header.tpl');
        $boards_thread_html .= $boards_posts_html;
        $boards_thread_html .= $smarty->fetch('board_thread_footer.tpl');
        $boards_html .= $boards_thread_html;
        $boards_thread_html = '';
        $boards_posts_html = '';
        $recived_posts_count = 0;
        $original_post = null;
        $original_attachments = array();
    }
    $smarty->assign('hidden_threads', $hidden_threads);
    $boards_html .= $smarty->fetch('board_footer.tpl');
    DataExchange::releaseResources();
    echo $boards_html;
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>