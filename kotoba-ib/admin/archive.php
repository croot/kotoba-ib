<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Archive thread script.

require_once '../config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/errors.php";
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/logging.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === FALSE) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if ( ($ban = bans_check($ip)) !== FALSE) {
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
    call_user_func(Logging::$f['ARCHIVE_USE']);

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
        $original_post = null;              // Original message with additional fields.
        $original_attachments = array();    // Attachments of original message.
        $simple_attachments = array();      // Attachments of replies.

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
        // Now skipped due debug.

        echo "Thread {$thread['original_post']} saved.<br>";
    }

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
