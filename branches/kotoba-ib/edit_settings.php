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
/*
 * Загрузка настроек.
 */
if(isset($_POST['keyword_load']))
{
    if(($keyword_code = check_format('keyword', $_POST['keyword_load'])) == false)
	{
        mysqli_close($link);
        kotoba_error(Errmsgs::$messages['KEYWORD'], $smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
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
        kotoba_error(sprintf(Errmsgs::$messages['THREADSPERPAGE'], Config::MIN_THREADSPERPAGE, Config::MAX_THREADSPERPAGE), $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(($posts_per_thread = check_format('posts_per_thread', $_POST['posts_per_thread'])) == false)
        kotoba_error(sprintf(Errmsgs::$messages['POSTSPERTHREAD'], Config::MIN_POSTSPERTHREAD, Config::MAX_POSTSPERTHREAD), $smarty, basename(__FILE__) . ' ' . __LINE__);

    if(($lines_per_post = check_format('lines_per_post', $_POST['lines_per_post'])) == false)
        kotoba_error(sprintf(Errmsgs::$messages['LINESPERPOST'], Config::MIN_LINESPERPOST, Config::MAX_LINESPERPOST), $smarty, basename(__FILE__) . ' ' . __LINE__);

	if(($new_stylesheet = check_format('stylesheet', $_POST['stylesheet'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['STYLESHEET'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$stylesheets = db_stylesheets_get($link, $smarty);
    if(count($stylesheets) <= 0)
	{
		mysqli_close($link);
        kotoba_error(Errmsgs::$messages['STYLESHEETS_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$found = false;
	$stylesheet_names = array();
	foreach($stylesheets as $stylesheet)
	{
		array_push($stylesheet_names, $stylesheet['name']);
		if($stylesheet['name'] == $new_stylesheet)
			$found = true;
	}
    if(! $found)
	{
        mysqli_close($link);
        kotoba_error(Errmsgs::$messages['STYLESHEET'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if(($new_language = check_format('language', $_POST['language'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['LANGUAGE'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$languages = db_languages_get($link, $smarty);
    if(count($languages) <= 0)
	{
		mysqli_close($link);
        kotoba_error(Errmsgs::$messages['LANGUAGES_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$found = false;
	$language_names = array();
	foreach($languages as $language)
	{
		array_push($language_names, $language['name']);
		if($language['name'] == $new_language)
			$found = true;
	}
    if(! $found)
	{
        mysqli_close($link);
        kotoba_error(Errmsgs::$messages['LANGUAGE'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}

    $keyword_hash = md5($keyword_code);

    if(db_save_user_settings($keyword_hash, $threads_per_page, $posts_per_thread, $lines_per_post, $new_stylesheet, $new_language, (!isset($_SESSION['rempass']) || $_SESSION['rempass'] == null ? '': $_SESSION['rempass']), $link, $smarty) == false)
        kotoba_error(Errmsgs::$messages['SAVE_USER_SETTINGS'], $smarty, basename(__FILE__) . ' ' . __LINE__);

    load_user_settings($keyword_hash, $link, $smarty); // Потому что нужно получить id пользователя.
}

/*
 * Если не происходит загрузка или сохранения настроек, то нужно получить
 * данные о языках и таблицах стилей.
 */
if(!isset($stylesheets))
{
	$stylesheets = db_stylesheets_get($link, $smarty);
    if(count($stylesheets) <= 0)
	{
		mysqli_close($link);
        kotoba_error(Errmsgs::$messages['STYLESHEETS_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$stylesheet_names = array();
	foreach($stylesheets as $stylesheet)
		array_push($stylesheet_names, $stylesheet['name']);
}

if(!isset($languages))
{
	$languages = db_languages_get($link, $smarty);
    if(count($languages) <= 0)
	{
		mysqli_close($link);
        kotoba_error(Errmsgs::$messages['LANGUAGES_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	$language_names = array();
	foreach($languages as $language)
		array_push($language_names, $language['name']);
}
mysqli_close($link);
if($smarty->language != $_SESSION['language'])
	$smarty = new SmartyKotobaSetup($_SESSION['language']); // Язык изменился после изменения настроек.
$smarty->assign('threads_per_page', $_SESSION['threads_per_page']);
$smarty->assign('posts_per_thread', $_SESSION['posts_per_thread']);
$smarty->assign('lines_per_post', $_SESSION['lines_per_post']);
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('stylesheet', $_SESSION['stylesheet']);
$smarty->assign('languages', $language_names);
$smarty->assign('stylesheets', $stylesheet_names);
$smarty->display('edit_settings.tpl');
?>
