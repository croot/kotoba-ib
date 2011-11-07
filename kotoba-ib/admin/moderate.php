<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Moderators main script.

require_once dirname(dirname(__FILE__)) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH
                . "/locale/{$_SESSION['language']}/messages.php";
        require Config::ABS_PATH
                . "/locale/{$_SESSION['language']}/logging.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ban = bans_check(get_remote_addr())) !== FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        $smarty->display('banned.tpl');

        session_destroy();
        exit(1);
    }

    // Check permission and write message to log file.
    $is_admin = is_admin();
    if (!$is_admin && !is_mod()) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new NotModError());
        exit(1);
    }
    call_user_func(Logging::$f['MODERATE_USE']);

    $_REQUEST = $_REQUEST;

    $f = array();   // Filter.

    $do_action = FALSE;
    $a = array();   // Action.

    $page = 1;
    if (isset($_REQUEST['page'])) {
        $page = check_page($_REQUEST['page']);
    }

    $f['board'] = '';
    $f['date_time'] = '';
    $f['number'] = '';
    $f['ip'] = '';
    if (isset($_REQUEST['filter'])) {
        if (isset($_REQUEST['filter']['board'])) {
            if ($_REQUEST['filter']['board'] == 'all') {
                $f['board'] = 'all';
            } else {
                $f['board'] = boards_check_id($_REQUEST['filter']['board']);
            }
        }

        if (isset($_REQUEST['filter']['date_time'])
                && $_REQUEST['filter']['date_time'] != '') {

            $f['date_time'] = $_REQUEST['filter']['date_time'];
            $f['date_time'] = date_format(date_create($f['date_time']), 'U');
        }

        if (isset($_REQUEST['filter']['number'])
                && $_REQUEST['filter']['number'] != '') {

            $f['number'] = posts_check_number($_REQUEST['filter']['number']);
        }

        if (isset($_REQUEST['filter']['ip'])
                && $_REQUEST['filter']['ip'] != '') {

            $f['ip'] = ip2long($_REQUEST['filter']['ip']);
        }
    }

    $a['ban_type'] = '';
    $a['del_type'] = '';
    if (isset($_REQUEST['action'])) {
        if (isset($_REQUEST['action']['ban_type'])) {
            if ($_REQUEST['action']['ban_type'] == 'simple') {
                $a['ban_type'] = 'simple';
            } else if ($_REQUEST['action']['ban_type'] == 'hard') {
                $a['ban_type'] = 'hard';
            } else if ($_REQUEST['action']['ban_type'] == 'none') {
                $a['ban_type'] = 'none';
            }
        }

        if (isset($_REQUEST['action']['del_type'])) {
            if ($_REQUEST['action']['del_type'] == 'post') {
                $a['del_type'] = 'post';
            } else if ($_REQUEST['action']['del_type'] == 'file') {
                $a['del_type'] = 'file';
            } else if ($_REQUEST['action']['del_type'] == 'last') {
                $a['del_type'] = 'last';
            } else if ($_REQUEST['action']['del_type'] == 'none') {
                $a['del_type'] = 'none';
            }
        }
    }

    if (isset($_REQUEST['do_action'])) {
        $do_action = TRUE;
    }

    $boards = ($is_admin == TRUE) ? boards_get_all()
                                  : boards_get_moderatable($_SESSION['user']);
    $filter_boards = array();
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

    if ($do_action && isset($_REQUEST['marked'])) {

        // Check add post id's.
        for ($i = 0; $i < count($_REQUEST['marked']); $i++) {
            $_REQUEST['marked'][$i] = posts_check_id($_REQUEST['marked'][$i]);
        }

        // Now post id's are safe. Get post by it's id's.
        $posts = posts_get_by_ids($_REQUEST['marked']);

        // Do action for each marked post.
        foreach ($posts as $post) {

            // Ban poster.
            switch ($a['ban_type']) {
                case 'simple':

                    // Ban for 1 hour by default.
                    bans_add(
                        $post['ip'],
                        $post['ip'],
                        'Banned via Moderator\\\'s Main Script.',
                        date(Config::DATETIME_FORMAT, time() + (60 * 60))
                    );
                    break;
                case 'hard':
                    hard_ban_add($post['ip'], $post['ip']);
                    break;
            }

            // Delete posts or attachments.
            switch ($a['del_type']) {
                case 'post':
                    posts_delete($post['id']);
                    break;
                case 'file':
                    posts_attachments_delete_by_post($post['id']);
                    break;
                case 'last':

                    // Delete all posts posted from this IP-address in last
                    // hour.
                    posts_delete_last(
                        $post['id'],
                        date(Config::DATETIME_FORMAT,time() - (60 * 60))
                    );
                    break;
            }
        }
    }

    if ($f['board'] != '' && ($f['date_time'] != ''
                                  || $f['number'] != ''
                                  || $f['ip'] != '')) {

        // Filter boards.
        if ($f['board'] == 'all') {
            if ($is_admin) {
                $filter_boards = $boards;
            } else {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, new NotAdminError());
                exit(1);
            }
        } else {
            foreach ($boards as $board) {
                if ($f['board'] == $board['id']) {
                    array_push($filter_boards, $board);
                    break;
                }
            }
        }

        // Filter posts.
        if ($f['date_time'] != '') {
            $posts_data = posts_get_by_boards_datetime($filter_boards,
                                                       $f['date_time'],
                                                       $page,
                                                       100);
            $posts = $posts_data['posts'];
            $f['date_time'] = $_REQUEST['filter']['date_time'];
            $f['date_time'] = date_format(date_create($f['date_time']),
                                          Config::DATETIME_FORMAT);
        } else if ($f['number'] != '') {
            $posts_data = posts_get_by_boards_number($filter_boards,
                                                     $f['number'],
                                                     $page,
                                                     100);
            $posts = $posts_data['posts'];
        } else if ($f['ip'] != '') {
            $posts_data = posts_get_by_boards_ip($filter_boards,
                                                 $f['ip'],
                                                 $page,
                                                 100);
            $posts = $posts_data['posts'];
            $f['ip'] = long2ip($f['ip']);
        }

        // We already select posts but anyway we need to calculate
        // pages count and check what page was correct.
        $page_max = ceil($posts_data['count'] / 100);
        if ($page_max == 0) {
            $page_max = 1;
        }
        if ($page > $page_max) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            display_error_page($smarty, new MaxPageError($page));
            exit(1);
        }

        // Generate html.
        $posts_attachments = posts_attachments_get_by_posts($posts);
        $attachments = attachments_get_by_posts($posts);
        foreach ($posts as $post) {
            array_push($moderate_posts,
                       post_moderate_generate_html(
                           $smarty,
                           $post,
                           $posts_attachments,
                           $attachments,
                           users_is_admin($post['user'])
                       ));
        }
    }

    $smarty->assign('moderate_posts', $moderate_posts);
    $smarty->assign('filter', $f);

    $pages = array();
    if (isset($page_max)) {
        $pages = range(1, $page_max);
    }

    $smarty->assign('page', $page);
    $smarty->assign('pages', $pages);
    $smarty->display('moderate.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
