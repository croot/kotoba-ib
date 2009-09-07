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

if(!class_exists('Config'))
	exit;

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
		case 'id':
			$length = strlen($value);
			$max_int_length = strlen('' . PHP_INT_MAX);
			if($length <= $max_int_length && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);
				if($length > $max_int_length || (ctype_digit($value) === false) || $length < 1)
					return false;
			}
			else
				return false;
			return $value;

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

		case 'popdown_handler':
		case 'upload_handler':
		case 'category':
		case 'stylesheet':
		case 'language':
		case 'group':
			$length = strlen($value);
			if($length <= 50 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);
				if($length > 50 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;
			return $value;

		case 'extension':
		case 'store_extension':
			$length = strlen($value);
			if($length <= 10 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);
				if($length > 10 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;
			return $value;

		case 'thumbnail_image':
			$length = strlen($value);
			if($length <= 256 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);
				if($length > 256 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;
			return $value;

        default:
            return false;
    }
}

/*
 * Загружает настройки пользователя с ключевым словом $keyword_hash.
 */
function load_user_settings($keyword_hash, $link, $smarty)
{
    if(($user_settings = db_get_user_settings($keyword_hash, $link, $smarty)) != null)
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
        kotoba_error(Errmsgs::$messages['USER_NOT_EXIST'], $smarty, basename(__FILE__) . ' ' . __LINE__);
	require_once "lang/$_SESSION[language]/errors.php";
}

/***********************
 * Начальная настройка *
 ***********************/

/*
 * en: smarty setup
 * ru: Настройка Smarty.
 */
require_once(Config::ABS_PATH . '/smarty/Smarty.class.php');

class SmartyKotobaSetup extends Smarty
{
	var $language;

	function SmartyKotobaSetup($language = Config::LANGUAGE)
	{
		$this->Smarty();
		$this->language = $language;

        $this->template_dir = Config::ABS_PATH . "/smarty/kotoba/templates/$language/";
		$this->compile_dir = Config::ABS_PATH . "/smarty/kotoba/templates_c/$language/";
		$this->config_dir = Config::ABS_PATH . "/smarty/kotoba/config/$language/";
		$this->cache_dir = Config::ABS_PATH . "/smarty/kotoba/cache/$language/";
        $this->caching = 0;

		$this->assign('DIR_PATH', Config::DIR_PATH);
        $this->assign('STYLESHEET', Config::STYLESHEET);
    }
}

/*
 * Установка кодировок и локали, установка настроек
 * пользователя по умолчнию, если требуется, проверка бана и снятие истекших банов.
 * В качестве параметров процедура принимает ссылку на соединение с базой данных $link
 * и ссылку на экземпляр шаблонизатора $smarty, которые будут инициализированы в ходе
 * работы процедуры.
 */
function kotoba_setup(&$link, &$smarty)
{
	ini_set('session.save_path', Config::ABS_PATH . '/sessions/');
	ini_set('session.gc_maxlifetime', Config::SESSION_LIFETIME);
	ini_set('session.cookie_lifetime', Config::SESSION_LIFETIME);
	if(! session_start())
		die(Errmsgs::$messages['SESSION_START']);
	/* По умолчанию пользователь является Гостем. */
    if(!isset($_SESSION['user']))
	{
        $_SESSION['user'] = Config::GUEST_ID;
        $_SESSION['groups'] = array(Config::GST_GROUP_NAME);
        $_SESSION['threads_per_page'] = Config::THREADS_PER_PAGE;
        $_SESSION['posts_per_thread'] = Config::POSTS_PER_THREAD;
        $_SESSION['lines_per_post'] = Config::LINES_PER_POST;
        $_SESSION['stylesheet'] = Config::STYLESHEET;
		$_SESSION['language'] = Config::LANGUAGE;
    }
	require_once "lang/$_SESSION[language]/errors.php";
	mb_language(Config::MB_LANGUAGE);
	mb_internal_encoding(Config::MB_ENCODING);
	if(!setlocale(LC_ALL, Config::$LOCALE_NAMES))
		kotoba_error(Errmsgs::$messages['SETLOCALE']);
	$link = db_connect();
	$smarty = new SmartyKotobaSetup($_SESSION['language']);
	if(($ban = db_check_banned(ip2long($_SERVER['REMOTE_ADDR']), $link, $smarty)) !== false)
	{
		$smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
		$smarty->assign('reason', $ban['reason']);
		session_destroy();
		mysqli_close($link);
		die($smarty->fetch('banned.tpl'));
	}
}

/**************************************
 * Обработка ошибок и сбор статистики *
 **************************************/

/*
 * Выводит сообщение об ошибке $msg и завершает работу скрипта.
 * Аргументы:
 * $msg - сообщение об ошибки
 * $smarty - экземпляр класса шаблонизатора
 * $error_source - имя файла и номер строки, в которой была вызвана эта
 * процедура.
 */
function kotoba_error($msg, $smarty, $error_source = '')
{
	$smarty->assign('msg', (isset($msg) && mb_strlen($msg) > 0 ? $msg : Errmsgs::$messages['UNKNOWN']) . " at $error_source");
	die($smarty->fetch('error.tpl'));
}

/*
 * Выводит сообщение $msg в лог файл $log_file.
 */
function kotoba_log($msg, $log_file) {
    fwrite($log_file, "$msg (" . @date("Y-m-d H:i:s") . ")\n");
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
function db_connect()
{
	if(($link = mysqli_connect(Config::DB_HOST, Config::DB_USER, Config::DB_PASS, Config::DB_BASENAME)) == false)
		kotoba_error(mysqli_connect_error());

	if(!mysqli_set_charset($link, Config::SQL_ENCODING))
		kotoba_error(mysqli_error($link));

	return $link;
}

/*
 * Проверяет, забанен ли узел с адресом $ip. Если нет, то возвращает false,
 * если да, то возвращает массив, содержащий причину, время истечения бана
 * и т.д.
 */
function db_check_banned($ip, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_check_ban($ip)")) == false)
        kotoba_error(mysqli_error($link), $smarty);
	$row = false;
    if(mysqli_affected_rows($link) > 0)
    {
        $row = mysqli_fetch_assoc($result);
        $row = array('range_beg' => $row['range_beg'],
			'range_end' => $row['range_end'],
			'untill' => $row['untill'],
			'reason' => $row['reason']);
    }
	mysqli_free_result($result);
	cleanup_link($link, $smarty);
	return $row;
}

/*
 * cleanup all results on link. useful when stored procedure used.
 * no returns
 * argumnets:
 * $link - database link
 */
function cleanup_link($link, $smarty)
{
	/*
	 * Заметка: если использовать mysqli_use_result вместо store, то
	 * не будет выведена ошибка, если таковая произошла в следующем запросе
	 * в mysqli_multi_query.
	 */
	do
    {
		if(($result = mysqli_store_result($link)) != false)
			mysqli_free_result($result);
	}
    while(mysqli_next_result($link));
	if(mysqli_errno($link))
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
}

/*
 * Возвращает имена досок с именами категорий.
 * Аргументы:
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_board_get($link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_board_get({$_SESSION['user']})")) == false)
        kotoba_error(mysqli_error($link), $smarty);
    $boards = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($boards, array('id' => $row['id'], 'name' => $row['name'], 'category' => $row['category']));
    mysqli_free_result($result);
	cleanup_link($link, $smarty);
	return $boards;
}

/*
 * Возвращает настройки пользователя с ключевым словом $keyword
 * или null.
 * Аргументы:
 * $keyword - хеш ключевого слова
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_get_user_settings($keyword, $link, $smarty)
{
	if(mysqli_multi_query($link, "call sp_get_user_settings('$keyword')") == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	if(($result = mysqli_store_result($link)) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    if(($row = mysqli_fetch_assoc($result)) != null)
        $user_settings = $row;
    else
    {
		/* Пользователь с ключевым словом $keyword не найден. */
        mysqli_free_result($result);
        cleanup_link($link, $smarty);
        return null;
    }
    @mysql_free_result($result);
	/*
	 * Если данные о группе пользователя не были получены,
	 * значит что-то пошло не так.
	 */
    if(! mysqli_next_result($link))
	{
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	}
	if(($result = mysqli_store_result($link)) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    $user_settings['groups'] = array();
    while(($row = mysqli_fetch_assoc($result)) != null)
        array_push($user_settings['groups'], $row['name']);
    if(count($user_settings['groups']) <= 0)
    {
		/* Пользователь не закреплен ни за одной группой. */
        mysqli_free_result($result);
        cleanup_link($link, $smarty);
        return null;
    }
    @mysql_free_result($result);
    cleanup_link($link, $smarty);
    return $user_settings;
}
/*
 * Возвращает список стилей оформления или null, если ни одного стиля не задано.
 * Аргументы:
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_get_stylesheets($link, $smarty)
{
    if(($result = mysqli_query($link, 'call sp_get_stylesheets()')) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    $stylesheets = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($stylesheets, $row['name']);
    else
    {
        mysqli_free_result($result);
        cleanup_link($link, $smarty);
        return null;
    }
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $stylesheets;
}
/*
 * Возвращает список языков или null, если ни одного языка не задано.
 * Аргументы:
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_get_languages($link, $smarty)
{
    if(($result = mysqli_query($link, 'call sp_get_languages()')) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    $stylesheets = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($stylesheets, $row['name']);
    else
    {
        mysqli_free_result($result);
        cleanup_link($link, $smarty);
        return null;
    }
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $stylesheets;
}
/*
 * Сохраняет настройки пользователя в базе данных.
 * Аргументы:
 * $keyword - хеш ключевого слова
 * $threads_per_page - количество нитей на странице предпросмотра доски
 * $posts_per_thread - количество сообщений в предпросмотре треда
 * $lines_per_post - максимальное количество строк в предпросмотре сообщения
 * $stylesheet - стиль оформления
 * $language - язык
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_save_user_settings($keyword, $threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet, $language, $link, $smarty)
{
    if(($result = mysqli_query($link, "call sp_save_user_settings('$keyword', $threads_per_page, $posts_per_thread, $lines_per_post, '$stylesheet', '$language')")) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    if(mysqli_affected_rows($link) < 0)
    {
        cleanup_link($link, $smarty);
        return false;
    }
	cleanup_link($link, $smarty);
    return true;
}
/*
 * Возвращает список групп или пустой массив, если групп нет.
 * Аргументы:
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_group_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_group_get()')) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    $groups = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($groups, array('id' => $row['id'], 'name' => $row['name']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $groups;
}
/*
 * Удаляет группы, имена которых перечислены в массиве $delete_list, а так же
 * всех пользователей, которые входят в эти группы и все права, которые заданы
 * для этих групп.
 * Аргументы:
 * $delete_list - список имён групп
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_group_delete($delete_list, $link, $smarty)
{
	foreach($delete_list as $group_id)
	{
		if(($result = mysqli_query($link, "call sp_group_delete('$group_id')")) == false)
			kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
		cleanup_link($link, $smarty);
	}
	return true;
}
/*
 * Добавляет группу с именем $new_group_name.
 * Аргументы:
 * $new_group_name - список имён групп
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_group_add($new_group_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_group_add('$new_group_name')")) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает массив закреплений пользователей за группами или пустой массив,
 * если закреплений нет.
 * Аргументы:
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_user_groups_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_user_groups_get()')) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    $user_groups = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($user_groups, array('user' => $row['user'], 'group' => $row['group']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $user_groups;
}
/*
 * Добавляет пользователя $new_bind_user в группу $new_bind_group.
 * Аргументы:
 * $new_bind_user - идентификатор пользователя
 * $new_bind_group - идентификатор группы
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_user_groups_add($new_bind_user, $new_bind_group, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_user_groups_add($new_bind_user, $new_bind_group)")) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Переносит пользователя $user_id из группы $old_group_id в группу
 * $new_group_id.
 * Аргументы:
 * $user_id - идентификатор пользователя
 * $old_group_id - идентификатор старой группы
 * $new_group_id - идентификатор новой группы
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_user_groups_edit($user_id, $old_group_id, $new_group_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_user_groups_edit($user_id, $old_group_id, $new_group_id)")) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет пользователя $user_id из группы $group_id.
 * Аргументы:
 * $user_id - идентификатор пользователя
 * $group_id - идентификатор группы
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_user_groups_delete($user_id, $group_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_user_groups_delete($user_id, $group_id)")) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список контроля доступа.
 * Аргументы:
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_acl_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_acl_get()')) == false)
        kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
    $acl = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($acl, array(	'group' => $row['group'],
									'board' => $row['board'],
									'thread' => $row['thread'],
									'post' => $row['post'],
									'view' => $row['view'],
									'change' => $row['change'],
									'moderate' => $row['moderate']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $acl;
}
/*
 * Добавляет новую запись в список контроля доступа.
 * Аргументы:
 * $group_id - идентификатор группы или -1 для всех групп
 * $board_id - идентификатор доски или -1 для всех досок
 * $thread_num - номер нити или -1 для всех нитей
 * $post_num - номер сообщения или -1 для всех сообщений
 * $view - право на чтение 0 или 1
 * $change - право на изменение 0 или 1
 * $moderate - право на модерирование 0 или 1
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_acl_add($group_id, $board_id, $thread_num, $post_num, $view, $change, $moderate, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_acl_add($group_id, $board_id, $thread_num, $post_num, $view, $change, $moderate)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Редактирует запись в списке контроля доступа.
 * Аргументы:
 * $group_id - идентификатор группы или -1 для всех групп
 * $board_id - идентификатор доски или -1 для всех досок
 * $thread_num - номер нити или -1 для всех нитей
 * $post_num - номер сообщения или -1 для всех сообщений
 * $view - право на чтение 0 или 1
 * $change - право на изменение 0 или 1
 * $moderate - право на модерирование 0 или 1
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_acl_edit($group_id, $board_id, $thread_num, $post_num, $view, $change, $moderate, $link, $smarty)
{
	if($group_id == null)
		$group_id = -1;
	if($board_id == null)
		$board_id = -1;
	if($thread_num == null)
		$thread_num = -1;
	if($post_num == null)
		$post_num = -1;
	if(($result = mysqli_query($link, "call sp_acl_edit($group_id, $board_id, $thread_num, $post_num, $view, $change, $moderate)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет запись из списка контроля доступа.
 * Аргументы:
 * $group_id - идентификатор группы или -1 для всех групп
 * $board_id - идентификатор доски или -1 для всех досок
 * $thread_num - номер нити или -1 для всех нитей
 * $post_num - номер сообщения или -1 для всех сообщений
 * $link - связь с базой данных
 * $smarty - экземпляр класса шаблонизатора
 */
function db_acl_delete($group_id, $board_id, $thread_num, $post_num, $link, $smarty)
{
	if($group_id == null)
		$group_id = -1;
	if($board_id == null)
		$board_id = -1;
	if($thread_num == null)
		$thread_num = -1;
	if($post_num == null)
		$post_num = -1;
	if(($result = mysqli_query($link, "call sp_acl_delete($group_id, $board_id, $thread_num, $post_num)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список языков.
 * Аргументы:
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_languages_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_languages_get()')) == false)
        kotoba_error(mysqli_error($link),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
    $languages = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($languages, array('id' => $row['id'],
					'name' => $row['name']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $languages;
}
/*
 * Добавляет новый язык с именем $new_language_name.
 * Аргументы:
 * $new_language_name - имя нового языка.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_languages_add($new_language_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_languages_add('$new_language_name')")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет язык с идентификатором $language_id.
 * Аргументы:
 * $language_id - идентификатор языка для удаления.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_languages_delete($language_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_languages_delete($language_id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список стилей оформления.
 * Аргументы:
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_stylesheets_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_stylesheets_get()')) == false)
        kotoba_error(mysqli_error($link),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
    $stylesheets = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($stylesheets, array('id' => $row['id'],
					'name' => $row['name']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $stylesheets;
}
/*
 * Добавляет новый стиль оформления с именем $new_stylesheet_name.
 * Аргументы:
 * $new_stylesheet_name - имя нового стиля.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_stylesheets_add($new_stylesheet_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_stylesheets_add('$new_stylesheet_name')")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет стиль оформления с идентификатором $stylesheet_id.
 * Аргументы:
 * $stylesheet_id - идентификатор стиля для удаления.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_stylesheets_delete($stylesheet_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_stylesheets_delete($stylesheet_id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список категорий досок.
 * Аргументы:
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_categories_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_categories_get()')) == false)
        kotoba_error(mysqli_error($link),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
    $categories = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($categories, array('id' => $row['id'],
					'name' => $row['name']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $categories;
}
/*
 * Добавляет новую категорию досок с именем $new_category_name.
 * Аргументы:
 * $new_category_name - имя новой категории.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_categories_add($new_category_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_categories_add('$new_category_name')")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет категорию досок с идентификатором $category_id.
 * Аргументы:
 * $category_id - идентификатор категории для удаления.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_categories_delete($category_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_categories_delete($category_id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список обработчиков загружаемых файлов.
 * Аргументы:
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_upload_handlers_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_upload_handlers_get()')) == false)
        kotoba_error(mysqli_error($link),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
    $upload_handlers = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($upload_handlers, array('id' => $row['id'],
					'name' => $row['name']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $upload_handlers;
}
/*
 * Добавляет новый обработчик загружаемых файлов с именем
 * $new_upload_handler_name.
 * Аргументы:
 * $new_upload_handler_name - имя нового обработчика.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_upload_handlers_add($new_upload_handler_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_upload_handlers_add('$new_upload_handler_name')")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет обработчик загружаемых файлов с идентификатором $upload_handler_id.
 * Аргументы:
 * $upload_handler_id - идентификатор обработчика для удаления.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_upload_handlers_delete($upload_handler_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_upload_handlers_delete($upload_handler_id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список обработчиков удаления нитей.
 * Аргументы:
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_popdown_handlers_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_popdown_handlers_get()')) == false)
        kotoba_error(mysqli_error($link),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
    $popdown_handlers = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($popdown_handlers, array('id' => $row['id'],
					'name' => $row['name']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $popdown_handlers;
}
/*
 * Добавляет новый обработчик удаления нитей с именем
 * $new_popdown_handler_name.
 * Аргументы:
 * $new_popdown_handler_name - имя нового обработчика.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_popdown_handlers_add($new_popdown_handler_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_popdown_handlers_add('$new_popdown_handler_name')")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет обработчик удаления нитей с идентификатором $popdown_handler_id.
 * Аргументы:
 * $popdown_handler_id - идентификатор обработчика для удаления.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_popdown_handlers_delete($popdown_handler_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_popdown_handlers_delete($popdown_handler_id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список загружаемых типов файлов.
 * Аргументы:
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_upload_types_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_upload_types_get()')) == false)
        kotoba_error(mysqli_error($link),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
    $upload_types = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($upload_types, array('id' => $row['id'],
					'extension' => $row['extension'],
					'store_extension' => $row['store_extension'],
					'upload_handler' => $row['upload_handler'],
					'thumbnail_image' => $row['thumbnail_image']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $upload_types;
}
/*
 * Добавляет новый тип загружаемых файлов.
 * Аргументы:
 * $extension - новый тип файлов.
 * $store_extension - сохраняемый тип файлов.
 * $upload_handler_id - идентификатор обработчика загружаемых файлов.
 * $thumbnail_image_name - имя уменьшенной копии изображения для нового
 * типа файлов.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_upload_types_add($extension, $store_extension, $upload_handler_id, $thumbnail_image_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_upload_types_add('$extension', '$store_extension', $upload_handler_id, '$thumbnail_image_name')")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Редактирует тип загружаемых файлов.
 * Аргументы:
 * $id - идентификатор типа файлов.
 * $store_extension - сохраняемый тип файлов.
 * $upload_handler_id - идентификатор обработчика загружаемых файлов.
 * $thumbnail_image_name - имя уменьшенной копии изображения.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_upload_types_edit($id, $store_extension, $upload_handler_id, $thumbnail_image_name, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_upload_types_edit($id, '$store_extension', $upload_handler_id, '$thumbnail_image_name')")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет тип загружаемых файлов.
 * Аргументы:
 * $id - идентифаикатор типа загружаемых файлов.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_upload_types_delete($id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_upload_types_delete($id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Возвращает список типов файлов на досках.
 * Аргументы:
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_board_upload_types_get($link, $smarty)
{
	if(($result = mysqli_query($link, 'call sp_board_upload_types_get()')) == false)
        kotoba_error(mysqli_error($link),
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
    $board_upload_types = array();
    if(mysqli_affected_rows($link) > 0)
        while(($row = mysqli_fetch_assoc($result)) != null)
            array_push($board_upload_types, array('board' => $row['board'],
					'upload_type' => $row['upload_type']));
    mysqli_free_result($result);
    cleanup_link($link, $smarty);
    return $board_upload_types;
}
/*
 * Добавляет тип загружаемого файла к доске.
 * Аргументы:
 * $board_id - идентификато доски.
 * $upload_type_id - идтенификатор типа загружаемого файла.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_board_upload_types_add($board_id, $upload_type_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_board_upload_types_add($board_id, $upload_type_id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
	return true;
}
/*
 * Удаляет тип загружаемого файла для доски.
 * Аргументы:
 * $board_id - идентификато доски.
 * $upload_type_id - идтенификатор типа загружаемого файла.
 * $link - связь с базой данных.
 * $smarty - экземпляр класса шаблонизатора.
 */
function db_board_upload_types_delete($board_id, $upload_type_id, $link, $smarty)
{
	if(($result = mysqli_query($link, "call sp_board_upload_types_delete($board_id, $upload_type_id)")) == false)
		kotoba_error(mysqli_error($link), $smarty, basename(__FILE__) . ' ' . __LINE__);
	cleanup_link($link, $smarty);
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
		cleanup_link_use($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link_use($link);
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
		cleanup_link_use($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link_use($link);
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
		cleanup_link_use($link);
		return array();
	}
	mysqli_stmt_close($st);
	cleanup_link_use($link);
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
		cleanup_link_use($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link_use($link);
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
		cleanup_link_use($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link_use($link);
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
	cleanup_link_use($link);
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
		cleanup_link_use($link);
		return array();
	}
	mysqli_stmt_close($st);
	cleanup_link_use($link);
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
	cleanup_link_use($link);
	return $types;
}
?>