<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Moderators main script.

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
    $is_admin = false;
    if (is_admin()) {
        $is_admin = true;
    } elseif (!is_mod()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN'] . ' ' . PermissionException::$messages['NOT_MOD']);
    }
    call_user_func(Logging::$f['MODERATE_USE']);

    $page = 1;
    if (isset($_GET['page'])) {
        $page = check_page($_GET['page']);
    }
    $page_max = 1;

    $boards = ($is_admin == true) ? boards_get_all() : boards_get_moderatable($_SESSION['user']);
    $output = '';
    $moderate_posts = array();
    $prev_filter_board = '';
    $prev_filter_date_time = '';
    $prev_filter_number = '';
    $prev_filter_ip = '';
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('is_admin', $is_admin);
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    date_default_timezone_set(Config::DEFAULT_TIMEZONE);

    // Dirty work.
    if (isset($_GET['filter'])) {
        $_POST['filter'] = 1;
        $_POST['filter_board'] = $_GET['bf'];
        $_POST['filter_date_time'] = $_GET['df'];
        $_POST['filter_number'] = $_GET['nf'];
        $_POST['filter_ip'] = $_GET['if'];
    }

    // Request posts. Apply defined filter to posts and show.
    if (isset($_POST['filter'])
            && isset($_POST['filter_board'])
            && isset($_POST['filter_date_time'])
            && isset($_POST['filter_number'])
            && isset($_POST['filter_ip'])
            && $_POST['filter_board'] != ''
            && ($_POST['filter_date_time'] != '' || $_POST['filter_number'] != '' ||  $_POST['filter_ip'] != '')) {

        // Board filter.
        if ($_POST['filter_board'] == 'all') {
            if ($is_admin) {
                $filter_boards = $boards;
                $prev_filter_board = 'all';
            } else {
                throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
            }
        } else {
            $filter_boards = array();
            foreach ($boards as $board) {
                if ($_POST['filter_board'] == $board['id']) {
                    array_push($filter_boards, $board);
                    $prev_filter_board = $board['id'];
                    break;  // Only one yet.
                }
            }
        }

        // Date-Time filter.
        if ($_POST['filter_date_time'] != '') {
            $filter_date_time = date_format(date_create($_POST['filter_date_time']), 'U');

            $posts_data = posts_get_by_boards_datetime($filter_boards, $filter_date_time, $page, 100);
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

            $prev_filter_date_time = date_format(date_create($_POST['filter_date_time']), Config::DATETIME_FORMAT);
        } elseif($_POST['filter_number'] != '') {
            $filter_number = posts_check_number($_POST['filter_number']);

            $posts_data = posts_get_by_boards_number($filter_boards, $filter_number, $page, 100);
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

            $prev_filter_number = $filter_number;
        } elseif($_POST['filter_ip'] != '') {

            $posts_data = posts_get_by_boards_ip($filter_boards, ip2long($_POST['filter_ip']), $page, 100);
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

            $prev_filter_ip = long2ip(ip2long($_POST['filter_ip']));
        }

        // Generate list of filtered posts.
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

            array_push($moderate_posts, post_moderate_generate_html($smarty,
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
            && ($_POST['ban_type'] != 'none' || $_POST['del_type'] != 'none')) {

        $posts = posts_get_by_boards($boards);
        foreach ($posts as $post) {

            // If post was marked do action.
            if (isset($_POST["mark_{$post['id']}"])) {

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

    $smarty->assign('moderate_posts', $moderate_posts);
    $smarty->assign('prev_filter_board', $prev_filter_board);
    $smarty->assign('prev_filter_date_time', $prev_filter_date_time);
    $smarty->assign('prev_filter_number', $prev_filter_number);
    $smarty->assign('prev_filter_ip', $prev_filter_ip);
    $pages = array();
    for ($i = 1; $i <= $page_max; $i++) {
        array_push($pages, $i);
    }
    $smarty->assign('pages', $pages);
    $smarty->assign('page', $page);
    $smarty->display('moderate.tpl');

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
