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

// Скрипт поиска нитей и сообщений (вывод результатов как нити).

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/events.php';

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

    // Fix for Firefox.
    header("Cache-Control: private");

    $boards = boards_get_visible($_SESSION['user']);

    $REQUEST = "_{$_SERVER['REQUEST_METHOD']}";
    $REQUEST = $$REQUEST;
    $page = 1;
    if (isset($REQUEST['search']['page'])) {
        $page = check_page($REQUEST['search']['page']);
    }

    $posts_per_page = 10;
    $pages = array();
    $keyword = '';
    $search_result = '';
    if (isset($REQUEST['search'])) {

        // Check input parameters.
        if (!isset($REQUEST['search']['keyword']) || mb_strlen($REQUEST['search']['keyword'], Config::MB_ENCODING) < 4) {
            throw new NodataException(NodataException::$messages['SEARCH_KEYWORD']);
        }

        posts_check_text_size($REQUEST['search']['keyword']);

        // Convert quotes, braces and percent sign to html entities.
        $keyword = htmlentities($REQUEST['search']['keyword'], ENT_QUOTES, Config::MB_ENCODING);
        
        // Escape escape character.
        $keyword = str_replace('\\', '\\\\', $keyword);

        /*
         * Key phrase cannot be greater than post size. Also, check for bad
         * unicode and unwanted ASCII characters.
         */
        posts_check_text_size($keyword);
        posts_check_text($keyword);

        // Escape special characters % and _
        $keyword = addcslashes($keyword, '%_');

        $search_boards = array();
        if (!isset($REQUEST['search']['boards'])) {
            $search_boards = $boards;
        } else {
            foreach ($REQUEST['search']['boards'] as $id) {
                $id = boards_check_id($id);
                foreach ($boards as &$board) {
                    if ($board['id'] == $id) {

                        // Add fake field to board what means what this board was selected to search on.
                        $board = array_merge($board, array('selected' => true));

                        array_push($search_boards, $board);
                        break;
                    }
                }
            }
        }

        $posts = posts_search_visible_by_boards($search_boards, $keyword, users_check_id($_SESSION['user']));
        $admins = users_get_admins();

        $smarty->assign('count', count($posts));
        $search_result .= $smarty->fetch('search_result.tpl');

        // Calculate pages count.
        $page_max = (count($posts) % $posts_per_page == 0
            ? (int)(count($posts) / $posts_per_page)
            : (int)(count($posts) / $posts_per_page) + 1);
        if ($page_max == 0) {
            $page_max = 1;
        }
        if ($page > $page_max) {
            throw new LimitException(LimitException::$messages['MAX_PAGE']);
        }
        for ($i = 1; $i <= $page_max; $i++) {
            array_push($pages, $i);
        }

        $posts = array_slice($posts, ($page - 1) * $posts_per_page, $posts_per_page);

        foreach ($posts as $p) {

            // Geoip.
            $p['ip'] = long2ip($p['ip']);
            $smarty->assign('enable_geoip', ($p['board']['enable_geoip'] === null) ? Config::ENABLE_GEOIP : $p['board']['enable_geoip']);
            if ($p['ip'] != '127.0.0.1') {
                $geoip = geoip_record_by_name($p['ip']);
                $smarty->assign('country', array('name' => $geoip['country_name'], 'code' => strtolower($geoip['country_code'])));
            }

            // Postid.
            $smarty->assign('enable_postid', ($p['board']['enable_postid'] === null) ? Config::ENABLE_POSTID : $p['board']['enable_postid']);
            $tripcode = calculate_tripcode("#{$p['ip']}");
            $smarty->assign('postid', $tripcode[1]);

            // Author is admin?
            $author_admin = false;
            foreach ($admins as $admin) {
                if ($p['user'] == $admin['id']) {
                    $author_admin = true;
                    break;
                }
            }
            $smarty->assign('author_admin', $author_admin);

            $smarty->assign('post', $p);
            $smarty->assign('enable_translation', ($p['board']['enable_translation'] === null) ? Config::ENABLE_TRANSLATION : $p['board']['enable_translation']);

            $search_result .= $smarty->fetch('search_post.tpl');
        }
    }

    DataExchange::releaseResources();

    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('pages', $pages);
    $smarty->assign('page', $page);
    $smarty->assign('keyword', $keyword);

    echo $smarty->fetch('search_header.tpl') . $search_result . $smarty->fetch('search_footer.tpl');

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>