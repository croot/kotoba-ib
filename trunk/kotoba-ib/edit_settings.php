<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Edit user settings script.
 *
 * Parameters:
 * keyword_load - keyword for load settings.
 * keyword_save - keyword for save settings.
 * threads_per_page - used in case of save settings. Count of threads per page
 *                    in board view (see config.php).
 * posts_per_thread - used in case of save settings. Count of posts per thread
 *                    in board view (see config.php).
 * lines_per_post - used in case of save settings. Count of lines per post
 *                  in board view (see config.php).
 * stylesheet_id - used in case of save settings. Stylesheet id.
 * language_id - used in case of save settings. Language id.
 * goto - used in case of save settings. Redirection after posting.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH
                . "/locale/{$_SESSION['language']}/messages.php";
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

    $stylesheets = stylesheets_get_all();
    $languages = languages_get_all();

    // Load or save settings.
    if (isset($_REQUEST['keyword_load'])) {

        // Reload session.
        $kotoba_session_start_time = $_SESSION['kotoba_session_start_time'];
        session_destroy();
        kotoba_session_start();
        $_SESSION['kotoba_session_start_time'] = $kotoba_session_start_time;
        if (Config::LANGUAGE != $_SESSION['language']) {
            require Config::ABS_PATH
                    . "/locale/{$_SESSION['language']}/messages.php";
        }

        // Check keyword and load user settings.
        if ( ($_ = users_check_keyword($_REQUEST['keyword_load'])) === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $keyword_hash = md5($_);
        if (load_user_settings($keyword_hash) === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }

        // Redirection.
        header('Location: ' . Config::DIR_PATH . "/edit_settings.php");

        // Cleanup.
        DataExchange::releaseResources();

        exit(0);
    } elseif (isset($_REQUEST['keyword_save'])) {

        // Reload session.
        $kotoba_session_start_time = $_SESSION['kotoba_session_start_time'];
        session_destroy();
        kotoba_session_start();
        $_SESSION['kotoba_session_start_time'] = $kotoba_session_start_time;
        if (Config::LANGUAGE != $_SESSION['language']) {
            require Config::ABS_PATH
                    . "/locale/{$_SESSION['language']}/messages.php";
        }

        // Check keyword.
        if ( ($_ = users_check_keyword($_REQUEST['keyword_save'])) === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $keyword_hash = md5($_);

        // Check threads per page.
        $_ = users_check_threads_per_page($_REQUEST['threads_per_page']);
        if ($_ === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $threads_per_page = $_;

        // Check posts per thread.
        $_ = users_check_posts_per_thread($_REQUEST['posts_per_thread']);
        if ($_ === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $posts_per_thread = $_;

        // Check lines per post.
        $_ = users_check_lines_per_post($_REQUEST['lines_per_post']);
        if ($_ === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $lines_per_post = $_;

        // Check stylesheet.
        $stylesheet_id = stylesheets_check_id($_REQUEST['stylesheet_id']);
        $found = FALSE;
        foreach ($stylesheets as $stylesheet) {
            if ($stylesheet_id == $stylesheet['id']) {
                $found = TRUE;
                break;
            }
        }
        if (!$found) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty,
                               new StylesheetNotExistsError($stylesheet_id));
            exit(1);
        }

        // Check language.
        $language_id = languages_check_id($_REQUEST['language_id']);
        $found = FALSE;
        foreach ($languages as $language) {
            if ($language_id == $language['id']) {
                $found = TRUE;
                break;
            }
        }
        if (!$found) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty,
                               new LanguageNotExistsError($language_id));
            exit(1);
        }

        // Check goto.
        $goto = users_check_goto($_REQUEST['goto']);
        if ($goto === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, new UserGotoError());
            exit(1);
        }

        users_edit_by_keyword($keyword_hash,
                              $posts_per_thread,
                              $threads_per_page,
                              $lines_per_post,
                              $language_id,
                              $stylesheet_id,
                              null,
                              $goto);

        if (load_user_settings($keyword_hash) === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }

        // Redirection.
        header('Location: ' . Config::DIR_PATH . "/edit_settings.php");

        // Cleanup.
        DataExchange::releaseResources();

        exit(0);
    }

    $categories = categories_get_all();
    $boards = boards_get_visible($_SESSION['user']);
    make_category_boards_tree($categories, $boards);

    // Pass all threads hidden by specific user.
    $htfilter = function ($hidden_thread, $user) {
        if ($hidden_thread['user'] == $user) {
            return true;
        }
        return false;
    };
    $hidden_threads = hidden_threads_get_filtred_by_boards($boards, $htfilter,
                                                           $_SESSION['user']);

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
    $posts = posts_get_visible_filtred_by_threads($threads, $_SESSION['user'],
                                                  $pfilter);
    foreach ($favorites as &$f) {
        $f['unread'] = 0;
        foreach ($posts as $post) {
            if ($f['thread']['id'] == $post['thread']['id']
                    && $post['number'] > $f['last_readed']) {

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
    $cookie_params = session_get_cookie_params();
    $exp_h = floor((time() - $_SESSION['kotoba_session_start_time']) / 3600);
    $exp_m = floor((time() - $_SESSION['kotoba_session_start_time']) / 60) - $exp_h * 60;
    $smarty->assign(
        'sess',
        array('exp_h' => $exp_h,
              'exp_m' => $exp_m,
              'id' => session_id(),
              'name' => session_name(),
              'lifet_h' => Config::SESSION_LIFETIME / 3600,
              'lifet_m' => 0)
    );
    $smarty->display('edit_settings.tpl');

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch (KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
