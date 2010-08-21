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

// Скрипт архивирования нитей.

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Check if remote host was banned.
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

    // Check permission and write message to log file.
    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    Logging::write_msg(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log',
            Logging::$messages['ADMIN_FUNCTIONS_ARCHIVE'],
            $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
    Logging::close_log();

    $threads = threads_get_archived();
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    foreach ($threads as $thread) {

        // Receive data for curent thread.
        $board = boards_get_by_id($thread['board']);
        $posts = posts_get_by_thread($thread['id']);
        $posts_attachments = posts_attachments_get_by_posts($posts);
        $attachments = attachments_get_by_posts($posts);

        // Generate output.
        $smarty->assign('board', $board);
        $smarty->assign('thread', array($thread));
        $view_html = $smarty->fetch('header.tpl');
        $view_thread_html = '';
        $view_posts_html = '';
        $original_post = null;              // Оригинальное сообщение с допольнительными полями.
        $original_attachments = array();    // Массив файлов, прикрепленных к оригинальному сообщению.
        $simple_attachments = array();      // Массив файлов, прикрепленных к сообщению.

        foreach ($posts as $p) {

            // Default name.
            if (!$board['force_anonymous'] && $board['default_name'] !== null && $p['name'] === null) {
                $p['name'] = $board['default_name'];
            }

            // Original post.
            if ($thread['original_post'] == $p['number']) {

                // By default post have no attachments. This is fake field.
                $p['with_attachments'] = false;

                foreach ($posts_attachments as $pa) {
                    if ($p['id'] == $pa['post']) {
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
                $smarty->assign('original_post', $p);
                $smarty->assign('original_attachments', $original_attachments);
                $view_thread_html = $smarty->fetch('post_original.tpl');
            } else {
                
                // By default post have no attachments. This is fake field.
                $p['with_attachments'] = false;

                foreach ($posts_attachments as $pa) {
                    if ($p['id'] == $pa['post']) {
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
                $view_posts_html .= $smarty->fetch('post_simple.tpl');
                $simple_attachments = array();
            }
        }
        $view_html .= $view_thread_html . $view_posts_html;
        $view_html .= $smarty->fetch('threads_footer.tpl');

        // Write output to file.
        $file = fopen(Config::ABS_PATH . "/{$board['name']}/arch/" . "{$thread['original_post']}.html", 'w');
        if ($file) {
            fwrite($file, $view_html);
        }
        fclose($file);

        // Remove data from database.

        echo "Thread {$thread['original_post']} saved.<br>";
    }

    DataExchange::releaseResources();
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
