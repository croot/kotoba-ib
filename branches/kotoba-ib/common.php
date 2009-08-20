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

/**********
 * Разное *
 **********/

/*
 * Проверяет корректность значения $value
 * в зависимости от типа $type.
 *
 * Например: значение типа "board" должно быть
 * строкой длины от 1 до 16 байт включительно,
 * которая состоит из символов латинских букв в верхнем
 * и нижем регистре, цифр, знача подчеркивания и тире.
 *
 */
function check_format($type, $value)
{
    switch($type)
    {
        case 'stylesheet':
        case 'language':
            return RawUrlEncode($value);

        case 'keyword':
			$length = strlen($value);

			if($length <= 32 && $length >= 16)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 32 || (strpos($value, '%') !== false) || $length < 16)
					return false;
			}
			else
				return false;

			return $value;

        case 'posts_per_thread':
        case 'lines_per_post':
		case 'threads_per_page':
			$length = strlen($value);

			if($length <= 2 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 2 || (ctype_digit($value) === false) || $length < 1)
					return false;
			}
			else
				return false;

			return $value;

        case 'board':
			$length = strlen($value);

			if($length <= 16 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 16 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;

			return $value;

        case 'thread':
        case 'post':
            $length = strlen($value);

			if($length <= 9 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 9 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;

            return $value;

        case 'page':
            $length = strlen($value);

			if($length <= 2 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 2 || (ctype_digit($value) == false) || $length < 1)
					return false;
			}
			else
				return false;

            return $value;

        case 'pass':
            $length = strlen($value);

			if($length <= 12 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 12 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;

            return $value;

        default:
            return false;
    }
}

/***********************
 * Начальная настройка *
 ***********************/

/*
 * en: smarty setup
 * ru: Настройка Smarty.
 */
require_once(KOTOBA_ABS_PATH . '/smarty/Smarty.class.php');

class SmartyKotobaSetup extends Smarty
{
	function SmartyKotobaSetup($language = KOTOBA_LANGUAGE)
	{
		$this->Smarty();

        $this->template_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/templates/$language/";
		$this->compile_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/templates_c/$language/";
		$this->config_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/config/$language/";
		$this->cache_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/cache/$language/";
        $this->caching = 0;

		$this->assign('KOTOBA_DIR_PATH', KOTOBA_DIR_PATH);
        $this->assign('stylesheet', KOTOBA_STYLESHEET);
    }
}

/*
 * Обёртка для session_start(). Устанавливает время жизни сессии и куки, начинает сессию.
 */
function kotoba_session_start()
{
    ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH  . '/sessions/');
	ini_set('session.gc_maxlifetime', KOTOBA_SESSION_LIFETIME);
	ini_set('session.cookie_lifetime', KOTOBA_SESSION_LIFETIME);

    return session_start();
}

/*
 * Настройка кодировки для mbstrings, локали.
 */
function locale_setup()
{
	mb_language('ru');
	mb_internal_encoding("UTF-8");

	if(!setlocale(LC_ALL, 'ru_RU.UTF-8', 'ru', 'rus', 'russian'))
		kotoba_error(ERR_SETLOCALE);
}

/*
 * 
 */
function kotoba_setup(&$link, &$smarty)
{
	if(! kotoba_session_start())
		die(ERR_SESSION_START);

	locale_setup();
	login();
	$link = db_connect();
	$smarty = new SmartyKotobaSetup($_SESSION['language']);

	if(($ban = db_check_banned($link, ip2long($_SERVER['REMOTE_ADDR']))) !== false)
	{
		$smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
		$smarty->assign('reason', $ban['reason']);
		session_destroy();
		die($smarty->fetch('banned.tpl'));
	}
}

/**************************************
 * Обработка ошибок и сбор статистики *
 **************************************/

/*
 * kotoba_error: show error message and exit
 * returns nothing
 * arguments:
 * $msg is custom error message
 *
 * ru: Выводит сообщение об ошибке $error_message и завершает работу скрипта.
 */
function kotoba_error($msg)
{
	$smarty = new SmartyKotobaSetup();

	if(isset($msg) && mb_strlen($msg) > 0)  //error message not empty
		$smarty->assign('msg', $msg);
	else
		$smarty->assign('msg', ERR_UNKNOWN);

	die($smarty->fetch('error.tpl'));
}

/*
 * Выводит сообщение $msg в лог файл $log_file.
 */
function kotoba_log($msg, $log_file) {
    fwrite($log_file, "$msg (" . @date("Y-m-d H:i:s") . ")\n");
}

/*******************************************
 * Регистрация, Авторизация, Идентификация *
 *******************************************/

/*
 * Устанавливает настройки пользователя по умолчанию, если они отсутствуют.
 */
function login() {
    if(!isset($_SESSION['user'])) { // По умолчанию пользователь является Гостем.
        $_SESSION['user'] = KOTOBA_GUEST_ID;
        $_SESSION['groups'] = array('Guests');
        $_SESSION['threads_per_page'] = KOTOBA_THREADS_PER_PAGE;
        $_SESSION['posts_per_thread'] = KOTOBA_POSTS_PER_THREAD;
        $_SESSION['lines_per_post'] = KOTOBA_LINES_PER_POST;
        $_SESSION['stylesheet'] = KOTOBA_STYLESHEET;
        $_SESSION['language'] = KOTOBA_LANGUAGE;
    }
}

/*************************
 * Работа с базой данных *
 *************************/

/*
 * en: Connect to database
 * return database link
 * no arguments
 * ru: Устанавливает соединение с сервером баз данных и возвращает соединение (объект, представляющий соединение с бд).
 */
function db_connect() {
	$link = mysqli_connect(KOTOBA_DB_HOST, KOTOBA_DB_USER, KOTOBA_DB_PASS, KOTOBA_DB_BASENAME);
    if(!$link)
		kotoba_error(mysqli_connect_error());
	// TODO: charset should be configurable
	if(!mysqli_set_charset($link, 'utf8'))
		kotoba_error(mysqli_error($link));
	return $link;
}

/*
 * Проверяет, забанен ли узел с адресом $ip.
 */
function db_check_banned($link, $ip)
{
	//mysqli_query($link, "call sp_ban($ip, $ip, 'Sorc banned', '2009-07-29 16:51:00')");
	//mysqli_query($link, "call sp_ban(" . ip2long('127.0.0.0') . ", " . ip2long('127.255.255.255') . ", 'local ban', '2009-07-07 16:58:00')");
	//echo "\n" . mysqli_error($link);
	//exit;
    if(($result = mysqli_query($link, "call sp_check_ban($ip)")) == false)
        kotoba_error(mysqli_error($link));

    if(($count = mysqli_affected_rows($link)) > 0)
    {
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        cleanup_link($link);
        return array('range_beg' => $row['range_beg'],'range_end' => $row['range_end'],'untill' => $row['untill'],'reason' => $row['reason']);
    }
    else
    {
        mysqli_free_result($result);
        cleanup_link($link);
        return false;
    }
}

/*
 * cleanup all results on link. useful when stored procedure used.
 * no returns
 * argumnets:
 * $link - database link
 */
function cleanup_link($link)
{
	do
    {
		if(($result = mysqli_use_result($link)) != false)
			mysqli_free_result($result);
	}
    while(mysqli_next_result($link));
}

/*
 *
 */
function cleanup_link_store($link)
{
    do
    {
        if(($result = mysqli_store_result($link)) != false)
            mysqli_free_result($result);
	}
    while(mysqli_next_result($link));
}

/*
 * Возвращает доски, видимые пользователю $user.
 */
function db_get_boards_list($link, $user)
{
	if(($result = mysqli_query($link, "call sp_get_boards_list($user)")) == false)
        kotoba_error(mysqli_error($link));

    $boards = array();

    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($boards, $row);

    mysqli_free_result($result);
	return $boards;
}

/*
 * Возвращает настройки пользователя с кулючевым словом $keyword
 * или null.
 */
function db_get_user_settings($link, $keyword)
{
    if(mysqli_multi_query($link, "call sp_get_user_settings('$keyword')") == false)
        kotoba_error(mysqli_error($link));

    if(($result = mysqli_store_result($link)) == false)
        kotoba_error(mysqli_error($link));

    if(($row = mysqli_fetch_assoc($result)) != null)
        $user_settings = $row;
    else    // Пользователь с ключевым словом $keyword не найден.
    {
        mysqli_free_result($result);
        cleanup_link_store($link);
        return null;
    }

    @mysql_free_result($result);

    if(!mysqli_next_result($link))
        kotoba_error(mysqli_error($link));

    if(($result = mysqli_store_result($link)) == false)
        kotoba_error(mysqli_error($link));

    $user_settings['groups'] = array();

    while(($row = mysqli_fetch_assoc($result)) != null)
        array_push($user_settings['groups'], $row);

    if(count($user_settings['groups']) <= 0)    // Пользователь не закреплен ни за одной группой.
    {
        mysqli_free_result($result);
        cleanup_link_store($link);
        return null;
    }

    @mysql_free_result($result);
    cleanup_link_store($link);
    return $user_settings;
}

/*
 *
 */
function db_get_stylesheets($link)
{
    if(($result = mysqli_query($link, 'call sp_get_stylesheets()')) == false)
        kotoba_error(mysqli_error($link));

    $stylesheets = array();

    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($stylesheets, $row['name']);
    else
    {
        mysqli_free_result($result);
        cleanup_link_store($link);
        return null;
    }

    mysqli_free_result($result);
    cleanup_link_store($link);
    return $stylesheets;
}

/*
 *
 */
function db_get_languages($link)
{
    if(($result = mysqli_query($link, 'call sp_get_languages()')) == false)
        kotoba_error(mysqli_error($link));

    $stylesheets = array();

    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($stylesheets, $row['name']);
    else
    {
        mysqli_free_result($result);
        cleanup_link_store($link);
        return null;
    }

    mysqli_free_result($result);
    cleanup_link_store($link);
    return $stylesheets;
}

/*
 *
 */
function db_save_user_settings($link, $keyword, $threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet, $language)
{
    if(($result = mysqli_query($link, "call sp_save_user_settings('$keyword', $threads_per_page, $posts_per_thread, $lines_per_post, '$stylesheet', '$language')")) == false)
        kotoba_error(mysqli_error($link));

    if(mysqli_affected_rows($link) < 0)
    {
        cleanup_link_store($link);
        return false;
    }

    return true;
}

/* db_get_board_id: get board id by name
 * returns board id (positive) on success, otherwise return -1
 * arguments:
 * $link - database link
 * $board_name - board name
 */

function db_get_board_id($link, $board_name) {
	$st = mysqli_prepare($link, "call sp_get_board_id(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "s", $board_name)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $id;
}

/* db_get_pages: get pages quantity
 * remark: on board preview threads splitted on pages
 * return number of pages (0 or more), -1 on error
 * arguments:
 * $link - database link
 * $boardid - board id
 * $threads - number of threads on page
 */

function db_get_pages($link, $userid, $boardid, $threads) {
	$st = mysqli_prepare($link, "select get_pages(?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "iii", $userid, $boardid, $threads)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $pages);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $pages;
}

/* db_get_board: get all board settings
 * return array with fields:
 * id, board_description, board_title, bump_limit, rubber_board, visible_threads, same_upload
 * arguments:
 * $link - database link
 * $board_name - board name
 */

function db_get_board($link, $board_name) {
	$st = mysqli_prepare($link, "call sp_get_board(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "s", $board_name)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $id, $board_description, $board_title,
		$bump_limit, $rubber_board, $visible_threads, $same_upload);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return array();
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return array('id' => $id, 'board_description' => $board_description,
		'board_title' => $board_title, 'bump_limit' => $bump_limit,
		'rubber_board' => $rubber_board, 'visible_threads' => $visible_threads,
		'same_upload' => $same_upload);
}

/* db_get_post_count: get post count on board
 * return 0 or more on success, -1 on error
 * arguments:
 * $link - database link
 * $board_id - board id
 */

function db_get_post_count($link, $board_id) {
	$boards = array();
	$st = mysqli_prepare($link, "call sp_get_board_post_count(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "i", $board_id)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $count);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $count;
}

/* db_board_bumplimit: get board bumplimit
 * returns 0 or more if success, otherwise return -1
 * arguments:
 * $link - database link
 * $board_id - board id
 */

function db_board_bumplimit($link, $board_id) {
	$st = mysqli_prepare($link, "call sp_get_board_bumplimit(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "i", $board_id)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $bumplimit);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $bumplimit;
}

/* db_get_post: get post
 * return array of arrays:
 * [0] is post itself, array fields:
 * post_number, name, email, subject, text, date_time, sage
 * XXX: date_time formatted internally by this function
 * [1] and more is uploads linked with this post. fileds are:
 * is_image, file, size, file_name, file_w, file_h, thumbnail, thumbnail_w, thumbnail_h
 * arguments:
 * $link - database link
 * $board_id - board id
 * $post_number - post number
 */

function db_get_post($link, $board_id, $post_number) {
	$post = array();
	$uploads = array();
	$query = sprintf("call sp_get_post(%d, %d)", $board_id, $post_number);

	if(mysqli_multi_query($link, $query)) {
		$count = 0;
		do {
			if($result = mysqli_store_result($link)) {
				if($count == 0) {
					$row = mysqli_fetch_assoc($result);
					$row['date_time'] = strftime(KOTOBA_DATETIME_FORMAT,
						$row['date_time']);
					$post = $row;
				}
				else {
					while($row = mysqli_fetch_assoc($result)) {
						array_push($uploads, $row);
					}
				}
				// sometimes it throw warning
				@mysql_free_result($result);
			}
			$count ++;
		} while(mysqli_next_result($link));
	}
	else {
		kotoba_error(mysqli_error($link));
	}
	cleanup_link($link);
	return array($post, $uploads);
}

/* db_get_thread: get thread information
 * return array with fileds:
 * original_post_num, messages, bump_limit, sage
 * arguments:
 * $link - database link
 * $board_id - board id
 * $thread_num - thread number (number of open post)
 */
function db_get_thread($link, $board_id, $thread_num) {
	$st = mysqli_prepare($link, "call sp_get_thread_info(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "ii", $board_id, $thread_num)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $original_post_num, $messages, $bump_limit, $sage);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return array();
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return array('original_post_num' => $original_post_num, 'messages' => $messages,
		'bump_limit' => $bump_limit, 'sage' => $sage);
}

/* db_get_board_types: get board types (filetypes which allowed to upload on this board)
 * return array where key is name of supported extension
 * arguments:
 * $link - database link
 * $boardid - board id
 */

function db_get_board_types($link, $boardid) {
	$types = array();
	$st = mysqli_prepare($link, "call sp_get_board_filetypes(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "i", $boardid)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $extension);
	while(mysqli_stmt_fetch($st)) {
		$types[$extension] = 1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $types;
}
?>