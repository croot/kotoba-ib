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

require 'kwrapper.php';

kotoba_setup($link, $smarty);

if(isset($_POST['keyword_load']))	// Загрузка настроек.
{
    if(($keyword_code = check_format('keyword', $_POST['keyword_load'])) == false)
        kotoba_error(Errmsgs::$messages['KEYWORD'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    load_user_settings(md5($keyword_code), $link, $smarty);
}
elseif (isset($_POST['keyword_save']))	// Сохранение настроек.
{
	/*
	 * Сначала проверка всех входных параметров.
	 */
    if(($keyword_code = check_format('keyword', $_POST['keyword_save'])) == false)
        kotoba_error(Errmsgs::$messages['KEYWORD'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(($threads_per_page = check_format('threads_per_page', $_POST['threads_per_page'])) == false)
        kotoba_error(Errmsgs::$messages['THREADSPERPAGE'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(($posts_per_thread = check_format('posts_per_thread', $_POST['posts_per_thread'])) == false)
        kotoba_error(Errmsgs::$messages['POSTSPERTHREAD'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(($lines_per_post = check_format('lines_per_post', $_POST['lines_per_post'])) == false)
        kotoba_error(Errmsgs::$messages['LINESPERPOST'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(($stylesheets = db_get_stylesheets($link, $smarty)) == null)
        kotoba_error(Errmsgs::$messages['STYLESHEETS_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(in_array(($stylesheet = check_format('stylesheet', $_POST['stylesheet'])), $stylesheets, true) != true)
        kotoba_error(Errmsgs::$messages['STYLESHEET'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(($languages = db_get_languages($link, $smarty)) == null)
        kotoba_error(Errmsgs::$messages['LANGUAGES_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(in_array(($language = check_format('language', $_POST['language'])), $languages, true) != true)
        kotoba_error(Errmsgs::$messages['LANGUAGE'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    $keyword_hash = md5($keyword_code);

    if(db_save_user_settings($keyword_hash, $threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet, $language, $link, $smarty) == false)
        kotoba_error(Errmsgs::$messages['SAVE_USER_SETTINGS'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    load_user_settings($keyword_hash, $link, $smarty); // Потому что нужно получить id пользователя.
}

/*
 * Если не происходит загрузка или сохранения настроек, то нужно получить данные о языках и таблицах стилей.
 */
if(!isset($stylesheets) && (($stylesheets = db_get_stylesheets($link, $smarty)) == null))
    kotoba_error(Errmsgs::$messages['STYLESHEETS_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);
if(!isset($languages) && (($languages = db_get_languages($link, $smarty)) == null))
    kotoba_error(Errmsgs::$messages['LANGUAGES_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);
mysqli_close($link);
if($smarty->language != $_SESSION['language'])
	$smarty = new SmartyKotobaSetup($_SESSION['language']); // Язык изменился после изменения настроек.
$smarty->assign('threads_per_page', $_SESSION['threads_per_page']);
$smarty->assign('posts_per_thread', $_SESSION['posts_per_thread']);
$smarty->assign('lines_per_post', $_SESSION['lines_per_post']);
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('stylesheet', $_SESSION['stylesheet']);
$smarty->assign('languages', $languages);
$smarty->assign('stylesheets', $stylesheets);
$smarty->display('edit_settings.tpl');
?>