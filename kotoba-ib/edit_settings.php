<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit user settings script.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

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

    $stylesheets = stylesheets_get_all();
    $languages = languages_get_all();

    // Load / Save settings.
    if (isset($_POST['keyword_load'])) {
        $kotoba_session_start_time = $_SESSION['kotoba_session_start_time'];
        session_destroy();
        kotoba_session_start();
        $_SESSION['kotoba_session_start_time'] = $kotoba_session_start_time;
        if (Config::LANGUAGE != $_SESSION['language']) {
            require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
        }
        $keyword_hash = md5(users_check_keyword($_POST['keyword_load']));
        load_user_settings($keyword_hash);
        header('Location: ' . Config::DIR_PATH . "/edit_settings.php");
        DataExchange::releaseResources();
        exit(0);
    } elseif (isset($_POST['keyword_save'])) {
        $kotoba_session_start_time = $_SESSION['kotoba_session_start_time'];
        session_destroy();
        kotoba_session_start();
        $_SESSION['kotoba_session_start_time'] = $kotoba_session_start_time;
        if (Config::LANGUAGE != $_SESSION['language']) {
            require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
        }
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
            throw new NodataException(NodataException::$messages['STYLESHEET_NOT_EXIST'], $stylesheet_id);
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
            throw new NodataException(NodataException::$messages['LANGUAGE_NOT_EXIST'], $language_id);
        }

        $goto = users_check_goto($_POST['goto']);
        users_edit_by_keyword($keyword_hash,
                              $posts_per_thread,
                              $threads_per_page,
                              $lines_per_post,
                              $language_id,
                              $stylesheet_id,
                              null,
                              $goto);
        load_user_settings($keyword_hash);  // We need to get new user id.
        header('Location: ' . Config::DIR_PATH . "/edit_settings.php");
        DataExchange::releaseResources();
        exit(0);
    }

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

    // Pass all threads hidden by specific user.
    $htfilter = function ($hidden_thread, $user) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards($boards, $htfilter, $_SESSION['user']);

    // Get favorite threads. Calculate count of unread posts and sort.
    $favorites = favorites_get_by_user($_SESSION['user']);
    $threads = array();
    foreach ($favorites as $f) {
        array_push($threads, $f['thread']);
    }
    // Pass all posts of thread.
    $pfilter = function($thread, $post) {
        return true;
    };
    $posts = posts_get_visible_filtred_by_threads($threads, $_SESSION['user'], $pfilter);
    foreach ($favorites as &$f) {
        $f['unread'] = 0;
        foreach ($posts as $post) {
            if ($f['thread']['id'] == $post['thread']['id'] && $post['number'] > $f['last_readed']) {
                $f['unread']++;
            }
        }
    }
    // Order by last unread.
    $cmp = function($a, $b) {
      if ($a['unread'] == $b['unread']) {
          return $a['post']['number'] > $b['post']['number'] ? -1 : 1;
      } else {
          return $a['unread']> $b['unread'] ? -1 : 1;
      }
    };
    usort($favorites, $cmp);

    // Generate html-code of page and display.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('categories', $categories);
    $smarty->assign('boards', $boards);
    $smarty->assign('settings', $_SESSION);
    $smarty->assign('languages', $languages);
    $smarty->assign('stylesheets', $stylesheets);
    $smarty->assign('favorites', $favorites);
    $smarty->assign('hidden_threads', $hidden_threads);
    date_default_timezone_set(Config::DEFAULT_TIMEZONE);
    $smarty->assign('sess', array('expire' => session_cache_expire(),
                                  'cookie_params' => session_get_cookie_params(),
                                  'id' => session_id(),
                                  'name' => session_name(),
                                  'curtime' => time()));
    $smarty->display('edit_settings.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch (FormatException $fe) {
    $smarty->assign('msg', $fe->__toString());
    DataExchange::releaseResources();
    if ($fe->getReason() == FormatException::$messages['STYLESHEET_ID']
            || $fe->getReason() == FormatException::$messages['LANGUAGE_ID']) {

        header('Location: http://z0r.de/?id=114');
        exit;
    }
    die($smarty->fetch('exception.tpl'));
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>