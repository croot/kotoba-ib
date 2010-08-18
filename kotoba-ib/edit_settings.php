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

/**
 * Скрипт редактирования настроек пользователя.
 * @package userscripts
 */

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'],
            $_SESSION['stylesheet']);

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

    $stylesheets = stylesheets_get_all();
    $languages = languages_get_all();

    if(isset($_POST['keyword_load'])) {
        session_destroy();
        kotoba_session_start();
        $keyword_hash = md5(users_check_keyword($_POST['keyword_load']));
        load_user_settings($keyword_hash);
    } elseif(isset($_POST['keyword_save'])) {
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
        users_edit_by_keyword($keyword_hash, $threads_per_page,
                $posts_per_thread, $lines_per_post, $stylesheet_id,
                $language_id, null, $goto);
        load_user_settings($keyword_hash); // Потому что нужно получить id пользователя.
    }

    DataExchange::releaseResources();

    if($smarty->language != $_SESSION['language']
            || $smarty->stylesheet != $_SESSION['stylesheet']) {
        // Язык и\или стиль оформления изменился после изменения настроек.
        $smarty = new SmartyKotobaSetup($_SESSION['language'],
                $_SESSION['stylesheet']);
    }
    $smarty->assign('threads_per_page', $_SESSION['threads_per_page']);
    $smarty->assign('posts_per_thread', $_SESSION['posts_per_thread']);
    $smarty->assign('lines_per_post', $_SESSION['lines_per_post']);
    $smarty->assign('language', $_SESSION['language']);
    $smarty->assign('languages', $languages);
    $smarty->assign('stylesheets', $stylesheets);
    $smarty->assign('goto', $_SESSION['goto']);
    $smarty->display('edit_settings.tpl');
} catch(FormatException $fe) {
    $smarty->assign('msg', $fe->__toString());
    DataExchange::releaseResources();
    if($fe->getReason() == FormatException::$messages['STYLESHEET_ID']
            || $fe->getReason() == FormatException::$messages['LANGUAGE_ID']) {
        header('Location: http://z0r.de/?id=114');
        exit;
    }
    die($smarty->fetch('error.tpl'));
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>