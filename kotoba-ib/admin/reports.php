<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Report handing script.

require_once '../config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
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

    $page = 1;
    if (isset($_GET['page'])) {
        $page = check_page($_GET['page']);
    }
    $page_max = 1;

    $prev_filter_board = '';
    if (isset($_POST['prev_filter_board'])) {
        if ($_POST['prev_filter_board'] == 'all') {
            $prev_filter_board = 'all';
        } else {
            $prev_filter_board = boards_check_id($_POST['prev_filter_board']);
        }
    }

    $boards = boards_get_all();
    $reported_posts = array();
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    date_default_timezone_set(Config::DEFAULT_TIMEZONE);

    // Dirty work.
    if (isset($_GET['filter'])) {
        $_POST['filter'] = 1;
        $_POST['filter_board'] = $_GET['bf'];
    }

    // Action on marked posts.
    if(isset($_POST['action'])
            && isset($_POST['ban_type'])
            && isset($_POST['del_type'])
            && isset($_POST['report_act'])
            && isset($_POST['marked'])
            && is_array($_POST['marked'])
            && ($_POST['ban_type'] != 'none'
                    || $_POST['del_type'] != 'none'
                    || $_POST['report_act'])) {

        for ($i = 0; $i < count($_POST['marked']); $i++) {
            $_POST['marked'][$i] = posts_check_id($_POST['marked'][$i]);
        }

        $posts = posts_get_by_ids($_POST['marked']);

        foreach ($posts as $post) {

            if ($_POST['report_act']) {
                reports_delete($post['id']);
            }

            // Ban poster?
            switch ($_POST['ban_type']) {
                case 'simple':

                    // Ban for 1 hour by default.
                    bans_add($post['ip'],
                             $post['ip'],
                             '',
                             date('Y-m-d H:i:s', time() + (60 * 60)));
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

                    // Delete all posts posted from this IP-address in last
                    // hour.
                    posts_delete_last($post['id'],
                                      date(Config::DATETIME_FORMAT,
                                           time() - (60 * 60)));
                    break;
            }
        }
    }

    // Request posts. Apply defined filter to posts and show.
    $filter_boards = array();
    if (isset($_POST['filter'])
            && isset($_POST['filter_board'])
            && $_POST['filter_board'] != '') {

        // Board filter.
        if ($_POST['filter_board'] == 'all') {
            $filter_boards = $boards;
            $prev_filter_board = 'all';
        } else {
            foreach ($boards as $board) {
                if ($_POST['filter_board'] == $board['id']) {
                    array_push($filter_boards, $board);
                    $prev_filter_board = $board['id'];
                    break;  // Only one yet.
                }
            }
        }
    } else if ($prev_filter_board != '') {

        // Board filter.
        if ($prev_filter_board == 'all') {
            $filter_boards = $boards;
        } else {
            foreach ($boards as $board) {
                if ($prev_filter_board == $board['id']) {
                    array_push($filter_boards, $board);
                    break;  // Only one yet.
                }
            }
        }
    }

    if (count($filter_boards) > 0) {

        // Generate list of filtered posts.
        $posts_data = posts_get_reported_by_boards($filter_boards, $page, 100);
        $posts = $posts_data['posts'];

        // We already select something but anyway we need to calculate
        // pages count and check what page was correct.
        $page_max = ($posts_data['count'] % 100 == 0
                     ? (int)($posts_data['count'] / 100)
                     : (int)($posts_data['count'] / 100) + 1);
        if ($page_max == 0) {
            $page_max = 1;
        }
        if ($page > $page_max) {
            throw new LimitException(LimitException::$messages['MAX_PAGE']);
        }

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

            array_push($reported_posts,
                       post_report_generate_html($smarty,
                                                 $post,
                                                 $posts_attachments,
                                                 $attachments,
                                                 $author_admin));
        }
    }

    $pages = array();
    for ($i = 1; $i <= $page_max; $i++) {
        array_push($pages, $i);
    }
    $smarty->assign('pages', $pages);
    $smarty->assign('page', $page);
    $smarty->assign('prev_filter_board', $prev_filter_board);
    $smarty->assign('reported_posts', $reported_posts);
    $smarty->display('reports.tpl');

    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
