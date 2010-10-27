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

// Скрипт редактирование настроек пользователя.

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

    // Получение данных о стилях и языках.
    $stylesheets = stylesheets_get_all();
    $languages = languages_get_all();

    // Загрузка и сохранение настроек.
    if (isset($_POST['keyword_load'])) {
        session_destroy();
        kotoba_session_start();
        $keyword_hash = md5(users_check_keyword($_POST['keyword_load']));
        load_user_settings($keyword_hash);
    } elseif (isset($_POST['keyword_save'])) {
        session_destroy();
        kotoba_session_start();
        $keyword_hash = md5(users_check_keyword($_POST['keyword_save']));
        $threads_per_page = users_check_threads_per_page($_POST['threads_per_page']);
        $posts_per_thread = users_check_posts_per_thread($_POST['posts_per_thread']);
        $lines_per_post = users_check_lines_per_post($_POST['lines_per_post']);

        $stylesheet_id = stylesheets_check_id($_POST['stylesheet_id']);
        $found = false;
        foreach ($stylesheets as $stylesheet) {
            if ($stylesheet_id == $stylesheet['id']) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new NodataException(sprintf(NodataException::$messages['STYLESHEET_NOT_EXIST']), $stylesheet_id);
        }

        $language_id = languages_check_id($_POST['language_id']);
        $found = false;
        foreach ($languages as $language) {
            if ($language_id == $language['id']) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new NodataException(sprintf(NodataException::$messages['LANGUAGE_NOT_EXIST']), $language_id);
        }

        $goto = users_check_goto($_POST['goto']);
        users_edit_by_keyword($keyword_hash, $posts_per_thread, $threads_per_page, $lines_per_post, $language_id, $stylesheet_id, null, $goto);
        load_user_settings($keyword_hash); // Потому что нужно получить id пользователя.
    }

    // Язык и\или стиль оформления изменился после изменения настроек.
    if ($smarty->language != $_SESSION['language'] || $smarty->stylesheet != $_SESSION['stylesheet']) {
        $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
    }

    // Получение данных о досках и скрытых нитях.
    $boards = boards_get_visible($_SESSION['user']);
    $htfilter = function ($user, $hidden_thread) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards($boards, $htfilter, $_SESSION['user']);

    // Получение избранных нитей.
    $favorites = favorites_get_by_user($_SESSION['user']);
    $threads = array();
    foreach ($favorites as $f) {
        array_push($threads, $f['thread']);
    }
    $pfilter = function($thread, $post) {
        // Pass all posts of thread.
        return true;
    };
    $posts = posts_get_visible_filtred_by_threads($threads, $_SESSION['user'], $pfilter);
    foreach ($favorites as &$f) {
        $unread = 0;
        foreach ($posts as $post) {
            if ($f['thread']['id'] == $post['thread'] && $f['thread']['original_post'] == $post['number']) {
                $f['post'] = $post;
            }
            if ($f['thread']['id'] == $post['thread'] && $post['number'] > $f['last_readed']) {
                $unread++;
            }
        }
        $f['unread'] = $unread;
    }
    $cmp = function($a, $b) {
      if ($a['unread'] == $b['unread']) {
          return $a['post']['number'] > $b['post']['number'] ? -1 : 1;
      } else {
          return $a['unread']> $b['unread'] ? -1 : 1;
      }
    };
    usort($favorites, $cmp);

    // Формирование кода страницы и вывод.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('threads_per_page', $_SESSION['threads_per_page']);
    $smarty->assign('posts_per_thread', $_SESSION['posts_per_thread']);
    $smarty->assign('lines_per_post', $_SESSION['lines_per_post']);
    $smarty->assign('languages', $languages);
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('stylesheets', $stylesheets);
    $smarty->assign('goto', $_SESSION['goto']);
    $smarty->assign('favorites', $favorites);
    $smarty->assign('hidden_threads', $hidden_threads);
    $smarty->assign('boards', $boards);
    $smarty->display('edit_settings.tpl');

    // Освобождение ресурсов и очистка.
    DataExchange::releaseResources();

    exit(0);
} catch (FormatException $fe) {
    $smarty->assign('msg', $fe->__toString());
    DataExchange::releaseResources();
    if ($fe->getReason() == FormatException::$messages['STYLESHEET_ID'] || $fe->getReason() == FormatException::$messages['LANGUAGE_ID']) {
        header('Location: http://z0r.de/?id=114');
        exit;
    }
    die($smarty->fetch('error.tpl'));
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>