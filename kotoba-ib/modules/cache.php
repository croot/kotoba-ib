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
 * @param name string <p>Имя новой доски.</p>
 * @return boolean
 * Возвращает TRUE в случае успешного создания директорий и FALSE в противном
 * случае.
 */
function create_directories($name) {
	$base = Config::ABS_PATH . "/$name";
	if(@mkdir ($base)) {		// Hide warning when directory exists.
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
/**
 * Создаёт необходимые директории при добавлении нового языка.
 * @param name string <p>Имя нового языка.</p>
 */
function create_language_directories($name) {
	$dir = Config::ABS_PATH . "/smarty/kotoba/templates/$name";
	@mkdir($dir);		// Hide warning when directory exists.
	chmod($dir, 0777);
	$dir = Config::ABS_PATH . "/smarty/kotoba/templates_c/$name";
	@mkdir($dir);		// Hide warning when directory exists.
	chmod($dir, 0777);
	$dir = Config::ABS_PATH . "/modules/lang/$name";
	@mkdir($dir);		// Hide warning when directory exists.
	chmod($dir, 0777);
}

/******************************
 * Блокировки адресов (баны). *
 ******************************/

/**
 * Проверяет, заблокирован ли IP адрес. Если да, то зверашет работу скрипта.
 * @param smarty SmartyKotobaSetup <p>Экземпляр шаблонизатора.</p>
 * @param ip string <p>IP адрес.</p>
 */
function bans_check($smarty, $ip)
{
	if(($ban = db_bans_check(DataExchange::getDBLink(), $ip)) !== false)
	{
		$smarty->assign('ip', long2ip($ip));
		$smarty->assign('reason', $ban['reason']);
		session_destroy();
		DataExchange::releaseResources();
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
 * Редактирует аннотацию доски.
 * @param id mixed <p>Идентификатор.</p>
 * @param annotation string <p>Аннотация.</p>
 */
function boards_edit_annotation($id, $annotation)
{
	db_boards_edit_annotation(DataExchange::getDBLink(), $id, $annotation);
}
/**
 * Получает доски, доступные для чтения пользователю.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор доски.<br>
 * 'name' - имя доски.<br>
 * 'title' - заголовок доски.<br>
 * 'annotation' - аннотация.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имя отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'same_upload' - политика загрузки одинаковых изображений.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория доски.</p>
 */
function boards_get_all_view($user_id)
{
	return db_boards_get_all_view(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает доски, доступные для редактирования пользователю.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имя отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_all_change($user_id)
{
	return db_boards_get_all_change(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает все доски.
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'annotation' - аннотация.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имени отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
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
 * 'force_anonymous' - флаг отображения имя отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_specifed($board_id)
{
	return db_boards_get_specifed(DataExchange::getDBLink(), $board_id);
}
/**
 * Получает доску по заданному имени.
 * @param board_name string <p>Имя доски.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор доски.<br>
 * 'name' - имя доски.<br>
 * 'title' - заголовок доски.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имя отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_specifed_byname($board_name)
{
	return db_boards_get_specifed_byname(DataExchange::getDBLink(), $board_name);
}
/**
 * Получает доску по заданному имени, доступную для редактирования пользователю.
 * @param board_name string <p>Имя доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имя отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_specifed_change_byname($board_name, $user_id)
{
	return db_boards_get_specifed_change_byname(DataExchange::getDBLink(),
		$board_name, $user_id);
}
/**
 * Получает доску, доступную для редактирования пользователю.
 * @param board_id string <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имя отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_specifed_change($board_id, $user_id)
{
	return db_boards_get_specifed_change(DataExchange::getDBLink(), $board_id,
		$user_id);
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
 * @param name string <p>Имя доски.</p>
 * @return string
 * Возвращает безопасное для использования имя доски.
 */
function boards_check_name($name)
{
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
 * @param title string <p>Заголовок доски.</p>
 * @return string
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
 * Проверяет корректность специфичного для доски бамплимита.
 * @param $bump_limit string <p>Сецифичный для доски бампилимит.</p>
 * @return string
 * Возвращает безопасный для использования сецифичный для доски бампилимит.
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
 * Проверяет корректность имени по умолчанию.
 * @param name string <p>Имя по умолчанию.</p>
 * @return string
 * Возвращает безопасное для использования имя по умолчанию.
 */
function boards_check_default_name($name)
{
	posts_check_name_size($name);
	$name = htmlentities($name, ENT_QUOTES, Config::MB_ENCODING);
	posts_check_name_size($name);
	return $name;
}
/**
 * Проверяет корректность политики загрузки одинаковых файлов.
 * @param same_upload string <p>Политика загрузки одинаковых файлов.</p>
 * @return string
 * Возвращает безопасную для использования политику загрузки одинаковых файлов.
 */
function boards_check_same_upload($same_upload)
{
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
 * Проверяет корректность аннотации.
 * @param annotation string <p>Аннотация.</p>
 * @return string
 * Возвращает аннотацию.
 */
function boards_check_annotation($annotation)
{
	if(strlen($annotation) > Config::MAX_ANNOTATION_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_ANNOTATION']);
	return $annotation;
}
/**
 * Редактирует параметры доски.
 * @param id mixed <p>Идентификатор.</p>
 * @param title string <p>Заголовок.</p>
 * @param bump_limit mixed <p>Специфичный для доски бамплимит.</p>
 * @param force_anonymous string <p>Флаг отображения имени отправителя.</p>
 * @param default_name string <p>Имя отправителя по умолчанию.</p>
 * @param with_files string <p>Флаг загрузки файлов.</p>
 * @param same_upload string <p>Политика загрузки одинаковых файлов.</p>
 * @param popdown_handler mixed <p>Обработчик удаления нитей.</p>
 * @param category mixed <p>Категория.</p>
 */
function boards_edit($id, $title, $bump_limit, $force_anonymous, $default_name,
	$with_files, $same_upload, $popdown_handler, $category)
{
	db_boards_edit(DataExchange::getDBLink(), $id, $title, $bump_limit,
		$force_anonymous, $default_name, $with_files, $same_upload,
		$popdown_handler, $category);
}
/**
 * Добавляет доску.
 * @param name string <p>Имя доски.</p>
 * @param title string <p>Заголовок.</p>
 * @param bump_limit mixed <p>Специфичный для доски бамплимит.</p>
 * @param force_anonymous string <p>Флаг отображения имени отправителя.</p>
 * @param default_name string <p>Имя отправителя по умолчанию.</p>
 * @param with_files string <p>Флаг загрузки файлов.</p>
 * @param same_upload string <p>Политика загрузки одинаковых файлов.</p>
 * @param popdown_handler mixed <p>Обработчик удаления нитей.</p>
 * @param category mixed <p>Категория.</p>
 */
function boards_add($name, $title, $bump_limit, $force_anonymous, $default_name,
	$with_files, $same_upload, $popdown_handler, $category)
{
	db_boards_add(DataExchange::getDBLink(), $name, $title, $bump_limit,
		$force_anonymous, $default_name, $with_files, $same_upload,
		$popdown_handler, $category);
}
/**
 * Удаляет заданную доску.
 * @param id mixed <p>Идентификатор доски.</p>
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
 * @param id mixed <p>Идентификатор категории.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор категории.
 */
function categories_check_id($id)
{
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
 * @return array
 * Возвращает категории:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.</p>
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
 * Получает настройки ползователя с заданным ключевым словом.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @return array
 * Возвращает настройки:<p>
 * 'id' - идентификатор пользователя.<br>
 * 'posts_per_thread' - количество последних сообщений в нити при просмотре доски.<br>
 * 'threads_per_page' - количество нитей на странице при просмотре доски.<br>
 * 'lines_per_post' - количество строк в урезанном сообщении при просмотре доски.<br>
 * 'language' - язык.<br>
 * 'stylesheet' - стиль оформления.<br>
 * 'rempass' - пароль для удаления сообщений.<br>
 * 'goto' - перенаправление при постинге.<br>
 * 'groups' - массив групп, в которые входит пользователь.</p>
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
 * Проверяет корректность перенаправления при постинге.
 * @param goto string <p>Перенаправление при постинге.</p>
 * Возвращает безопасное для использования перенаправление при постинге.
 */
function users_check_goto($goto)
{
	if($goto == 'b' || $goto == 't')
		return $goto;
	else
		throw new FormatException(FormatException::$messages['GOTO']);
}
/**
 * Редактирует настройки пользователя с заданным ключевым словом или добавляет
 * нового.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @param threads_per_page mixed <p>Количество нитей на странице предпросмотра доски.</p>
 * @param posts_per_thread mixed <p>Количество сообщений в предпросмотре треда.</p>
 * @param lines_per_post mixed <p>Максимальное количество строк в предпросмотре сообщения.</p>
 * @param stylesheet mixed <p>Стиль оформления.</p>
 * @param language mixed <p>Язык.</p>
 * @param rempass mixed <p>Пароль для удаления сообщений.</p>
 * @param goto string <p>Перенаправление при постинге.</p>
 */
function users_edit_bykeyword($keyword, $threads_per_page, $posts_per_thread,
	$lines_per_post, $stylesheet, $language, $rempass, $goto)
{
	db_users_edit_bykeyword(DataExchange::getDBLink(), $keyword,
		$threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet,
		$language, $rempass, $goto);
}
/**
 * Получает всех пользователей.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает идентификаторы пользователей:<p>
 * 'id' - идентификатор пользователя.</p>
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
 * Проверяет корректность имени языка.
 * @param name string <p>Имя языка.</p>
 * @return string
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
 * Добавляет новый язык.
 * @param name string <p>Имя нового языка.</p>
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

/*************************
 * Работа с сообщениями. *
 *************************/

/**
 * Проверяет корректность идентификатора сообщения.
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @return string
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
 * @param number mixed <p>Номер сообщения.</p>
 * @return string
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
 * Проверяет, удовлетворяет ли текст сообщения ограничениям по размеру.
 * @param text string <p>Текст сообщения.</p>
 */
function posts_check_text_size($text)
{
	if(mb_strlen($text) > Config::MAX_MESSAGE_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_TEXT_LENGTH']);
}
/**
 * Проверяет, удовлетворяет ли тема сообщения ограничениям по размеру.
 * @param subject string <p>Тема сообщения.</p>
 */
function posts_check_subject_size($subject)
{
	if(strlen($subject) > Config::MAX_THEME_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_SUBJECT_LENGTH']);
}
/**
 * Проверяет, удовлетворяет ли имя отправителя ограничениям по размеру.
 * @param name string <p>Имя отправителя.</p>
 */
function posts_check_name_size($name)
{
	if(strlen($name) > Config::MAX_THEME_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_NAME_LENGTH']);
}
/**
 * Получает $posts_per_thread сообщений и оригинальное сообщение для каждой
 * нити из $threads, доступных для чтения пользователю с идентификатором
 * $user_id.
 * @param threads array <p>Нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param posts_per_thread mixed <p>Количество сообщений, которое необходимо вернуть.</p>
 * @return array
 * Возвращает сообщения:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function posts_get_threads_view($threads, $user_id, $posts_per_thread)
{
	return db_posts_get_threads_view(DataExchange::getDBLink(), $threads,
		$user_id, $posts_per_thread);
}
/**
 * Получает все сообщения заданной нити.
 * @param thread_id array <p>Идентификатор нити.</p>
 * @return array
 * Возвращает сообщения:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function posts_get_thread($thread_id)
{
	return db_posts_get_thread(DataExchange::getDBLink(), $thread_id);
}
/**
 * Получает сообщение по номеру.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param post_num mixed <p>Номер сообщения.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает сообщение:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function posts_get_specifed_view_bynumber($board_id, $post_num, $user_id)
{
	return db_posts_get_specifed_view_bynumber(DataExchange::getDBLink(),
		$board_id, $post_num, $user_id);
}
/**
 * Добавляет сообщение.
 * @param board_id mixed<p>Идентификатор доски.</p>
 * @param thread_id mixed<p>Идентификатор нити.</p>
 * @param user_id mixed<p>Идентификатор автора.</p>
 * @param password string <p>Пароль на удаление сообщения.</p>
 * @param name string <p>Имя автора.</p>
 * @param tripcode string <p>Трипкод.</p>
 * @param ip int <p>IP адрес автора.</p>
 * @param subject string <p>Тема.</p>
 * @param datetime string <p>Время получения сообщения.</p>
 * @param text string <p>Текст.</p>
 * @param sage mixed <p>Флаг поднятия нити.</p>
 * @return array
 * Возвращает сообщение.
 */
function posts_add($board_id, $thread_id, $user_id, $password, $name, $tripcode,
	$ip, $subject, $datetime, $text, $sage)
{
	return db_posts_add(DataExchange::getDBLink(), $board_id, $thread_id,
		$user_id, $password, $name, $tripcode, $ip, $subject, $datetime, $text,
		$sage);
}
/**
 * Урезает длинное сообщение.
 * TODO: Урезание в длины.
 * @param message string <p>Текст сообщения.</p>
 * @param preview_lines mixed <p>Количество строк, которые нужно оставить.</p>
 * @param is_cutted boolean <p>Ссылка на флаг урезанного сообщения.</p>
 * @return string
 * Возвращает урезанное сообщение.
 */
function posts_corp_text(&$message, $preview_lines, &$is_cutted)
{
	$lines = explode('<br>', $message);
	if(count($lines) > $preview_lines) {
		$is_cutted = 1;
		return implode('<br>', array_slice($lines, 0, $preview_lines));
	}
	else {
		$is_cutted = 0;
		return $message;
	}
}
/**
 * Удаляет сообщение с заданным идентификатором.
 * @param id mixed <p>Идентификатор сообщения.</p>
 */
function posts_delete($id)
{
	db_posts_delete(DataExchange::getDBLink(), $id);
}
/**
 * Добавляет текст в конец текста заданного сообщения.
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param text string <p>Текст.</p>
 */
function posts_edit_specifed_addtext($id, $text)
{
	db_posts_edit_specifed_addtext(DataExchange::getDBLink(), $id, $text);
}
/**
 * Получает все сообщения с номерами нитей и именем доски.
 * @return array
 * Возвращает сообщения:<p>
 * 'post' - номер сообщения.<br>
 * 'thread' - номер нити.<br>
 * 'board' - номер доски.</p>
 */
function posts_get_all_numbers()
{
	return db_posts_get_all_numbers(DataExchange::getDBLink());
}

/**********************************************
 * Работа с обработчиками загружаемых файлов. *
 **********************************************/

/**
 * Получает все обработчики загружаемых файлов.
 * @return array
 * Возвращает обработчики загружаемых файлов:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.</p>
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
	if(!isset($name))
		throw new NodataException(NodataException::$messages['UPLOAD_HANDLER_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false)
			|| $length < 1 || ctype_digit($name[0]))
			throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_NAME']);
	return $name;
}
/**
 * Проверяет корректность идентификатора обработчика загружаемых файлов.
 * @param id mixed <p>Идентификатор обработчика загружаемых файлов.</p>
 * @return string
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
 * @return array
 * Возвращает обработчики удаления нитей:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.</p>
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
	if(!isset($name))
		throw new NodataException(NodataException::$messages['POPDOWN_HANDLER_NAME_NOT_SPECIFED']);
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false)
			|| $length < 1 || ctype_digit($name[0]))
			throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_NAME']);
	}
	else
		throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_NAME']);
	return $name;
}
/**
 * Проверяет корректность идентификатора обработчика удаления нитей.
 * @param id mixed <p>Идентификатор обработчика удаления нитей.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор обработчика удаления
 * нитей.
 */
function popdown_handlers_check_id($id)
{
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
 * @return array
 * Возвращает типы загружаемых файлов:<p>
 * 'id' - идентификатор.<br>
 * 'extension' - расширение файла.<br>
 * 'store_extension' - сохраняемое расширение файла.<br>
 * 'upload_handler' - обработчик загружаемых файлов, обслуживающий данный тип.<br>
 * 'thumbnail_image' - имя картинки для файлов, не являющихся изображением.</p>
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
 * Проверяет корректность расширения загружаемого файла.
 * @param ext string <p>Расширение загружаемого файла.</p>
 * @return string
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
 * @param store_ext string <p>Сохраняемое расширение загружаемого файла.</p>
 * @return string
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
 * @param thumbnail_image string <p>имя картинки для файла, не являющегося изображением.</p>
 * @return string
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
 * @param id mixed <p>Идентифаикатор типа загружаемых файлов.</p>
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
 * @return array
 * Возвращает нити:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити при ответе.<br>
 * 'with_files' - флаг загрузки файлов.</p>
 */
function threads_get_all()
{
	return db_threads_get_all(DataExchange::getDBLink());
}
/**
 * Получает все нити, помеченные для архивирования.
 * @return array
 * Возвращает нити:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити при ответе.<br>
 * 'with_files' - флаг загрузки файлов.</p>
 */
function threads_get_all_archived()
{
	return db_threads_get_all_archived(DataExchange::getDBLink());
}
/**
 * Проверяет корректность специфичного для нити бамплимита.
 * @param bump_limit mixed <p>Специфичный для нити бампилимит.</p>
 * @return string
 * Возвращает безопасный для использования специфичный для нити бампилимит.
 */
function threads_check_bump_limit($bump_limit)
{
	$length = strlen($bump_limit);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$bump_limit = RawUrlEncode($bump_limit);
		$length = strlen($bump_limit);
		if($length > $max_int_length || (ctype_digit($bump_limit) === false)
			|| $length < 1)
				throw new FormatException(FormatException::$messages['THREAD_BUMP_LIMIT']);
	}
	else
		throw new FormatException(FormatException::$messages['THREAD_BUMP_LIMIT']);
	return $bump_limit;
}
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
/**
 * Проверяет корректность номера нити.
 * @param number mixed <p>Номер нити.</p>
 * @return string
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
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sage mixed <p>Флаг поднятия нити при ответе.</p>
 * @param sticky mixed <p>Флаг закрепления.</p>
 * @param with_files mixed <p>Флаг загрузки файлов.</p>
 */
function threads_edit($thread_id, $bump_limit, $sticky, $sage, $with_files)
{
	db_threads_edit(DataExchange::getDBLink(), $thread_id, $bump_limit, $sticky,
		$sage, $with_files);
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
 * пустая нить.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param original_post mixed <p>Номер оригинального сообщения.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sage mixed <p>Флаг поднятия нити.</p>
 * @param with_files mixed <p>Флаг загрузки файлов.</p>
 * @return array
 * Возвращает нить.
 */
function threads_add($board_id, $original_post, $bump_limit, $sage, $with_files)
{
	return db_threads_add(DataExchange::getDBLink(), $board_id, $original_post,
		$bump_limit, $sage, $with_files);
}
/**
 * Получает нити, доступные для модерирования заданному пользователю.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нити:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити при ответе.<br>
 * 'with_files' - флаг загрузки файлов.</p>
 */
function threads_get_all_moderate($user_id)
{
	return db_threads_get_all_moderate(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает доступные для просмотра пользователю нити и количество сообщений в
 * них, с заданной страницы доски.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param page mixed <p>Номер страницы.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param threads_per_page mixed <p>Количество нитей на странице.</p>
 * @return array
 * Возвращает нити:<p>
 * 'id' - идентификатор.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити при ответе.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'posts_count' - число доступных для просмотра сообщений в нити.</p>
 */
function threads_get_board_view($board_id, $page, $user_id,
	$threads_per_page)
{
	return db_threads_get_board_view(DataExchange::getDBLink(), $board_id,
		$page, $user_id, $threads_per_page);
}
/**
 * Вычисляет количество нитей, доступных для просмотра заданному пользователю
 * на заданной доске.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @return string
 * Возвращает число нитей.
 */
function threads_get_view_threadscount($user_id, $board_id)
{
	return db_threads_get_view_threadscount(DataExchange::getDBLink(),
		$user_id, $board_id);
}
/**
 * Получает доступную для просмотра пользователю нить с заданной доски и
 * количество сообщений в ней.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_num mixed <p>Номер нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'archived' - нить помечена для архивирования.<br>
 * 'posts_count' - число доступных для просмотра сообщений в нити.</p>
 */
function threads_get_specifed_view($board_id, $thread_num, $user_id)
{
	return db_threads_get_specifed_view(DataExchange::getDBLink(), $board_id,
		$thread_num, $user_id);
}
/**
 * Получает доступную для просмотра пользователю скрытую нить с заданной доски
 * и количество сообщений в ней.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_num mixed <p>Номер нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити.<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'archived' - нить помечена для архивирования.<br>
 * 'posts_count' - число доступных для просмотра сообщений в нити.</p>
 */
function threads_get_specifed_view_hiden($board_id, $thread_num, $user_id)
{
	return db_threads_get_specifed_view_hiden(DataExchange::getDBLink(),
		$board_id, $thread_num, $user_id);
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

/*************************************
 * Работа с информацией о загрузках. *
 *************************************/

/**
 * Получает для каждого сообщения из массива сообщений информацию о загрузках.
 * @param posts array <p>Массив сообщений.</p>
 * @return array
 * Возвращает информацию о загруженных файлах:<p>
 * 'id' - идентификатор.<br>
 * 'hash' - хеш файла.<br>
 * 'is_image' - флаг картинки.<br>
 * 'link_type' - тип ссылки на файл.<br>
 * 'file' - файл.<br>
 * 'file_w' - ширина файла (для изображений).<br>
 * 'file_h' - высота файла (для изображений).<br>
 * 'size' - размер файла в байтах.<br>
 * 'thumbnail' - имя уменьшенной копии.<br>
 * 'thumbnail_w' - ширина уменьшенной копии.<br>
 * 'thumbnail_h' - высота уменьшенной копии.</p>
 */
function uploads_get_posts($posts)
{
	return db_uploads_get_posts(DataExchange::getDBLink(), $posts);
}
/**
 * Проверяет, удовлетворяет ли загружаемое изображение ограничениям по размеру.
 * @param img_size mixed <p>Размер изображения в байтах.</p>
 */
function uploads_check_image_size($img_size)
{
	if($img_size < Config::MIN_IMGSIZE)
		throw new LimitException(LimitException::$messages['MIN_IMG_SIZE']);
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
 * 'link_type' - тип ссылки на файл.<br>
 * 'file' - файл.<br>
 * 'file_w' - ширина файла (для изображений).<br>
 * 'file_h' - высота файла (для изображений).<br>
 * 'size' - размер файла в байтах.<br>
 * 'thumbnail' - имя уменьшенной копии.<br>
 * 'thumbnail_w' - ширина уменьшенной копии.<br>
 * 'thumbnail_h' - высота уменьшенной копии.<br>
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
 * Сохраняет данные о загрузке.
 * @param hash string <p>Хеш файла.</p>
 * @param is_image mixed <p>Флаг изображения.</p>
 * @param link_type mixed <p>Тип ссылки на файл.</p>
 * @param file string <p>Файл.</p>
 * @param file_w mixed <p>Ширина изображения (для изображений).</p>
 * @param file_h mixed <p>Высота изображения (для изображений).</p>
 * @param size string <p>Размер файла в байтах.</p>
 * @param thumbnail string <p>Уменьшенная копия.</p>
 * @param thumbnail_w mixed <p>Ширина уменьшенной копии.</p>
 * @param thumbnail_h mixed <p>Высота уменьшенной копии.</p>
 * @return string
 * Возвращает идентификатор поля с сохранёнными данными.
 */
function uploads_add($hash, $is_image, $link_type, $file, $file_w, $file_h,
	$size, $thumbnail, $thumbnail_w, $thumbnail_h)
{
	return db_uploads_add(DataExchange::getDBLink(), $hash, $is_image,
		$link_type, $file, $file_w, $file_h, $size, $thumbnail, $thumbnail_w,
		$thumbnail_h);
}

/******************************
 * Работа со скрытыми нитями. *
 ******************************/

/**
 * Получает нити, скрыте пользователем на заданной доске.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает скрытые нити:<p>
 * 'number' - номер оригинального сообщения.<br>
 * 'id' - идентификатор нити.</p>
 */
function hidden_threads_get_board($board_id, $user_id)
{
	return db_hidden_threads_get_board(DataExchange::getDBLink(), $board_id,
		$user_id);
}
/**
 * Скрывает нить.
 * @param thread_id mixed <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function hidden_threads_add($thread_id, $user_id)
{
	return db_hidden_threads_add(DataExchange::getDBLink(), $thread_id,
		$user_id);
}
/**
 * Отменяет скрытие нити.
 * @param thread_id mixed <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function hidden_threads_delete($thread_id, $user_id)
{
	return db_hidden_threads_delete(DataExchange::getDBLink(), $thread_id,
		$user_id);
}
?>