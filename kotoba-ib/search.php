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

    if (isset($_POST['search'])) {

        // Check input parameters.
        if (!isset($_POST['search']['keyword']) || mb_strlen($_POST['search']['keyword'], Config::MB_ENCODING) < 4) {
            throw new NodataException(NodataException::$messages['SEARCH_KEYWORD']);
        }

        posts_check_text_size($_POST['search']['keyword']);
        $keyword = htmlentities($_POST['search']['keyword'], ENT_QUOTES, Config::MB_ENCODING);
        $keyword = str_replace('\\', '\\\\', $keyword);
        posts_check_text_size($keyword);
        posts_check_text($keyword);
        $keyword = addcslashes($keyword, '%_');

        $search_boards = array();
        if (!isset($_POST['search']['boards'])) {
            $search_boards = $boards;
        } else {
            foreach ($_POST['search']['boards'] as $id) {
                $id = boards_check_id($id);
                foreach ($boards as $board) {
                    if ($board['id'] == $id) {
                        array_push($search_boards, $board);
                        break;
                    }
                }
            }
        }

        $posts = posts_search_visible_by_boards($search_boards, $keyword, users_check_id($_SESSION['user']));
    }

    DataExchange::releaseResources();

    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    @$smarty->assign('pages', $pages);
    @$smarty->assign('page', $page);

    $search_html = $smarty->fetch('search_header.tpl');
    $search_html .= $smarty->fetch('search_footer.tpl');
    echo $search_html;

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>