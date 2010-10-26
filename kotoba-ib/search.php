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

// Скрипт поиска сообщений.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Инициализация.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Проверка, не заблокирован ли клиент.
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

    // Проверка входных параметров и получение данных о досках.
    $boards = boards_get_visible($_SESSION['user']);
    $REQUEST = "_{$_SERVER['REQUEST_METHOD']}";
    $REQUEST = $$REQUEST;
    $page = 1;
    if (isset($REQUEST['search']['page'])) {
        $page = check_page($REQUEST['search']['page']);
    }

    $posts_per_page = 10;   // Число сообщений на странице.
    $pages = array();
    $keyword = '';
    $search_result = '';

    // Осуществляется поиск.
    if (isset($REQUEST['search'])) {

        // Проверка входых параметров поиска.
        if (!isset($REQUEST['search']['keyword'])
                || mb_strlen($REQUEST['search']['keyword'], Config::MB_ENCODING) < 4) {

            throw new NodataException(NodataException::$messages['SEARCH_KEYWORD']);
        }
        posts_check_text_size($REQUEST['search']['keyword']);

        // Преобразуем кавычки, угловые скобки и знак процента в соответствующие html сущности.
        $keyword = htmlentities($REQUEST['search']['keyword'], ENT_QUOTES, Config::MB_ENCODING);
        
        // Заэкранируем экранирующие символы.
        $keyword = str_replace('\\', '\\\\', $keyword);

        /*
         * Ключевая фраза для поиска в тексте сообщения не может быть больше
         * максимально возможного текста сообщения. Так же, ключевая фраза
         * не может содержать не верных юникод символов и управляющих символов
         * ASCII.
         */
        posts_check_text_size($keyword);
        posts_check_text($keyword);

        // Экранирование символов % и _
        $keyword = addcslashes($keyword, '%_');

        // Выбор досок для поиска.
        $search_boards = array();
        if (!isset($REQUEST['search']['boards'])) {
            $search_boards = $boards;
        } else {
            foreach ($REQUEST['search']['boards'] as $id) {
                $id = boards_check_id($id);
                foreach ($boards as &$board) {
                    if ($board['id'] == $id) {

                        /*
                         * Добавление фиктивного поля, которое указывает, что
                         * на доске производится поиск.
                         */
                        $board = array_merge($board, array('selected' => true));

                        array_push($search_boards, $board);
                        break;
                    }
                }
            }
        }

        // Поиск.
        $posts = posts_search_visible_by_boards($search_boards, $keyword, users_check_id($_SESSION['user']));

        // Формирование кода заголовка результатов поиска.
        $smarty->assign('count', count($posts));
        $search_result .= $smarty->fetch('search_result.tpl');

        // Вычисление числа страниц, на которых будут размещены найденные сообщения.
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

        // Из всех найденных сообщений выбираются только сообщения с нужной страницы.
        $posts = array_slice($posts, ($page - 1) * $posts_per_page, $posts_per_page);

        $admins = users_get_admins();

        // Формирование кода найденных сообщений.
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

            // Является ли автор сообщения администратором?
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

    // Освобождение ресурсов и очистка.
    DataExchange::releaseResources();

    // Формирование кода страницы поиска и вывод.
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