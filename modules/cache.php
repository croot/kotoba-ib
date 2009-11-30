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
// Скрипт, предоставляющий прослойку из фукнций для фукнций работы с БД.

/**********
 * Разное *
 **********/

/**
 * Просто обёртка во избежание возни с глобальной переменной.
 */
class DataExchange
{
	private static $link = null;

	/**
	 * Возвращает связь с базой данных.
	 */
	static function getDBLink()
	{
		if(self::$link == null)
			self::$link = db_connect();
		return self::$link;
	}
	/**
	 * Освобождает используемые ресурсы.
	 */
	static function releaseResources()
	{
		if(self::$link != null && self::$link instanceof MySQLi)
			mysqli_close(self::$link);
	}
}
/**
 * Создаёт необходимые директории при создании доски.
 *
 * Аргументы:
 * $name - имя новой доски.
 *
 * Возвращает TRUE в случае успешного создания директорий и FALSE в противном
 * случае.
 */
function create_directories($name) {
	$base = Config::ABS_PATH . "/$name";
	if(mkdir ($base)) {
		chmod ($base, 0777);
		foreach(array("arch", "img", "thumb") as $dir) {
			$subdir = "$base/$dir";
			if(@mkdir($subdir))	// Hide warning when directory exists.
				chmod($subdir, 0777);
			else
				return false;
		}
	}
	else
		return false;
	return true;
}

/******************************
 * Блокировки адресов (баны). *
 ******************************/

/**
 * Проверяет, заблокирован ли адрес $ip и если да, то зверашет работу скрипта.
 *
 * Аргументы:
 * $smarty - экземпляр шаблонизатора.
 * $ip - адрес.
 */
function bans_check($smarty, $ip)
{
	if(($ban = db_bans_check(DataExchange::getDBLink(), $ip)) !== false)
	{
		$smarty->assign('ip', long2ip($ip));
		$smarty->assign('reason', $ban['reason']);
		session_destroy();
		mysqli_close(DataExchange::getDBLink());
		die($smarty->fetch('banned.tpl'));
	}
}
/**
 * Проверяет корректность начала диапазона ip адресов.
 *
 * Аргументы:
 * $range_beg - начало диапазона ip адресов.
 *
 * Возвращает безопасное для использования начало диапазона ip адресов.
 */
function bans_check_range_beg($range_beg)
{
	if(($range_beg = ip2long($range_beg)) == false)
		throw new FormatException(FormatException::$messages['BANS_RANGE_BEG']);
	return $range_beg;
}
/**
 * Проверяет корректность конца диапазона ip адресов.
 *
 * Аргументы:
 * $range_end - конец диапазона ip адресов.
 *
 * Возвращает безопасный для использования конец диапазона ip адресов.
 */
function bans_check_range_end($range_end)
{
	if(($range_end = ip2long($range_end)) == false)
		throw new FormatException(FormatException::$messages['BANS_RANGE_END']);
	return $range_end;
}
/**
 * Проверяет корректность причины бана.
 *
 * Аргументы:
 * $reason - причина бана.
 *
 * Возвращает безопасную для использования причину бана.
 */
function bans_check_reason($reason)
{
	$length = strlen($reason);
	if($length <= 10000 && $length >= 1)
	{
		$reason = htmlentities($reason, ENT_QUOTES, Config::MB_ENCODING);
		$length = strlen($reason);
		if($length > 10000 || $length < 1)
			throw new FormatException(FormatException::$messages['BANS_REASON']);
	}
	else
		throw new FormatException(FormatException::$messages['BANS_REASON']);
	return $reason;
}
/**
 * Проверяет корректность времени истечения бана.
 *
 * Аргументы:
 * $untill - время истечения бана.
 *
 * Возвращает безопасное для использования время истечения бана.
 */
function bans_check_untill($untill)
{
	$length = strlen($untill);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$untill = RawUrlEncode($untill);
		$length = strlen($untill);
		if($length > $max_int_length || (ctype_digit($untill) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['BANS_UNTILL']);
	}
	else
		throw new FormatException(FormatException::$messages['BANS_UNTILL']);
	return $untill;
}
/**
 * Удаляет бан с заданным идентификатором.
 *
 * Аргументы:
 * $id - идентификатор бана.
 */
function bans_delete_byid($id)
{
	db_bans_delete_byid(DataExchange::getDBLink(), $id);
}
/**
 * Блокирует диапазон адресов.
 *
 * Аргументы:
 * $range_beg - начало диапазона адресов.
 * $range_end - конец диапазона адресов.
 * $reason - причина.
 * $untill - время истечения бана.
 */
function bans_add($range_beg, $range_end, $reason, $untill)
{
	db_bans_add(DataExchange::getDBLink(), $range_beg, $range_end, $reason,
		$untill);
}
/**
 * Удаляет баны с заданным IP адресом.
 *
 * Аргументы:
 * $ip - ip адрес.
 */
function bans_delete_byip($ip)
{
	db_bans_delete_byip(DataExchange::getDBLink(), $ip);
}
/**
 * Получает все баны.
 * 
 * Возвращает баны:
 * 'id' - идентификатор бана.
 * 'range_beg' - начало диапазона блокированных IP адресов.
 * 'range_end' - конец диапазона блокированных IP адресов.
 * 'reason' - причина бана.
 * 'untill' - время истечения бана.
 */
function bans_get_all()
{
	return db_bans_get_all(DataExchange::getDBLink());
}

/*********************
 * Работа с досками. *
 *********************/

/**
 * Получает доски, доступные для чтения пользователю с идентификатором $user_id.
 *
 * Аргументы:
 * $user_id - идентификатор пользователя.
 *
 * Возвращает пустой массив, если досок доступных для просмотра нет. Если есть,
 * то возвращает массив досок:
 * 'id' - идентификатор доски.
 * 'name' - имя доски.
 * 'title' - заголовок доски.
 * 'bump_limit' - спецефиный для доски бамплимит.
 * 'same_upload' - политика загрузки одинаковых изображений.
 * 'popdown_handler' - обработчик автоматического удаления нитей.
 * 'category' - категория доски.
 */
function boards_get_all_view($user_id)
{
	return db_boards_get_all_view(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает доски, доступные для редактирования пользователю.
 *
 * Аргументы:
 * $user_id - идентификатор пользователя.
 *
 * Возвращает доски:
 * 'id' - идентификатор.
 * 'name' - имя.
 * 'title' - заголовок.
 * 'bump_limit' - спецефиный для доски бамплимит.
 * 'same_upload' - политика загрузки одинаковых изображений.
 * 'popdown_handler' - обработчик автоматического удаления нитей.
 * 'category' - категория.
 */
function boards_get_all_change($user_id)
{
	return db_boards_get_all_change(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает все доски.
 *
 * Возвращает доски:
 * 'id' - идентификатор доски.
 * 'name' - имя доски.
 * 'title' - заголовок доски.
 * 'bump_limit' - спецефиный для доски бамплимит.
 * 'same_upload' - политика загрузки одинаковых файлов.
 * 'popdown_handler' - обработчик автоматического удаления нитей.
 * 'category' - категория доски.
 */
function boards_get_all()
{
	return db_boards_get_all(DataExchange::getDBLink());
}
/**
 * Получает доску по заданному идентификатору.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор доски.<br>
 * 'name' - имя доски.<br>
 * 'title' - заголовок доски.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_specifed($board_id)
{
	return db_boards_get_specifed(DataExchange::getDBLink(), $board_id);
}
/**
 * Проверяет корректность идентификатора $id доски.
 *
 * Аргументы:
 * $id - идентификатор доски.
 *
 * Возвращает безопасный для использования идентификатор доски.
 */
function boards_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['BOARD_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['BOARD_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['BOARD_ID']);
	return $id;
}
/**
 * Проверяет корректность имени доски.
 *
 * Аргументы:
 * $name - имя доски.
 *
 * Возвращает безопасный для использования имя доски.
 */
function boards_check_name($name)
{
	if(!isset($name))
		throw new NodataException(NodataException::$messages['BOARD_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 16 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 16 || (strpos($name, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['BOARD_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['BOARD_NAME']);
	return $name;
}
/**
 * Проверяет корректность заголовка доски.
 *
 * Аргументы:
 * $title - заголовок доски.
 *
 * Возвращает безопасный для использования заголовок доски.
 */
function boards_check_title($title)
{
	if(!isset($title))
		throw new NodataException(NodataException::$messages['BOARD_TITLE_NOT_SPECIFED']);
	$length = strlen($title);
	if($length <= 50 && $length >= 1)
	{
		$title = htmlentities($title, ENT_QUOTES, Config::MB_ENCODING);
		$length = strlen($title);
		if($length > 50 || $length < 1)
			throw new FormatException(FormatException::$messages['BOARD_TITLE']);
	}
	else
		throw new FormatException(FormatException::$messages['BOARD_TITLE']);
	return $title;
}
/**
 * Проверяет корректность бамплимита.
 *
 * Аргументы:
 * $bump_limit - бампилимит.
 *
 * Возвращает безопасный для использования бампилимит.
 */
function boards_check_bump_limit($bump_limit)
{
	if(!isset($bump_limit))
		throw new NodataException(NodataException::$messages['BOARD_BUMP_LIMIT_NOT_SPECIFED']);
	$length = strlen($bump_limit);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$bump_limit = RawUrlEncode($bump_limit);
		$length = strlen($bump_limit);
		if($length > $max_int_length || (ctype_digit($bump_limit) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['BOARD_BUMP_LIMIT']);
	}
	else
		throw new FormatException(FormatException::$messages['BOARD_BUMP_LIMIT']);
	return $bump_limit;
}
/**
 * Проверяет корректность названия политики загрузки одинаковых файлов.
 *
 * Аргументы:
 * $same_upload - название политики загрузки одинаковых файлов.
 *
 * Возвращает безопасный для использования название политики загрузки одинаковых
 * файлов.
 */
function boards_check_same_upload($same_upload)
{
	if(!isset($same_upload))
		throw new NodataException(NodataException::$messages['BOARD_SAME_UPLOAD_NOT_SPECIFED']);
	$length = strlen($same_upload);
	if($length <= 32 && $length >= 1)
	{
		$same_upload = RawUrlEncode($same_upload);
		$length = strlen($same_upload);
		if($length > 32 || (strpos($same_upload, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['BOARD_SAME_UPLOAD']);
	}
	else
		throw new FormatException(FormatException::$messages['BOARD_SAME_UPLOAD']);
	return $same_upload;
}
/**
 * Редактирует параметры доски.
 *
 * Аргументы:
 * $id - идентификатор доски.
 * $title - заголовок доски.
 * $bump_limit - бамплимит.
 * $same_upload - поведение при добавлении одинаковых файлов.
 * $popdown_handler - обработчик удаления нитей.
 * $category - категория доски.
 */
function boards_edit($id, $title, $bump_limit, $same_upload,
	$popdown_handler, $category)
{
	db_boards_edit(DataExchange::getDBLink(), $id, $title, $bump_limit,
		$same_upload, $popdown_handler, $category);
}
/**
 * Добавляет доску.
 *
 * Аргументы:
 * $name - имя доски.
 * $title - заголовок доски.
 * $bump_limit - бамплимит.
 * $same_upload - поведение при добавлении одинаковых файлов.
 * $popdown_handler - обработчик удаления нитей.
 * $category - категория доски.
 */
function boards_add($name, $title, $bump_limit, $same_upload,
	$popdown_handler, $category)
{
	db_boards_add(DataExchange::getDBLink(), $name, $title, $bump_limit,
		$same_upload, $popdown_handler, $category);
}
/**
 * Удаление доски.
 *
 * Аргументы:
 * $id - идентификатор доски.
 */
function boards_delete($id)
{
	db_boards_delete(DataExchange::getDBLink(), $id);
}

/*************************
 * Работа с категориями. *
 *************************/

/**
 * Проверяет корректность имени $name категории.
 *
 * Аргументы:
 * $name - имя категории.
 *
 * Возвращает безопасное для использования имя категории.
 */
function categories_check_name($name)
{
	if(!isset($name))
		throw new NodataException(NodataException::$messages['CATEGORY_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['CATEGORY_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['CATEGORY_NAME']);
	return $name;
}
/**
 * Проверяет корректность идентификатора категории.
 *
 * Аргументы:
 * $id - идентификатор категории.
 *
 * Возвращает безопасный для использования идентификатор категории.
 */
function categories_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['CATEGORY_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['CATEGORY_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['CATEGORY_ID']);
	return $id;
}
/**
 * Получает все категории.
 *
 * Возвращает пустой массив, если нет ни одной категории или массив категорий:
 * 'id' - идентификатор категории.
 * 'name' - имя категории.
 */
function categories_get_all()
{
	return db_categories_get_all(DataExchange::getDBLink());
}
/**
 * Добавляет новую категорию с именем $name.
 *
 * Аргументы:
 * $name - имя новой категории.
 */
function categories_add($name)
{
	db_categories_add(DataExchange::getDBLink(), $name);
}
/**
 * Удаляет категорию с идентификатором $id.
 *
 * Аргументы:
 * $id - идентификатор категории для удаления.
 */
function categories_delete($id)
{
	db_categories_delete(DataExchange::getDBLink(), $id);
}

/****************************
 * Работа с пользователями. *
 ****************************/

/**
 * Проверяет корректность идентификатора $id пользователя.
 *
 * Аргументы:
 * $id - идентификатор пользователя.
 *
 * Возвращает безопасный для использования идентификатор пользователя.
 */
function users_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['USER_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['USER_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['USER_ID']);
	return $id;
}
/**
 * Проверяет корректность ключевого слова $keyword.
 *
 * Аргументы:
 * $keyword - ключевое слово.
 *
 * Возвращает безопасное для использования ключевое слово.
 */
function users_check_keyword($keyword)
{
	if(!isset($keyword))
		throw new NodataException(NodataException::$messages['KEYWORD_NOT_SPECIFED']);
	$length = strlen($keyword);
	if($length <= 32 && $length >= 16)
	{
		$keyword = RawUrlEncode($keyword);
		$length = strlen($keyword);
		if($length > 32 || (strpos($keyword, '%') !== false) || $length < 16)
			throw new FormatException(FormatException::$messages['KEYWORD']);
	}
	else
		throw new FormatException(FormatException::$messages['KEYWORD']);
	return $keyword;
}
/**
 * Получает настройки ползователя с ключевым словом $keyword.
 *
 * Аргументы:
 * $keyword - хеш ключевого слова.
 *
 * Возвращает настройки:
 * 'id' - идентификатор пользователя.
 * 'posts_per_thread' - количество последних сообщений в нити при просмотре доски.
 * 'threads_per_page' - количество нитей на странице при просмотре доски.
 * 'lines_per_post' - количество строк в урезанном сообщении при просмотре доски.
 * 'language' - язык.
 * 'stylesheet' - стиль оформления.
 * 'rempass' - пароль для удаления сообщений.
 * 'groups' - массив групп, в которые входит пользователь.
 */
function users_get_settings($keyword)
{
	return db_users_get_settings(DataExchange::getDBLink(), $keyword);
}
/**
 * Проверяет корректность количества нитей $threads_per_page на странице
 * просмотра доски.
 *
 * Аргументы:
 * $threads_per_page - количество нитей на странице просмотра доски.
 *
 * Возвращает безопасное для использования количество нитей на странице
 * просмотра доски.
 */
function users_check_threads_per_page($threads_per_page)
{
	if(!isset($threads_per_page))
		throw new NodataException(NodataException::$messages['THREADS_PER_PAGE_NOT_SPECIFED']);
	$length = strlen($threads_per_page);
	if($length <= 2 && $length >= 1)
	{
		$threads_per_page = RawUrlEncode($threads_per_page);
		$length = strlen($threads_per_page);
		if($length > 2 || (ctype_digit($threads_per_page) === false)
			|| $length < 1)
			throw new FormatException(sprintf(FormatException::$messages['THREADS_PER_PAGE'],
					Config::MIN_THREADSPERPAGE, Config::MAX_THREADSPERPAGE));
	}
	else
		throw new FormatException(sprintf(FormatException::$messages['THREADS_PER_PAGE'],
				Config::MIN_THREADSPERPAGE, Config::MAX_THREADSPERPAGE));
	return $threads_per_page;
}
/**
 * Проверяет корректность количества сообщений $posts_per_thread в нити на
 * странице просмотра доски.
 *
 * Аргументы:
 * $posts_per_thread - количество сообщений в нити на странице просмотра доски.
 *
 * Возвращает безопасное для использования количество сообщений в нити на
 * странице просмотра доски.
 */
function users_check_posts_per_thread($posts_per_thread)
{
	if(!isset($posts_per_thread))
		throw new NodataException(NodataException::$messages['POSTS_PER_THREAD_NOT_SPECIFED']);
	$length = strlen($posts_per_thread);
	if($length <= 2 && $length >= 1)
	{
		$posts_per_thread = RawUrlEncode($posts_per_thread);
		$length = strlen($posts_per_thread);
		if($length > 2 || (ctype_digit($posts_per_thread) === false)
			|| $length < 1)
			throw new FormatException(sprintf(FormatException::$messages['POSTS_PER_THREAD'],
					Config::MIN_POSTSPERTHREAD, Config::MAX_POSTSPERTHREAD));
	}
	else
		throw new FormatException(sprintf(FormatException::$messages['POSTS_PER_THREAD'],
				Config::MIN_POSTSPERTHREAD, Config::MAX_POSTSPERTHREAD));
	return $posts_per_thread;
}
/**
 * Проверяет корректность количества строк $lines_per_post в сообщении на
 * странице просмотра доски.
 *
 * Аргументы:
 * $lines_per_post - количество строк в сообщении на странице просмотра доски.
 *
 * Возвращает безопасное для использования количество строк в сообщении на
 * странице просмотра доски.
 */
function users_check_lines_per_post($lines_per_post)
{
	if(!isset($lines_per_post))
		throw new NodataException(NodataException::$messages['LINES_PER_POST_NOT_SPECIFED']);
	$length = strlen($lines_per_post);
	if($length <= 2 && $length >= 1)
	{
		$lines_per_post = RawUrlEncode($lines_per_post);
		$length = strlen($lines_per_post);
		if($length > 2 || (ctype_digit($lines_per_post) === false)
			|| $length < 1)
			throw new FormatException(sprintf(FormatException::$messages['LINES_PER_POST'],
					Config::MIN_LINESPERPOST, Config::MAX_LINESPERPOST));
	}
	else
		throw new FormatException(sprintf(FormatException::$messages['LINES_PER_POST'],
				Config::MIN_LINESPERPOST, Config::MAX_LINESPERPOST));
	return $lines_per_post;
}
/**
 * Редактирует настройки пользователя с ключевым словом $keyword или добавляет
 * нового.
 *
 * Аргументы:
 * $keyword - хеш ключевого слова
 * $threads_per_page - количество нитей на странице предпросмотра доски
 * $posts_per_thread - количество сообщений в предпросмотре треда
 * $lines_per_post - максимальное количество строк в предпросмотре сообщения
 * $stylesheet - стиль оформления
 * $language - язык
 * $rempass - пароль для удаления сообщений
 */
function users_edit_bykeyword($keyword, $threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet, $language, $rempass)
{
	db_users_edit_bykeyword(DataExchange::getDBLink(), $keyword, $threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet, $language, $rempass);
}
/**
 * Получает всех пользователей.
 *
 * Возвращает пользователей:
 * 'id' - идентификатор пользователя.
 */
function users_get_all()
{
	return db_users_get_all(DataExchange::getDBLink());
}

/*********************************
 * Работа со стилями оформления. *
 *********************************/

/**
 * Проверяет корректность идентификатора $id стиля оформления.
 *
 * Аргументы:
 * $id - идентификатор стиля оформления.
 *
 * Возвращает безопасный для использования идентификатор стиля оформления.
 */
function stylesheets_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['STYLESHEET_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['STYLESHEET_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['STYLESHEET_ID']);
	return $id;
}
/**
 * Проверяет корректность имени $name стиля оформления.
 *
 * Аргументы:
 * $name - имя стиля оформления.
 *
 * Возвращает безопасное для использования имя стиля оформления.
 */
function stylesheets_check_name($name)
{
	if(!isset($name))
		throw new NodataException(NodataException::$messages['STYLESHEET_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['STYLESHEET_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['STYLESHEET_NAME']);
	return $name;
}
/**
 * Получает все стили оформления.
 *
 * Возвращает стили оформления:
 * 'id' - идентификатор стиля оформления.
 * 'name' - имя стиля оформления.
 */
function stylesheets_get_all()
{
	return db_stylesheets_get_all(DataExchange::getDBLink());
}
/**
 * Добавляет новый стиль оформления с именем $name.
 *
 * Аргументы:
 * $name - имя нового стиля оформления.
 */
function stylesheets_add($name)
{
	db_stylesheets_add(DataExchange::getDBLink(), $name);
}
/**
 * Удаляет стиль оформления с идентификатором $id.
 *
 * Аргументы:
 * $id - идентификатор стиля для удаления.
 */
function stylesheets_delete($id)
{
	db_stylesheets_delete(DataExchange::getDBLink(), $id);
}

/*********************
 * Работа с языками. *
 *********************/

/**
 * Проверяет корректность идентификатора $id языка.
 *
 * Аргументы:
 * $id - идентификатор языка.
 *
 * Возвращает безопасный для использования идентификатор языка.
 */
function languages_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['LANGUAGE_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['LANGUAGE_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['LANGUAGE_ID']);
	return $id;
}
/**
 * Проверяет корректность имени $name языка.
 *
 * Аргументы:
 * $name - имя языка.
 *
 * Возвращает безопасное для использования имя языка.
 */
function languages_check_name($name)
{
	if(!isset($name))
		throw new NodataException(NodataException::$messages['LANGUAGE_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['LANGUAGE_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['LANGUAGE_NAME']);
	return $name;
}
/**
 * Получает все языки.
 *
 * Возвращает языки:
 * 'id' - идентификатор языка.
 * 'name' - имя языка.
 */
function languages_get_all()
{
	return db_languages_get_all(DataExchange::getDBLink());
}
/**
 * Добавляет новый язык с именем $name.
 *
 * Аргументы:
 * $name - имя нового языка.
 */
function languages_add($name)
{
	db_languages_add(DataExchange::getDBLink(), $name);
}
/**
 * Удаляет язык с идентификатором $id.
 *
 * Аргументы:
 * $id - идентификатор языка для удаления.
 */
function languages_delete($id)
{
	db_languages_delete(DataExchange::getDBLink(), $id);
}

/**********************
 * Работа с группами. *
 **********************/

/**
 * Получает все группы.
 *
 * Возвращает группы:
 * 'id' - идентификатор группы.
 * 'name' - имя группы.
 */
function groups_get_all()
{
	return db_groups_get_all(DataExchange::getDBLink());
}
/**
 * Проверяет корректность имени $name группы.
 *
 * Аргументы:
 * $name - имя группы.
 *
 * Возвращает безопасное для использования имя группы.
 */
function groups_check_name($name)
{
	if(!isset($name))
		throw new NodataException(NodataException::$messages['GROUP_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['GROUP_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['GROUP_NAME']);
	return $name;
}
/**
 * Проверяет корректность идентификатора $id группы.
 *
 * Аргументы:
 * $id - идентификатор группы.
 *
 * Возвращает безопасный для использования идентификатор группы.
 */
function groups_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['GROUP_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['GROUP_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['GROUP_ID']);
	return $id;
}
/**
 * Добавляет группу с именем $group_name, а так же стандартные разрешения на
 * чтение.
 *
 * Аргументы:
 * $group_name - имя группы.
 */
function groups_add($group_name)
{
	db_groups_add(DataExchange::getDBLink(), $group_name);
}
/**
 * Удаляет группы, идентификаторы которых перечислены в массиве $group_ids, а
 * так же всех пользователей, которые входят в эти группы и все права, которые
 * заданы для этих групп.
 *
 * Аргументы:
 * $group_ids - массив идентификаторов групп для удаления.
 */
function groups_delete($group_ids)
{
	db_groups_delete(DataExchange::getDBLink(), $group_ids);
}

/*****************************************************
 * Работа с закреплениями пользователей за группами. *
 *****************************************************/

/**
 * Получает закрепления пользователей за группами.
 *
 * Возвращает массив закреплений:
 * 'user' - идентификатор пользователя.
 * 'group' - идентификатор группы.
 */
function user_groups_get_all()
{
	return db_user_groups_get_all(DataExchange::getDBLink());
}
/**
 * Добавляет пользователя с идентификатором $user в группу с идентификатором
 * $group.
 *
 * Аргументы:
 * $user - идентификатор пользователя.
 * $group - идентификатор группы.
 */
function user_groups_add($user, $group)
{
	db_user_groups_add(DataExchange::getDBLink(), $user, $group);
}
/**
 * Переносит пользователя с идентификатором $user_id из группы с идентификатором
 * $old_group_id в группу с идентификатором $new_group_id.
 *
 * Аргументы:
 * $user_id - идентификатор пользователя.
 * $old_group_id - идентификатор старой группы.
 * $new_group_id - идентификатор новой группы.
 */
function user_groups_edit($user_id, $old_group_id, $new_group_id)
{
	db_user_groups_edit(DataExchange::getDBLink(), $user_id, $old_group_id,
		$new_group_id);
}
/**
 * Удаляет пользователя с идентификатором $user_id из группы с идентификатором
 * $group_id.
 *
 * Аргументы:
 * $user_id - идентификатор пользователя.
 * $group_id - идентификатор группы.
 */
function user_groups_delete($user_id, $group_id)
{
	db_user_groups_delete(DataExchange::getDBLink(), $user_id, $group_id);
}

/***************************************
 * Работа со списком контроля доступа. *
 ***************************************/

/**
 * Получает список контроля доступа.
 *
 * Возвращает список контроля доступа:
 * 'group' - идентификатор группы.
 * 'board' - идентификатор доски.
 * 'thread' - идентификатор нити.
 * 'post' - идентификатор сообщения.
 * 'view' - разрешение на просмотр.
 * 'change' - разрешение на редактирование.
 * 'moderate' - разрешение на модерирование.
 */
function acl_get_all()
{
	return db_acl_get_all(DataExchange::getDBLink());
}

/********************
 * Работа с нитями. *
 ********************/

/**
 * Проверяет корректность идентификатора $id нити.
 * @param id string <p>Идентификатор нити.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор нити.
 */
function threads_check_id($id)
{
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false)
			|| $length < 1)
				throw new FormatException(FormatException::$messages['THREAD_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['THREAD_ID']);
	return $id;
}

/*************************
 * Работа с сообщениями. *
 *************************/

/**
 * Проверяет корректность идентификатора $id сообщения.
 *
 * Аргументы:
 * $id - идентификатор сообщения.
 *
 * Возвращает безопасный для использования идентификатор сообщения.
 */
function posts_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['POST_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['POST_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['POST_ID']);
	return $id;
}
/**
 * Проверяет корректность пароля для удаления сообщения.
 * @param password string <p>Пароль.</p>
 * @return string
 * Возвращает безопасный для использования пароль для удаления сообщения.
 */
function posts_check_password($password)
{
	$length = strlen($password);
	if($length <= 12 && $length >= 1)
	{
		$password = RawUrlEncode($password);
		$length = strlen($password);
		if($length > 12 || (strpos($password, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['POST_PASSWORD']);
	}
	else
		throw new FormatException(FormatException::$messages['POST_PASSWORD']);
	return $password;
}
/**
 * Проверяет корректность номера сообщения.
 *
 * Аргументы:
 * $number - номер сообщения.
 *
 * Возвращает безопасный для использования номер сообщения.
 */
function posts_check_number($number)
{
	$length = strlen($number);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$number = RawUrlEncode($number);
		$length = strlen($number);
		if($length > $max_int_length || (ctype_digit($number) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['POST_NUMBER']);
	}
	else
		throw new FormatException(FormatException::$messages['POST_NUMBER']);
	return $number;
}
/**
 * Редактирует запись в списке контроля доступа.
 *
 * Аргументы:
 * $group_id - идентификатор группы или null для всех групп.
 * $board_id - идентификатор доски или null для всех досок.
 * $thread_id - идентификатор нити или null для всех нитей.
 * $post_id - идентификатор сообщения или null для всех сообщений.
 * $view - право на чтение.
 * $change - право на изменение.
 * $moderate - право на модерирование.
 */
function acl_edit($group_id, $board_id, $thread_num, $post_num, $view, $change,
	$moderate)
{
	db_acl_edit(DataExchange::getDBLink(), $group_id, $board_id, $thread_num,
		$post_num, $view, $change, $moderate);
}
/**
 * Добавляет новую запись в список контроля доступа.
 *
 * Аргументы:
 * $group_id - идентификатор группы или null для всех групп.
 * $board_id - идентификатор доски или null для всех досок.
 * $thread_id - идентификатор нити или null для всех нитей.
 * $post_id - идентификатор сообщения или null для всех сообщений.
 * $view - право на чтение. 0 или 1.
 * $change - право на изменение. 0 или 1.
 * $moderate - право на модерирование. 0 или 1.
 */
function acl_add($group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate)
{
	db_acl_add(DataExchange::getDBLink(), $group_id, $board_id, $thread_id,
		$post_id, $view, $change, $moderate);
}
/**
 * Удаляет запись из списка контроля доступа.
 *
 * Аргументы:
 * $group_id - идентификатор группы или null для всех групп.
 * $board_id - идентификатор доски или null для всех досок.
 * $thread_id - идентификатор нити или null для всех нитей.
 * $post_id - идентификатор сообщения или null для всех сообщений.
 */
function acl_delete($group_id, $board_id, $thread_id, $post_id)
{
	db_acl_delete(DataExchange::getDBLink(), $group_id, $board_id, $thread_id,
		$post_id);
}
/**
 * Проверяет, удовлетворяет ли текст сообщения ограничениям по размеру.
 *
 * Аргументы:
 * $text - текст сообщения.
 */
function posts_check_text_size($text)
{
	if(mb_strlen($text) > Config::MAX_MESSAGE_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_TEXT_LENGTH']);
}
/**
 * Проверяет, удовлетворяет ли тема сообщения ограничениям по размеру.
 *
 * Аргументы:
 * $subject - тема сообщения.
 */
function posts_check_subject_size($subject)
{
	// TODO А почему же не mb_?
	if(strlen($subject) > Config::MAX_THEME_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_SUBJECT_LENGTH']);
}
/**
 * Проверяет, удовлетворяет ли имя отправителя ограничениям по размеру.
 *
 * Аргументы:
 * $name - имя отправителя.
 */
function posts_check_name_size($name)
{
	// TODO А почему же не mb_?
	if(strlen($name) > Config::MAX_THEME_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_NAME_LENGTH']);
}

/**********************************************
 * Работа с обработчиками загружаемых файлов. *
 **********************************************/

/**
 * Получает все обработчики загружаемых файлов.
 *
 * Аргументы:
 *
 * Возвращает обработчики загружаемых файлов:
 * 'id' - идентификатор обработчика.
 * 'name' - имя обработчика.
 */
function upload_handlers_get_all()
{
	return db_upload_handlers_get_all(DataExchange::getDBLink());
}
/**
 * Проверяет корректность имени $name обработчика загружаемых файлов.
 *
 * Аргументы:
 * $name - имя обработчика загружаемых файлов.
 *
 * Возвращает безопасное для использования имя обработчика загружаемых файлов.
 */
function upload_handlers_check_name($name)
{
	// TODO имя обрабтчика не может начинаться с цифры т.к. это имя фукнции
	if(!isset($name))
		throw new NodataException(NodataException::$messages['UPLOAD_HANDLER_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_NAME']);
	return $name;
}
/**
 * Проверяет корректность идентификатора обработчика загружаемых файлов.
 *
 * Аргументы:
 * $id - идентификатор обработчика загружаемых файлов.
 *
 * Возвращает безопасный для использования идентификатор обработчика загружаемых
 * файлов.
 */
function upload_handlers_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['UPLOAD_HANDLER_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_ID']);
	return $id;
}
/**
 * Добавляет новый обработчик загружаемых файлов.
 *
 * Аргументы:
 * $name - имя нового обработчика загружаемых файлов.
 */
function upload_handlers_add($name)
{
	db_upload_handlers_add(DataExchange::getDBLink(), $name);
}
/**
 * Удаляет обработчик загружаемых файлов.
 *
 * Аргументы:
 * $id - идентификатор обработчика загружаемых файлов для удаления.
 */
function upload_handlers_delete($id)
{
	db_upload_handlers_delete(DataExchange::getDBLink(), $id);
}

/******************************************
 * Работа с обработчиками удаления нитей. *
 ******************************************/

/**
 * Получает все обработчики удаления нитей.
 *
 * Возвращает обработчики удаления нитей:
 * 'id' - идентификатор обработчика.
 * 'name' - имя обработчика.
 */
function popdown_handlers_get_all()
{
	return db_popdown_handlers_get_all(DataExchange::getDBLink());
}
/**
 * Добавляет новый обработчик удаления нитей.
 *
 * Аргументы:
 * $name - имя нового обработчика удаления нитей.
 */
function popdown_handlers_add($name)
{
	db_popdown_handlers_add(DataExchange::getDBLink(), $name);
}
/**
 * Удаляет обработчик удаления нитей.
 *
 * Аргументы:
 * $id - идентификатор обработчика для удаления.
 */
function popdown_handlers_delete($id)
{
	db_popdown_handlers_delete(DataExchange::getDBLink(), $id);
}
/**
 * Проверяет корректность имени обработчика удаления нитей.
 *
 * Аргументы:
 * $name - имя обработчика удаления нитей.
 *
 * Возвращает безопасное для использования имя обработчика удаления нитей.
 */
function popdown_handlers_check_name($name)
{
	// TODO имя обрабтчика не может начинаться с цифры т.к. это имя фукнции
	if(!isset($name))
		throw new NodataException(NodataException::$messages['POPDOWN_HANDLER_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_NAME']);
	return $name;
}
/**
 * Проверяет корректность идентификатора обработчика удаления нитей.
 *
 * Аргументы:
 * $id - идентификатор обработчика удаления нитей.
 *
 * Возвращает безопасный для использования идентификатор обработчика удаления
 * нитей.
 */
function popdown_handlers_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['POPDOWN_HANDLER_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_ID']);
	return $id;
}

/***************************************
 * Работа с типами загружаемых файлов. *
 ***************************************/

/**
 * Получает все типы загружаемых файлов.
 *
 * Возвращает массив типов загружаемых файлов:
 * 'id' - идентификатор типа.
 * 'extension' - расширение файла.
 * 'store_extension' - сохраняемое расширение файла.
 * 'upload_handler' - обработчик загружаемых файлов, обслуживающий данный тип.
 * 'thumbnail_image' - имя картинки для файлов, не являющихся изображением.
 */
function upload_types_get_all()
{
	return db_upload_types_get_all(DataExchange::getDBLink());
}
/**
 * Получает типы файлов, доступных для загрузки на заданной доске.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @return array
 * Возвращает массив типов загружаемых файлов:<p>
 * 'id' - идентификатор.<br>
 * 'extension' - расширение.<br>
 * 'store_extension' - сохраняемое расширение.<br>
 * 'upload_handler' - идентификатор обработчика загружаемых файлов.<br>
 * 'upload_handler_name' - имя обработчика загружаемых файлов.<br>
 * 'thumbnail_image' - картинка для файлов, не являющихся изображением.</p>
 */
function upload_types_get_board($board_id)
{
	return db_upload_types_get_board(DataExchange::getDBLink(), $board_id);
}
/**
 * Получает одинаковые файлы, загруженные на заданную доску.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param hash string <p>Хеш файла.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает массив загруженных файлов:<p>
 * 'id' - идентификатор.<br>
 * 'hash' - хеш файла.<br>
 * 'is_image' - флаг картинки.<br>
 * 'file_name' - имя файла.<br>
 * 'file_w' - ширина файла (для изображений).<br>
 * 'file_h' - высота файла (для изображений).<br>
 * 'size' - размер файла в байтах.<br>
 * 'thumbnail_name' - имя уменьшенной копии (для изображений).<br>
 * 'thumbnail_w' - ширина уменьшенной копии (для изображений).<br>
 * 'thumbnail_h' - высота уменьшенной копии (для изображений).<br>
 * 'post_number' - номер сообщения, к которому прикреплен файл.<br>
 * 'thread_number' - номер нити с сообщением, к которому прикреплен файл.<br>
 * 'view' - видно ли сообщение пользователю.</p>
 */
function uploads_get_same($board_id, $hash, $user_id)
{
	return db_uploads_get_same(DataExchange::getDBLink(), $board_id, $hash,
		$user_id);
}
/**
 * Сохраняет данные о загруженном файле.
 * @param board_id string <p>Идентификатор доски.</p>
 * @param hash string <p>Хеш файла.</p>
 * @param is_image string <p>Является файл изображением или нет.</p>
 * @param file_name string <p>Относительный путь к файлу.</p>
 * @param file_w string <p>Ширина изображения (для изображений).</p>
 * @param file_h string <p>Высота изображения (для изображений).</p>
 * @param size string <p>Размер файла в байтах.</p>
 * @param thumbnail_name string <p>Относительный путь к уменьшенной копии.</p>
 * @param thumbnail_w string <p>Ширина уменьшенной копии (для изображений).</p>
 * @param thumbnail_h string <p>Высота уменьшенной копии (для изображений).</p>
 * @return string
 * Возвращает идентификатор поля с сохранёнными данными.
 */
function uploads_add($board_id, $hash, $is_image, $file_name, $file_w, $file_h,
	$size, $thumbnail_name, $thumbnail_w, $thumbnail_h)
{
	return db_uploads_add(DataExchange::getDBLink(), $board_id, $hash,
		$is_image, $file_name, $file_w, $file_h, $size, $thumbnail_name,
		$thumbnail_w, $thumbnail_h);
}
/**
 * Проверяет корректность расширения загружаемого файла.
 *
 * Аргументы:
 * $ext - расширение загружаемого файла.
 *
 * Возвращает безопасное для использования расширение загружаемого файла.
 */
function upload_types_check_extension($ext)
{
	if(!isset($ext))
		throw new NodataException(NodataException::$messages['UPLOAD_TYPE_EXTENSION_NOT_SPECIFED']);
	$length = strlen($ext);
	if($length <= 10 && $length >= 1)
	{
		$ext = RawUrlEncode($ext);
		$length = strlen($ext);
		if($length > 10 || (strpos($ext, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['UPLOAD_TYPE_EXTENSION']);
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_TYPE_EXTENSION']);
	return $ext;
}
/**
 * Проверяет корректность сохраняемого расширения загружаемого файла.
 *
 * Аргументы:
 * $store_ext - сохраняемое расширение загружаемого файла.
 *
 * Возвращает безопасное для использования сохраняемое расширение загружаемого
 * файла.
 */
function upload_types_check_store_extension($store_ext)
{
	if(!isset($store_ext))
		throw new NodataException(NodataException::$messages['UPLOAD_TYPE_STORE_EXTENSION_NOT_SPECIFED']);
	$length = strlen($store_ext);
	if($length <= 10 && $length >= 1)
	{
		$store_ext = RawUrlEncode($store_ext);
		$length = strlen($store_ext);
		if($length > 10 || (strpos($store_ext, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['UPLOAD_TYPE_STORE_EXTENSION']);
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_TYPE_STORE_EXTENSION']);
	return $store_ext;
}
/**
 * Проверяет корректность имени картинки для файла, не являющегося изображением.
 *
 * Аргументы:
 * $thumbnail_image - имя картинки для файла, не являющегося изображением.
 *
 * Возвращает безопасное для использования имя картинки для файла, не
 * являющегося изображением.
 */
function upload_types_check_thumbnail_image($thumbnail_image)
{
	if(!isset($thumbnail_image))
		throw new NodataException(NodataException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE_NOT_SPECIFED']);
	$length = strlen($thumbnail_image);
	if($length <= 256 && $length >= 1)
	{
		$thumbnail_image = RawUrlEncode($thumbnail_image);
		$length = strlen($thumbnail_image);
		if($length > 256 || (strpos($thumbnail_image, '%') !== false) || $length < 1)
			throw new FormatException(FormatException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE']);
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE']);
	return $thumbnail_image;
}
/**
 * Редактирует тип загружаемых файлов.
 * @param id mixed <p>Идентификатор типа.</p>
 * @param store_extension string <p>Сохраняемое расширение файла.</p>
 * @param is_image mixed <p>Флаг типа файлов изображений.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемых
 * файлов.</p>
 * @param thumbnail_image string <p>Имя картинки для файлов, не являющихся
 * изображением.</p>
 */
function upload_types_edit($id, $store_extension, $is_image,
	$upload_handler_id, $thumbnail_image)
{
	db_upload_types_edit(DataExchange::getDBLink(), $id, $store_extension,
		$is_image, $upload_handler_id, $thumbnail_image);
}
/**
 * Добавляет новый тип загружаемых файлов.
 * @param extension string <p>Расширение файла.</p>
 * @param store_extension string <p>Сохраняемое расширение файла.</p>
 * @param is_image mixed <p>Флаг типа файлов изображений.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемых
 * файлов.</p>
 * @param thumbnail_image string <p>Имя картинки для файлов, не являющихся
 * изображением.</p>
 */
function upload_types_add($extension, $store_extension, $is_image,
	$upload_handler_id, $thumbnail_image)
{
	db_upload_types_add(DataExchange::getDBLink(), $extension, $store_extension,
		$is_image, $upload_handler_id, $thumbnail_image);
}
/**
 * Удаляет тип загружаемых файлов.
 *
 * Аргументы:
 * $id - идентифаикатор типа загружаемых файлов.
 */
function upload_types_delete($id)
{
	db_upload_types_delete(DataExchange::getDBLink(), $id);
}
/**
 * Проверяет корректность идентификатора типа загружаемых файлов.
 *
 * Аргументы:
 * $id - идентификатор типа загружаемых файлов.
 *
 * Возвращает безопасный для использования идентификатор типа загружаемых файлов.
 */
function upload_types_check_id($id)
{
	if(!isset($id))
		throw new NodataException(NodataException::$messages['UPLOAD_TYPE_ID_NOT_SPECIFED']);
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['UPLOAD_TYPE_ID']);
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_TYPE_ID']);
	return $id;
}

/*********************************************
 * Работа со связями типов файлов с досками. *
 *********************************************/

/**
 * Получает все связи типов файлов с досками.
 *
 * Возвращает связи:
 * 'board' - идентификатор доски.
 * 'upload_type' - идентификатор типа файла.
 */
function board_upload_types_get_all()
{
	return db_board_upload_types_get_all(DataExchange::getDBLink());
}
/**
 * Добавляет связь типа загружаемого файла с доской.
 *
 * Аргументы:
 * $board_id - идентификатор доски.
 * $upload_type_id - идтенификатор типа загружаемого файла.
 */
function board_upload_types_add($board_id, $upload_type_id)
{
	db_board_upload_types_add(DataExchange::getDBLink(), $board_id,
		$upload_type_id);
}
/**
 * Удаляет связь типа загружаемого файла с доской.
 *
 * Аргументы:
 * $board_id - идентификатор доски.
 * $upload_type_id - идтенификатор типа загружаемого файла.
 */
function board_upload_types_delete($board_id, $upload_type_id)
{
	db_board_upload_types_delete(DataExchange::getDBLink(), $board_id,
		$upload_type_id);
}

/********************
 * Работа с нитями. *
 ********************/

/**
 * Получает все нити.
 * 
 * Возвращает нити:
 * 'id' - идентификатор нити.
 * 'board' - идентификатор доски.
 * 'original_post' - оригинальный пост.
 * 'bump_limit' - специфичный для нити бамплимит.
 * 'sage' - флаг поднятия нити при ответе.
 * 'with_images' - флаг прикрепления файлов к ответам в нить.
 */
function threads_get_all()
{
	return db_threads_get_all(DataExchange::getDBLink());
}
/**
 * Проверяет корректность бамплимита.
 *
 * Аргументы:
 * $bump_limit - бампилимит.
 *
 * Возвращает безопасный для использования бампилимит.
 */
function threads_check_bump_limit($bump_limit)
{
	$length = strlen($bump_limit);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$bump_limit = RawUrlEncode($bump_limit);
		$length = strlen($bump_limit);
		if($length > $max_int_length || (ctype_digit($bump_limit) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['THREAD_BUMP_LIMIT']);
	}
	else
		throw new FormatException(FormatException::$messages['THREAD_BUMP_LIMIT']);
	return $bump_limit;
}
/**
 * Проверяет корректность флага поднятия нити при ответе.
 *
 * Аргументы:
 * $sage - флаг поднятия нити при ответе.
 *
 * Возвращает безопасный для использования флаг поднятия нити при ответе.
 */
function threads_check_sage($sage)
{
	if($sage === '1' || $sage === '0')
		return $sage;
	else
		throw new FormatException(FormatException::$messages['THREAD_SAGE']);
}
/**
 * Проверяет корректность флага прикрепления файлов к ответам в нить.
 *
 * Аргументы:
 * $sage - флаг прикрепления файлов к ответам в нить.
 *
 * Возвращает безопасный для использования флаг прикрепления файлов к ответам в
 * нить.
 */
function threads_check_with_images($with_images)
{
	if($with_images === '1' || $with_images === '0')
		return $with_images;
	else
		throw new FormatException(FormatException::$messages['THREAD_WITH_IMAGES']);
}
/**
 * Проверяет корректность номера нити.
 *
 * Аргументы:
 * $number - номер нити.
 *
 * Возвращает безопасный для использования номер нити.
 */
function threads_check_number($number)
{
	$length = strlen($number);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$number = RawUrlEncode($number);
		$length = strlen($number);
		if($length > $max_int_length || (ctype_digit($number) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['THREAD_NUMBER']);
	}
	else
		throw new FormatException(FormatException::$messages['THREAD_NUMBER']);
	return $number;
}
/**
 * Редактирует настройки нити.
 *
 * Аргументы:
 * $thread_id - идентификатор нити.
 * $bump_limit - бамплимит.
 * $sage - флаг поднятия нити при ответе
 * $with_images - флаг прикрепления файлов к ответам в нить.
 */
function threads_edit($thread_id, $bump_limit, $sage, $with_images)
{
	db_threads_edit(DataExchange::getDBLink(), $thread_id, $bump_limit, $sage,
		$with_images);
}
/**
 * Редактирует оригинальное сообщение нити.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param original_post mixed <p>Номер оригинального сообщения нити.</p>
 */
function threads_edit_originalpost($thread_id, $original_post)
{
	db_threads_edit_originalpost(DataExchange::getDBLink(), $thread_id,
		$original_post);
}
/**
 * Создаёт нить. Если номер оригинального сообщения null, то будет создана
 * "пустая" нить.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param original_post mixed <p>Номер оригинального сообщения нити.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sage mixed <p>Не поднимать нить ответами.</p>
 * @param with_images mixed <p>Флаг прикрепления файлов к ответам в нить.</p>
 * @return array
 * Возвращает нить.
 */
function threads_add($board_id, $original_post, $bump_limit, $sage, $with_images)
{
	return db_threads_add(DataExchange::getDBLink(), $board_id, $original_post,
		$bump_limit, $sage, $with_images);
}
/**
 * Получает нити, доступные для модерирования пользователю с заданным
 * идентификатором.
 * 
 * Аргументы:
 * $user_id - идентификатор пользователя.
 *
 * Возвращает нити:
 * 'id' - идентификатор нити.
 * 'board' - идентификатор доски.
 * 'original_post' - оригинальный пост.
 * 'bump_limit' - специфичный для нити бамплимит.
 * 'sage' - не поднимать нить при ответе в неё.
 * 'with_images' - разрешить прикреплять файлы к ответам в нить.
 */
function threads_get_all_moderate($user_id)
{
	return db_threads_get_all_moderate(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает $threads_per_page нитей со страницы $page доски с идентификатором
 * $board_id, доступные для чтения пользователю с идентификатором
 * $user_id. А так же количество доступных для просмотра сообщений в этих нитях.
 *
 * Аргументы:
 * $board_id - идентификатор доски.
 * $page - номер страницы.
 * $user_id - идентификатор пользователя.
 * $threads_per_page - количество нитей ни странице.
 *
 * Возвращает нити:
 * 'id' - идентификатор нити.
 * 'original_post' - оригинальный пост.
 * 'bump_limit' - специфичный для нити бамплимит.
 * 'sage' - не поднимать нить при ответе в неё.
 * 'with_images' - разрешить прикреплять файлы к ответам в нить.
 * 'posts_count' - число доступных для просмотра сообщений в нити.
 */
function threads_get_board_view($board_id, $page, $user_id,
	$threads_per_page)
{
	return db_threads_get_board_view(DataExchange::getDBLink(), $board_id, $page,
		$user_id, $threads_per_page);
}
/**
 * Вычисляет количество нитей, доступных для просмотра заданному пользователю
 * на заданной доске.
 *
 * Аргументы:
 * $user_id - идентификатор пользователя.
 * $board_id - идентификатор доски.
 *
 * Возвращает строку, содержащую число нитей.
 */
function threads_get_view_threadscount($user_id, $board_id)
{
	return db_threads_get_view_threadscount(DataExchange::getDBLink(),
		$user_id, $board_id);
}
/**
 * Получает нить с номером $thread_num на доске с идентификатором $board_id,
 * доступную для просмотра пользователю с идентификатором $user_id. А так же
 * число сообщений в нити, видимых этим пользователем.
 *
 * Аргументы:
 * $board_id - идентификатор доски.
 * $thread_num - номер нити.
 * $user_id - идентификатор пользователя.
 *
 * Возвращает нить:
 * 'id' - идентификатор.
 * 'board' - идентификатор доски.
 * 'original_post' - оригинальное сообщение.
 * 'bump_limit' - специфичный для нити бамплимит.
 * 'sage' - флаг поднятия нити
 * 'with_images' - разрешить прикреплять файлы к ответам в нить.
 * 'archived' - нить помечена для архивирования.
 * 'posts_count' - число сообщений.
 */
function threads_get_specifed_view($board_id, $thread_num, $user_id)
{
	return db_threads_get_specifed_view(DataExchange::getDBLink(), $board_id, $thread_num, $user_id);
}
/**
 * Проверяет, доступна ли нить для модерирования пользователю.
 *
 * Аргументы:
 * $thread_id - идентификатор нити.
 * $user_id - идентификатор пользователя.
 *
 * Возвращает true или false.
 */
function threads_check_specifed_moderate($thread_id, $user_id)
{
	return db_threads_check_specifed_moderate(DataExchange::getDBLink(),
		$thread_id, $user_id);
}
/**
 * Получает нить доступную для редактирования заданному пользователю.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sage' - флаг поднятия нити.<br>
 * 'with_files' - разрешить прикреплять файлы к ответам в нить.<br>
 * 'archived' - нить помечена для архивирования.</p>
 */
function threads_get_specifed_change($thread_id, $user_id)
{
	return db_threads_get_specifed_change(DataExchange::getDBLink(), $thread_id,
		$user_id);
}

/*************************
 * Работа с сообщениями. *
 *************************/

/**
 * Получает $posts_per_thread сообщений и оригинальное сообщение для каждой
 * нити из $threads, доступных для чтения пользователю с идентификатором
 * $user_id.
 *
 * Аргументы:
 * $threads - нити.
 * $user_id - идентификатор пользователя.
 * $posts_per_thread - количество сообщений, которое необходимо вернуть.
 *
 * Возвращает сообщения:
 * 'id' - идентификатор.
 * 'thread' - идентификатор нити.
 * 'number' - номер.
 * 'password' - пароль для удаления.
 * 'name' - имя отправителя.
 * 'ip' - ip адрес отправителя.
 * 'subject' - тема.
 * 'date_time' - время сохранения.
 * 'text' - текст.
 * 'sage' - флаг поднятия нити.
 */
function posts_get_threads_view($threads, $user_id, $posts_per_thread)
{
	return db_posts_get_threads_view(DataExchange::getDBLink(), $threads,
		$user_id, $posts_per_thread);
}
/**
 * Добавляет сообщение.
 * @param board_id mixed<p>Идентификатор доски.</p>
 * @param thread_id mixed<p>Идентификатор нити.</p>
 * @param user_id mixed<p>Идентификатор автора.</p>
 * @param password string <p>Пароль на удаление сообщения.</p>
 * @param name string <p>Имя автора.</p>
 * @param ip int <p>IP адрес автора.</p>
 * @param subject string <p>Тема.</p>
 * @param datetime string <p>Время получения сообщения.</p>
 * @param text string <p>Текст.</p>
 * @param sage mixed <p>Не поднимать нить этим сообщением.</p>
 * @return array
 * Возвращает сообщение.
 */
function posts_add($board_id, $thread_id, $user_id, $password, $name,
	$ip, $subject, $datetime, $text, $sage)
{
	return db_posts_add(DataExchange::getDBLink(), $board_id, $thread_id,
		$user_id, $password, $name, $ip, $subject, $datetime, $text, $sage);
}
/**
 * Урезает длинное сообщение.
 * TODO: limit only lines
 *
 * arguments:
 * $message - текст сообщения.
 * $preview_lines - количество строк, которые нужно оставить.
 * $is_cutted - ссылка на флаг, было ли сообщение урезано или нет.
 *
 * Возвращает урезанное сообщение.
 */
function posts_corp_text(&$message, $preview_lines, &$is_cutted)
{
	$lines = explode("<br>", $message);
	if(count($lines) > $preview_lines) {
		$is_cutted = 1;
		return implode("<br>", array_slice($lines, 0, $preview_lines));
	}
	else {
		$is_cutted = 0;
		return $message;
	}
}

/******************************************************************
 * Работа со связями сообщений и информации о загруженных файлах. *
 ******************************************************************/

/**
 * Получает для каждого сообщения из $posts его связь с информацией о
 * загруженных файлах.
 *
 * Аргументы:
 * $posts - сообщения.
 *
 * Возвращает связи:
 * 'post' - идентификатор сообщения.
 * 'upload' - идентификатор загруженного файла.
 */
function posts_uploads_get_posts($posts)
{
	return db_posts_uploads_get_posts(DataExchange::getDBLink(), $posts);
}
/**
 * Связывает сообщение с загруженным файлом.
 * @param post_id mixed <p>идентификатор сообщения.</p>
 * @param upload_id mixed <p>идентификатор сообщения.</p>
 */
 function posts_uploads_add($post_id, $upload_id)
 {
	db_posts_uploads_add(DataExchange::getDBLink(), $post_id, $upload_id);
 }

/**********************************************
 * Работа с информацией о загруженных файлах. *
 **********************************************/

/**
 * Получает для каждого сообщения из $posts информацию о загруженных файлах.
 *
 * Аргументы:
 * $posts - сообщения.
 *
 * Возвращает информацию о загруженных файлах:
 * 'id' - идентификатор.
 * 'hash' - хеш файла.
 * 'is_image' - флаг картинки.
 * 'file_name' - имя файла.
 * 'file_w' - ширина файла (для изображений).
 * 'file_h' - высота файла (для изображений).
 * 'size' - размер файла в байтах.
 * 'thumbnail_name' - имя уменьшенной копии (для изображений).
 * 'thumbnail_w' - ширина уменьшенной копии (для изображений).
 * 'thumbnail_h' - высота уменьшенной копии (для изображений).
 */
function uploads_get_posts($posts)
{
	return db_uploads_get_posts(DataExchange::getDBLink(), $posts);
}
/**
 * Проверяет, удовлетворяет ли загружаемое изображение ограничениям по размеру.
 *
 * Аргументы:
 * $img_size - размер изображения в байтах.
 */
function uploads_check_image_size($img_size)
{
	if($img_size < Config::MIN_IMGSIZE)
		throw new LimitException(LimitException::$messages['MIN_IMG_SIZE']);
}

/******************************
 * Работа со скрытыми нитями. *
 ******************************/

/**
 * Получает нити, скрыте пользователем с идентификатором $user_id на
 * доске с идентификатором $board_id.
 *
 * Аргументы:
 * $board_id - идентификатор доски.
 * $user_id - идентификатор пользователя.
 *
 * Возвращает скрытые нити:
 * 'number' - номер оригинального сообщения.
 * 'id' - идентификатор нити.
 */
function hidden_threads_get_board($board_id, $user_id)
{
	return db_hidden_threads_get_board(DataExchange::getDBLink(), $board_id,
		$user_id);
}
?>