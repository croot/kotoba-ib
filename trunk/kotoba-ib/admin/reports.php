<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Report handing script.

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

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

    // Check permission and write message to log file.
    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    call_user_func(Logging::$f['REPORTS_USE']);

    $boards = boards_get_all();
    $reported_posts = array();
    $smarty->assign('boards', $boards);
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    date_default_timezone_set(Config::DEFAULT_TIMEZONE);

    // Request posts. Apply defined filter to posts and show.
    if (isset($_POST['filter'])
            && isset($_POST['filter_board'])
            && $_POST['filter_board'] != '') {

        // Board filter.
        if ($_POST['filter_board'] == 'all') {
            $filter_boards = $boards;
        } else {
            $filter_boards = array();
            foreach ($boards as $board) {
                if ($_POST['filter_board'] == $board['id']) {
                    array_push($filter_boards, $board);
                    break;  // Only one yet.
                }
            }
        }

        // Generate list of filtered posts.
        $posts = posts_get_reported_by_boards($filter_boards);
        $posts_attachments = posts_attachments_get_by_posts($posts);
        $attachments = attachments_get_by_posts($posts);
        $admins = users_get_admins();
        foreach ($posts as $post) {

            // Find if author of this post is admin.
            $author_admin = false;
            foreach ($admins as $admin) {
                if ($post['user'] == $admin['id']) {
                    $author_admin = true;
                    break;
                }
            }

            array_push($reported_posts, post_report_generate_html($smarty,
                                                                  $post,
                                                                  $posts_attachments,
                                                                  $attachments,
                                                                  $author_admin));
        }
    }

    // Action on marked posts.
    if(isset($_POST['action'])
            && isset($_POST['ban_type'])
            && isset($_POST['del_type'])
            && isset($_POST['report_act'])
            && ($_POST['ban_type'] != 'none' || $_POST['del_type'] != 'none' || $_POST['report_act'])) {

        $posts = posts_get_reported_by_boards($boards);
        foreach ($posts as $post) {

            // If post was marked do action.
            if (isset($_POST["mark_{$post['id']}"])) {

                if ($_POST['report_act']) {
                    reports_delete($post['id']);
                }

                // Ban poster?
                switch ($_POST['ban_type']) {
                    case 'simple':

                        // Ban for 1 hour by default.
                        bans_add($post['ip'], $post['ip'], '', date('Y-m-d H:i:s', time() + (60 * 60)));
                        break;
                    case 'hard':
                        hard_ban_add($post['ip'], $post['ip']);
                        break;
                }

                // Remove post(s) or attachment?
                switch ($_POST['del_type']) {
                    case 'post':
                        posts_delete($post['id']);
                        break;
                    case 'file':
                        posts_attachments_delete_by_post($post['id']);
                        break;
                    case 'last':

                        // Delete all posts posted from this IP-address in last hour.
                        posts_delete_last($post['id'], date(Config::DATETIME_FORMAT, time() - (60 * 60)));
                        break;
                }
            }
        }
    }

    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('reported_posts', $reported_posts);
    $smarty->display('reports.tpl');

    DataExchange::releaseResources();
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
