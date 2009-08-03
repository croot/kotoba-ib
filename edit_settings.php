<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

require_once 'config.php';
require_once 'common.php';
require_once 'exception_processing.php';

if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/stat/edit_settings.stat', 'a')) == false)
        kotoba_error("Ошибка. Не удалось открыть или создать файл статистики");

kotoba_setup();

if(!session_start())
    exit;

login();

require_once 'database_connect.php';
require_once 'database_common.php';
require_once 'events.php';

$link = dbconnect();

if(isset($_POST['keyword_load']))
{
    if(($keyword_code = check_format('keyword', $_POST['keyword_load'])) == false)
    {
        kotoba_stat(ERR_KEYWORD, $stat_file);
        kotoba_error(ERR_KEYWORD);
    }

    load_user_settings(md5($keyword_code));
}
elseif (isset($_POST['keyword_save']))
{
    if(($keyword_code = check_format('keyword', $_POST['keyword_save'])) == false)
    {
        kotoba_stat(ERR_KEYWORD, $stat_file);
        kotoba_error(ERR_KEYWORD);
    }

    if(($threads_per_page = check_format('threads_per_page', $_POST['threads_per_page'])) == false)
    {
        kotoba_stat(ERR_THREADSPERPAGE, $stat_file);
        kotoba_error(ERR_THREADSPERPAGE);
    }

    if(($lines_per_post = check_format('lines_per_post', $_POST['lines_per_post'])) == false)
    {
        kotoba_stat(ERR_LINESPERPOST, $stat_file);
        kotoba_error(ERR_LINESPERPOST);
    }

    if(($posts_per_thread = check_format('posts_per_thread', $_POST['posts_per_thread'])) == false)
    {
        kotoba_stat(ERR_POSTSPERTHREAD, $stat_file);
        kotoba_error(ERR_POSTSPERTHREAD);
    }

    $link = dbconnect();

    if(($stylesheets = db_get_stylesheets($link)) == null)
    {
        kotoba_stat(ERR_STYLESHEETS_NOT_EXIST, $stat_file);
        kotoba_error(ERR_STYLESHEETS_NOT_EXIST);
    }

    if(in_array(($stylesheet = check_format('stylesheet', $_POST['stylesheet'])), $stylesheets, true) != true)
    {
        kotoba_stat(ERR_STYLESHEET, $stat_file);
        kotoba_error(ERR_STYLESHEET);
    }

    if(($languages = db_get_languages($link)) == null)
    {
        kotoba_stat(ERR_LANGUAGES_NOT_EXIST, $stat_file);
        kotoba_error(ERR_LANGUAGES_NOT_EXIST);
    }

    if(in_array(($language = check_format('language', $_POST['language'])), $languages, true) != true)
    {
        kotoba_stat(ERR_LANGUAGE, $stat_file);
        kotoba_error(ERR_LANGUAGE);
    }

    $keyword_hash = md5($keyword_code);

    if(db_save_user_settings($link, $keyword_hash, $threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet, $language) == false)
    {
        kotoba_stat(ERR_SAVEUSERSETTINGS, $stat_file);
        kotoba_error(ERR_SAVEUSERSETTINGS);
    }

    load_user_settings($keyword_hash);
}

if(!isset($stylesheets) && (($stylesheets = db_get_stylesheets($link)) == null))
{
    kotoba_stat(ERR_STYLESHEETS_NOT_EXIST, $stat_file);
    kotoba_error(ERR_STYLESHEETS_NOT_EXIST);
}

if(!isset($languages) && (($languages = db_get_languages($link)) == null))
{
    kotoba_stat(ERR_LANGUAGES_NOT_EXIST, $stat_file);
    kotoba_error(ERR_LANGUAGES_NOT_EXIST);
}

mysqli_close($link);
$smarty = new SmartyKotobaSetup();
$smarty->assign('threads_per_page', $_SESSION['threads_per_page']);
$smarty->assign('posts_per_thread', $_SESSION['posts_per_thread']);
$smarty->assign('lines_per_post', $_SESSION['lines_per_post']);
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('stylesheet', $_SESSION['stylesheet']);
$smarty->assign('languages', $languages);
$smarty->assign('stylesheets', $stylesheets);

$smarty->display('edit_settings.tpl');
?>
<?php
/*
 * Загружает настройки пользователя с ключевым словом $keyword_hash.
 */
function load_user_settings($keyword_hash)
{
    global $link, $stat_file;
    $user_settings = db_get_user_settings($link, $keyword_hash);

    if($user_settings != null)
    {
        $_SESSION['user'] = $user_settings['id'];
        $_SESSION['groups'] = $user_settings['groups'];
        $_SESSION['threads_per_page'] = $user_settings['threads_per_page'];
        $_SESSION['posts_per_thread'] = $user_settings['posts_per_thread'];
        $_SESSION['lines_per_post'] = $user_settings['lines_per_post'];
        $_SESSION['stylesheet'] = $user_settings['stylesheet'];
        $_SESSION['language'] = $user_settings['language'];
    }
    else
    {
        kotoba_stat(ERR_USERNOTEXIST, $stat_file);
        kotoba_error(ERR_USERNOTEXIST);
    }
}
?>