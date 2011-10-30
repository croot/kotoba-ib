<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Moderators main script.

require_once '../config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/logging.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if (!isset($_SERVER['REMOTE_ADDR'])
            || ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === FALSE) {

        throw new RemoteAddressException();
    }
    if ( ($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Check permission and write message to log file.
    $is_admin = is_admin();
    if (!$is_admin && !is_mod()) {

        // Cleanup.
        DataExchange::releaseResources();

        $ERRORS['NOT_MOD']($smarty);
        exit(1);
    }
    call_user_func(Logging::$f['MODERATE_USE']);

    $REQ = $_REQUEST;

    $f = array();   // Filter params.

    $action = FALSE;
    $a = array();   // Action params.

    $page = 1;
    $page_max = 1;
    if (isset($REQ['page'])) {
        $page = check_page($REQ['page']);
    }

    $f['board'] = '';
    $f['date_time'] = '';
    $f['number'] = '';
    $f['ip'] = '';
    if (isset($REQ['filter'])) {
        if (isset($REQ['filter']['board'])) {
            if ($REQ['filter']['board'] == 'all') {
                $f['board'] = 'all';
            } else {
                $f['board'] = boards_check_id($REQ['filter']['board']);
            }
        }

        if (isset($REQ['filter']['date_time'])
                && $REQ['filter']['date_time'] != '') {

            $f['date_time'] = $REQ['filter']['date_time'];
            $f['date_time'] = date_format(date_create($f['date_time']), 'U');
        }

        if (isset($REQ['filter']['number']) && $REQ['filter']['number'] != '') {
            $f['number'] = posts_check_number($REQ['filter']['number']);
        }

        if (isset($REQ['filter']['ip']) && $REQ['filter']['ip'] != '') {
            $f['ip'] = ip2long($REQ['filter']['ip']);
        }
    }

    $a['ban_type'] = '';
    $a['del_type'] = '';
    if (isset($REQ['action'])) {
        if (isset($REQ['action']['ban_type'])) {
            if ($REQ['action']['ban_type'] == 'simple') {
                $a['ban_type'] = 'simple';
            } else if ($REQ['action']['ban_type'] == 'hard') {
                $a['ban_type'] = 'hard';
            } else if ($REQ['action']['ban_type'] == 'none') {
                $a['ban_type'] = 'none';
            }
        }

        if (isset($REQ['action']['del_type'])) {
            if ($REQ['action']['del_type'] == 'post') {
                $a['del_type'] = 'post';
            } else if ($REQ['action']['del_type'] == 'file') {
                $a['del_type'] = 'file';
            } else if ($REQ['action']['del_type'] == 'last') {
                $a['del_type'] = 'last';
            } else if ($REQ['action']['del_type'] == 'none') {
                $a['del_type'] = 'none';
            }
        }
    }

    if (isset($REQ['do_action'])) {
        $action = TRUE;
    }

    $boards = ($is_admin == true) ? boards_get_all()
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

    date_default_timezone_set(Config::DEFAULT_TIMEZONE);

    if ($action && isset($REQ['marked'])) {

        // Check add post id's.
        for ($i = 0; $i < count($REQ['marked']); $i++) {
            $REQ['marked'][$i] = posts_check_id($REQ['marked'][$i]);
        }

        // Now post id's are safe. Get post by it's id's.
        $posts = posts_get_by_ids($REQ['marked']);

        // Do action for each marked post.
        foreach ($posts as $post) {

            // Ban poster.
            switch ($a['ban_type']) {
                case 'simple':

                    // Ban for 1 hour by default.
                    bans_add($post['ip'],
                             $post['ip'],
                             'Banned via Moderator\\\'s Main Script.',
                             date(Config::DATETIME_FORMAT, time() + (60 * 60)));
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
                    posts_delete_last($post['id'],
                                      date(Config::DATETIME_FORMAT,
                                           time() - (60 * 60)));
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

                $ERRORS['NOT_ADMIN']($smarty);
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
            $f['date_time'] = $REQ['filter']['date_time'];
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
        $page_max = ($posts_data['count'] % 100 == 0
                     ? (int)($posts_data['count'] / 100)
                     : (int)($posts_data['count'] / 100) + 1);
        if ($page_max == 0) {
            $page_max = 1;
        }
        if ($page > $page_max) {

            // Cleanup.
            DataExchange::releaseResources();
            Logging::close_log();

            $ERRORS['MAX_PAGE']($smarty, $page);
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
    die($smarty->fetch('exception.tpl'));
}
?>
