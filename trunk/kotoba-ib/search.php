<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Search script.
 *
 * Parameters:
 * search - array of search data:
 * search['page'] - page of search result view.
 * search['keyword'] - keyword for search.
 * search['boards'] - array of boards for search.
 */

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
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

    // Fix for Firefox.
    header("Cache-Control: private");

    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);

    // Make category-boards tree for navigation panel.
    foreach ($categories as &$c) {
        $c['boards'] = array();
        foreach ($boards as $b) {
            if ($b['category'] == $c['id'] && !in_array($b['name'], Config::$INVISIBLE_BOARDS)) {
                array_push($c['boards'], $b);
            }
        }
    }

    // Check input parameters.
    if (isset($_REQUEST['search'])) {
        $search = $_REQUEST['search'];
    }

    $page = 1;
    if (isset($search['page'])) {
        $page = check_page($search['page']);
    }
    $posts_per_page = 10;   // Count of posts per page.
    $pages = array();
    $keyword = '';
    $posts_html = '';

    // Do search.
    if (isset($_REQUEST['search'])) {

        // Check input parameters.
        if (!isset($search['keyword'])
                || mb_strlen($search['keyword'], Config::MB_ENCODING) < 4) {

            throw new NodataException(NodataException::$messages['SEARCH_KEYWORD']);
        }
        posts_check_text_size($search['keyword']);

        // Encode quotes, bracers and percent sign into html entities.
        $keyword = htmlentities($search['keyword'], ENT_QUOTES, Config::MB_ENCODING);
        
        // Strip slashes.
        $keyword = str_replace('\\', '\\\\', $keyword);

        posts_check_text_size($keyword);
        posts_check_text($keyword);

        // Strip % and _ signs.
        $keyword = addcslashes($keyword, '%_');

        // Choose boards for search.
        $search_boards = array();
        if (!isset($search['boards'])) {
            $search_boards = $boards;
        } else {
            foreach ($search['boards'] as $id) {
                $id = boards_check_id($id);
                foreach ($boards as &$board) {
                    if ($board['id'] == $id) {

                        // Fake field what means what board selected to search.
                        $board = array_merge($board, array('selected' => true));

                        array_push($search_boards, $board);
                        break;
                    }
                }
            }
        }

        // Search.
        $posts = posts_search_visible_by_boards($search_boards, $keyword, users_check_id($_SESSION['user']));

        // Assign total founded posts count here.
        $smarty->assign('count', count($posts));

        // Calculate page count.
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

        // Select posts only from correspond page.
        $posts = array_slice($posts, ($page - 1) * $posts_per_page, $posts_per_page);

        $admins = users_get_admins();

        // Create html code of founded posts.
        foreach ($posts as $p) {
            // Find if author of this post is admin.
            $author_admin = false;
            foreach ($admins as $admin) {
                if ($p['user'] == $admin['id']) {
                    $author_admin = true;
                    break;
                }
            }

            $posts_html .= post_search_generate_html($smarty, $p, $author_admin);
        }
    }

    // Generate html code of search page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('categories', $categories);
    $smarty->assign('boards', $boards);
    $smarty->assign('pages', $pages);
    $smarty->assign('page', $page);
    $smarty->assign('keyword', $keyword);
    $smarty->assign('posts_html', $posts_html);
    $smarty->display('search.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>