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

/***************************************
 * Работа со списком контроля доступа. *
 ***************************************/

/**
 * Добавляет новое правило в список контроля доступа.
 * @param group_id mixed <p>Группа.</p>
 * @param board_id mixed <p>Доска.</p>
 * @param thread_id mixed <p>Нить.</p>
 * @param post_id mixed <p>Сообщение.</p>
 * @param view mixed <p>Право на просмотр.</p>
 * @param change mixed <p>Право на изменение.</p>
 * @param moderate mixed <p>Право на модерирование.</p>
 */
function acl_add($group_id, $board_id, $thread_id, $post_id, $view, $change,
		$moderate)
{
	db_acl_add(DataExchange::getDBLink(), $group_id, $board_id, $thread_id,
		$post_id, $view, $change, $moderate);
}
/**
 * Удаляет правило из списка контроля доступа.
 * @param group_id mixed <p>Группа.</p>
 * @param board_id mixed <p>Доска.</p>
 * @param thread_id mixed <p>Нить.</p>
 * @param post_id mixed <p>Сообщение.</p>
 */
function acl_delete($group_id, $board_id, $thread_id, $post_id)
{
	db_acl_delete(DataExchange::getDBLink(), $group_id, $board_id, $thread_id,
		$post_id);
}
/**
 * Редактирует правило в списке контроля доступа.
 * @param group_id mixed <p>Группа.</p>
 * @param board_id mixed <p>Доска.</p>
 * @param thread_id mixed <p>Нить.</p>
 * @param post_id mixed <p>Сообщение.</p>
 * @param view mixed <p>Право на просмотр.</p>
 * @param change mixed <p>Право на изменение.</p>
 * @param moderate mixed <p>Право на модерирование.</p>
 */
function acl_edit($group_id, $board_id, $thread_id, $post_id, $view, $change,
	$moderate)
{
	db_acl_edit(DataExchange::getDBLink(), $group_id, $board_id, $thread_id,
		$post_id, $view, $change, $moderate);
}
/**
 * Получает список контроля доступа.
 * @return array
 * Возвращает список контроля доступа:<p>
 * 'group' - Группа.<br>
 * 'board' - Доска.<br>
 * 'thread' - Нить.<br>
 * 'post' - Сообщение.<br>
 * 'view' - Право на просмотр.<br>
 * 'change' - Право на изменение.<br>
 * 'moderate' - Право на модерирование.</p>
 */
function acl_get_all()
{
	return db_acl_get_all(DataExchange::getDBLink());
}

/*********************************************************************
 * Работа с вложениями (абстракция над конкретными типами вложений). *
 *********************************************************************/

/**
 * Получает связи сообщений с их вложениями.
 * @param posts array <p>Сообщения.</p>
 * @return array
 * Возвращает вложения:<p>
 * 'post' - Идентификатор сообщения.<br>
 * 'attachment_type' - Тип вложения.<br>
 * ... - Идентификатор, зависящий от конкретного типа вложения.</p>
 */
function posts_attachments_get_by_posts($posts)
{
	$posts_attachments = array();
	foreach($posts as $post)
	{
		foreach(db_posts_files_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_file)
			array_push($posts_attachments, $post_file);
		foreach(db_posts_images_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_image)
			array_push($posts_attachments, $post_image);
		foreach(db_posts_links_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_link)
			array_push($posts_attachments, $post_link);
		foreach(db_posts_videos_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_video)
			array_push($posts_attachments, $post_video);
	}
	return $posts_attachments;
}
/**
 * Получает вложения заданных сообщений.
 * @param posts array <p>Сообщения.</p>
 * @return array
 * Возвращает вложения:<p>
 * 'id' - Идентификатор.<br>
 * 'attachment_type' - Тип вложения.<br>
 * ... - Атрибуты, зависимые от конкретного типа вложения.</p>
 */
function attachments_get_by_posts($posts)
{
	$attachments = array();
	foreach($posts as $post)
	{
		foreach(db_files_get_by_post(DataExchange::getDBLink(), $post['id']) as $file)
			array_push($attachments, $file);
		foreach(db_images_get_by_post(DataExchange::getDBLink(), $post['id']) as $image)
			array_push($attachments, $image);
		foreach(db_links_get_by_post(DataExchange::getDBLink(), $post['id']) as $link)
			array_push($attachments, $link);
		foreach(db_videos_get_by_post(DataExchange::getDBLink(), $post['id']) as $video)
			array_push($attachments, $video);
	}
	return $attachments;
}

/**************************
 * Работа с блокировками. *
 **************************/

/**
 * Блокирует заданный диапазон IP-адресов.
 * @param range_beg int <p>Начало диапазона IP-адресов.</p>
 * @param range_end int <p>Конец диапазона IP-адресов.</p>
 * @param reason string <p>Причина блокировки.</p>
 * @param untill string <p>Время истечения блокировки.</p>
 */
function bans_add($range_beg, $range_end, $reason, $untill)
{
	db_bans_add(DataExchange::getDBLink(), $range_beg, $range_end, $reason,
		$untill);
}
/**
 * Проверяет, заблокирован ли IP-адрес. Если да, то завершает работу скрипта.
 * @param smarty SmartyKotobaSetup <p>Экземпляр шаблонизатора.</p>
 * @param ip string <p>IP-адрес.</p>
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
 * Проверяет корректность начала диапазона IP-адресов.
 * @param range_beg string <p>Начало диапазона IP-адресов.</p>
 * @return string
 * Возвращает безопасное для использования начало диапазона IP-адресов.
 */
function bans_check_range_beg($range_beg)
{
	if(($range_beg = ip2long($range_beg)) == false)
		throw new FormatException(FormatException::$messages['BANS_RANGE_BEG']);
	return $range_beg;
}
/**
 * Проверяет корректность конца диапазона IP-адресов.
 * @param range_end string <p>Конец диапазона IP-адресов.</p>
 * @return string
 * Возвращает безопасный для использования конец диапазона IP-адресов.
 */
function bans_check_range_end($range_end)
{
	if(($range_end = ip2long($range_end)) == false)
		throw new FormatException(FormatException::$messages['BANS_RANGE_END']);
	return $range_end;
}
/**
 * Проверяет корректность причины блокировки.
 * @param reason string <p>Причина бана.</p>
 * @return string
 * Возвращает безопасную для использования причину блокировки.
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
 * Проверяет корректность времени истечения блокировки.
 * @param untill string <p>Время истечения блокировки.</p>
 * @return string
 * Возвращает безопасное для использования время истечения блокировки.
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
 * Удаляет блокировку с заданным идентификатором.
 * @param id miexd <p>Идентификатор блокировки.</p>
 */
function bans_delete_by_id($id)
{
	db_bans_delete_by_id(DataExchange::getDBLink(), $id);
}
/**
 * Удаляет блокировки с заданным IP-адресом.
 * @param ip int <p>IP-адрес.</p>
 */
function bans_delete_by_ip($ip)
{
	db_bans_delete_by_ip(DataExchange::getDBLink(), $ip);
}
/**
 * Получает все блокировки.
 * @return array
 * Возвращает блокировки:<p>
 * 'id' - Идентификатор.<br>
 * 'range_beg' - Начало диапазона IP-адресов.<br>
 * 'range_end' - Конец диапазона IP-адресов.<br>
 * 'reason' - Причина блокировки.<br>
 * 'untill' - Время истечения блокировки.</p>
 */
function bans_get_all()
{
	return db_bans_get_all(DataExchange::getDBLink());
}

/*******************************************************
 * Работа со связями досок и типов загружаемых файлов. *
 *******************************************************/

/**
 * Добавляет связь доски с типом загружаемых файлов.
 * @param board mixed <p>Доска.</p>
 * @param upload_type mixed <p>Тип загружаемого файла.</p>
 */
function board_upload_types_add($board, $upload_type)
{
	db_board_upload_types_add(DataExchange::getDBLink(), $board, $upload_type);
}
/**
 * Удаляет связь доски с типом загружаемых файлов.
 * @param board mixed <p>Доска.</p>
 * @param upload_type mixed <p>Тип загружаемого файла.</p>
 */
function board_upload_types_delete($board, $upload_type)
{
	db_board_upload_types_delete(DataExchange::getDBLink(), $board,
			$upload_type);
}
/**
 * Получает все связи досок с типами загружаемых файлов.
 * @return array
 * Возвращает связи:<p>
 * 'board' - Доска.<br>
 * 'upload_type' - Тип загружаемого файла.</p>
 */
function board_upload_types_get_all()
{
	return db_board_upload_types_get_all(DataExchange::getDBLink());
}

/*********************
 * Работа с досками. *
 *********************/

/**
 * Добавляет доску.
 * @param name string <p>Имя.</p>
 * @param title string <p>Заголовок.</p>
 * @param annotation string <p>Аннотация.</p>
 * @param bump_limit mixed <p>Специфичный для доски бамплимит.</p>
 * @param force_anonymous string <p>Флаг отображения имени отправителя.</p>
 * @param default_name string <p>Имя отправителя по умолчанию.</p>
 * @param with_attachments string <p>Флаг вложений.</p>
 * @param enable_macro mixed <p>Включение интеграции с макрочаном.</p>
 * @param enable_youtube mixed <p>Включение вложения видео с ютуба.</p>
 * @param enable_captcha mixed <p>Включение капчи.</p>
 * @param same_upload string <p>Политика загрузки одинаковых файлов.</p>
 * @param popdown_handler mixed <p>Обработчик автоматического удаления нитей.</p>
 * @param category mixed <p>Категория.</p>
 */
function boards_add($name, $title, $annotation, $bump_limit, $force_anonymous,
	$default_name, $with_attachments, $enable_macro, $enable_youtube,
	$enable_captcha, $same_upload, $popdown_handler, $category)
{
	db_boards_add(DataExchange::getDBLink(), $name, $title, $annotation,
		$bump_limit, $force_anonymous, $default_name, $with_attachments,
		$enable_macro, $enable_youtube, $enable_captcha, $same_upload,
		$popdown_handler, $category);
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
 * Проверяет корректность специфичного для доски бамплимита.
 * @param $bump_limit string <p>Специфичный для доски бамплимит.</p>
 * @return string
 * Возвращает безопасный для использования специфичный для доски бамплимит.
 */
function boards_check_bump_limit($bump_limit)
{
	$length = strlen($bump_limit);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$bump_limit = RawUrlEncode($bump_limit);
		$length = strlen($bump_limit);
		if($length > $max_int_length || (ctype_digit($bump_limit) === false)
			|| $length < 1)
		{
			throw new FormatException(FormatException::$messages['BOARD_BUMP_LIMIT']);
		}
	}
	else
		throw new FormatException(FormatException::$messages['BOARD_BUMP_LIMIT']);
	return $bump_limit;
}
/**
 * Проверяет корректность имени отправителя по умолчанию.
 * @param name string <p>Имя отправителя по умолчанию.</p>
 * @return string
 * Возвращает безопасное для использования имя отправителя по умолчанию.
 */
function boards_check_default_name($name)
{
	posts_check_name_size($name);
	$name = htmlentities($name, ENT_QUOTES, Config::MB_ENCODING);
	posts_check_name_size($name);
	return $name;
}
/**
 * Проверяет корректность идентификатора доски.
 * @param id mixed <p>Идентификатор доски.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор доски.
 */
function boards_check_id($id)
{
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
 * Проверяет корректность заголовка доски.
 * @param title string <p>Заголовок доски.</p>
 * @return string
 * Возвращает безопасный для использования заголовок доски.
 */
function boards_check_title($title)
{
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
 * Удаляет заданную доску.
 * @param id mixed <p>Идентификатор доски.</p>
 */
function boards_delete($id)
{
	db_boards_delete(DataExchange::getDBLink(), $id);
}
/**
 * Редактирует доску.
 * @param id mixed <p>Идентификатор.</p>
 * @param title string <p>Заголовок.</p>
 * @param annotation string <p>Аннотация.</p>
 * @param bump_limit mixed <p>Специфичный для доски бамплимит.</p>
 * @param force_anonymous string <p>Флаг отображения имени отправителя.</p>
 * @param default_name string <p>Имя отправителя по умолчанию.</p>
 * @param with_attachments string <p>Флаг вложений.</p>
 * @param enable_macro mixed <p>Включение интеграции с макрочаном.</p>
 * @param enable_youtube mixed <p>Включение вложения видео с ютуба.</p>
 * @param enable_captcha mixed <p>Включение капчи.</p>
 * @param same_upload string <p>Политика загрузки одинаковых файлов.</p>
 * @param popdown_handler mixed <p>Обработчик автоматического удаления нитей.</p>
 * @param category mixed <p>Категория.</p>
 */
function boards_edit($id, $title, $annotation, $bump_limit, $force_anonymous,
	$default_name, $with_attachments, $enable_macro, $enable_youtube,
	$enable_captcha, $same_upload, $popdown_handler, $category)
{
	db_boards_edit(DataExchange::getDBLink(), $id, $title, $annotation,
		$bump_limit, $force_anonymous, $default_name, $with_attachments,
		$enable_macro, $enable_youtube, $enable_captcha, $same_upload,
		$popdown_handler, $category);
}
/**
 * Получает все доски.
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'annotation' - аннотация.<br>
 * 'bump_limit' - специфичный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имени отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_attachments' - флаг вложений.<br>
 * 'enable_macro' - включение интеграции с макрочаном.<br>
 * 'enable_youtube' - включение вложения видео с ютуба.<br>
 * 'enable_captcha' - включение капчи.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_all()
{
	return db_boards_get_all(DataExchange::getDBLink());
}
/**
 * Получает заданную доску.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @return array
 * Возвращает доску:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'annotation' - аннотация.<br>
 * 'bump_limit' - специфичный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имени отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_attachments' - флаг вложений.<br>
 * 'enable_macro' - включение интеграции с макрочаном.<br>
 * 'enable_youtube' - включение вложения видео с ютуба.<br>
 * 'enable_captcha' - включение капчи.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function boards_get_by_id($board_id)
{
	return db_boards_get_by_id(DataExchange::getDBLink(), $board_id);
}
/**
 * Получает заданную доску.
 * @param board_name string <p>Имя доски.</p>
 * @return array
 * Возвращает доску:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.<br>
 * 'title' - Заголовок.<br>
 * 'annotation' - Аннотация.<br>
 * 'bump_limit' - Специфичный для доски бамплимит.<br>
 * 'force_anonymous' - Флаг отображения имени отправителя.<br>
 * 'default_name' - Имя отправителя по умолчанию.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'enable_macro' - Включение интеграции с макрочаном.<br>
 * 'enable_youtube' - Включение вложения видео с ютуба.<br>
 * 'enable_captcha' - Включение капчи.<br>
 * 'same_upload' - Политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - Обработчик автоматического удаления нитей.<br>
 * 'category' - Категория.</p>
 */
function boards_get_by_name($board_name)
{
	return db_boards_get_by_name(DataExchange::getDBLink(), $board_name);
}
/**
 * Получает доски, доступные для изменения заданному пользователю.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.<br>
 * 'title' - Заголовок.<br>
 * 'annotation' - Аннотация.<br>
 * 'bump_limit' - Специфичный для доски бамплимит.<br>
 * 'force_anonymous' - Флаг отображения имени отправителя.<br>
 * 'default_name' - Имя отправителя по умолчанию.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'enable_macro' - Включение интеграции с макрочаном.<br>
 * 'enable_youtube' - Включение вложения видео с ютуба.<br>
 * 'enable_captcha' - Включение капчи.<br>
 * 'same_upload' - Политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - Обработчик автоматического удаления нитей.<br>
 * 'category' - Категория.<br>
 * 'category_name' - Имя категории.</p>
 */
function boards_get_changeable($user_id)
{
	return db_boards_get_changeable(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает заданную доску, доступную для редактирования заданному
 * пользователю.
 * @param board_id string <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доску:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.<br>
 * 'title' - Заголовок.<br>
 * 'annotation' - Аннотация.<br>
 * 'bump_limit' - Специфичный для доски бамплимит.<br>
 * 'force_anonymous' - Флаг отображения имени отправителя.<br>
 * 'default_name' - Имя отправителя по умолчанию.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'enable_macro' - Включение интеграции с макрочаном.<br>
 * 'enable_youtube' - Включение вложения видео с ютуба.<br>
 * 'enable_captcha' - Включение капчи.<br>
 * 'same_upload' - Политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - Обработчик автоматического удаления нитей.<br>
 * 'category' - Категория.<br>
 * 'category_name' - Имя категории.</p>
 */
function boards_get_changeable_by_id($board_id, $user_id)
{
	return db_boards_get_changeable_by_id(DataExchange::getDBLink(), $board_id,
		$user_id);
}
/**
 * Получает заданную доску, доступную для редактирования заданному
 * пользователю.
 * @param board_name string <p>Имя доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доску:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.<br>
 * 'title' - Заголовок.<br>
 * 'annotation' - Аннотация.<br>
 * 'bump_limit' - Специфичный для доски бамплимит.<br>
 * 'force_anonymous' - Флаг отображения имени отправителя.<br>
 * 'default_name' - Имя отправителя по умолчанию.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'enable_macro' - Включение интеграции с макрочаном.<br>
 * 'enable_youtube' - Включение вложения видео с ютуба.<br>
 * 'enable_captcha' - Включение капчи.<br>
 * 'same_upload' - Политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - Обработчик автоматического удаления нитей.<br>
 * 'category' - Категория.<br>
 * 'category_name' - Имя категории.</p>
 */
function boards_get_changeable_by_name($board_name, $user_id)
{
	return db_boards_get_changeable_by_name(DataExchange::getDBLink(),
		$board_name, $user_id);
}
/**
 * Получает доски, доступные для модерирования заданному пользователю.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.<br>
 * 'title' - Заголовок.<br>
 * 'annotation' - Аннотация.<br>
 * 'bump_limit' - Специфичный для доски бамплимит.<br>
 * 'force_anonymous' - Флаг отображения имени отправителя.<br>
 * 'default_name' - Имя отправителя по умолчанию.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'enable_macro' - Включение интеграции с макрочаном.<br>
 * 'enable_youtube' - Включение вложения видео с ютуба.<br>
 * 'enable_captcha' - Включение капчи.<br>
 * 'same_upload' - Политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - Обработчик автоматического удаления нитей.<br>
 * 'category' - Категория.</p>
 */
function boards_get_moderatable($user_id)
{
	return db_boards_get_moderatable(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает доски, доступные для просмотра заданному пользователю.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'annotation' - аннотация.<br>
 * 'bump_limit' - специфичный для доски бамплимит.<br>
 * 'force_anonymous' - флаг отображения имени отправителя.<br>
 * 'default_name' - имя отправителя по умолчанию.<br>
 * 'with_attachments' - флаг вложений.<br>
 * 'enable_macro' - включение интеграции с макрочаном.<br>
 * 'enable_youtube' - включение вложения видео с ютуба.<br>
 * 'enable_captcha' - включение капчи.<br>
 * 'same_upload' - политика загрузки одинаковых файлов.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.<br>
 * 'category_name' - Имя категории.</p>
 */
function boards_get_visible($user_id)
{
	return db_boards_get_visible(DataExchange::getDBLink(), $user_id);
}

/*************************
 * Работа с категориями. *
 *************************/

/**
 * Добавляет новую категорию с заданным именем.
 * @param name string <p>Имя.</p>
 */
function categories_add($name)
{
	db_categories_add(DataExchange::getDBLink(), $name);
}
/**
 * Проверяет корректность идентификатора категории.
 * @param id mixed <p>Идентификатор.</p>
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
 * Проверяет корректность имени категории.
 * @param name string <p>Имя.</p>
 * @return string
 * Возвращает безопасное для использования имя категории.
 */
function categories_check_name($name)
{
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
 * Удаляет заданную категорию.
 * @param id mixed <p>Идентификатор.</p>
 */
function categories_delete($id)
{
	db_categories_delete(DataExchange::getDBLink(), $id);
}
/**
 * Получает все категории.
 * @return array
 * Возвращает категории:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.</p>
 */
function categories_get_all()
{
	return db_categories_get_all(DataExchange::getDBLink());
}

/**********************
 * Работа с группами. *
 **********************/

/**
 * Добавляет группу с заданным именем.
 * @param name string <p>Имя группы.</p>
 * @return string
 * Возвращает идентификатор добавленной группы.
 */
function groups_add($name)
{
	db_groups_add(DataExchange::getDBLink(), $name);
}
/**
 * Проверяет корректность идентификатора группы.
 * @param id mixed <p>Идентификатор группы.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор группы.
 */
function groups_check_id($id)
{
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
 * Проверяет корректность имени группы.
 * @param name string <p>Имя группы.</p>
 * Возвращает безопасное для использования имя группы.
 * @return string
 */
function groups_check_name($name)
{
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
 * Удаляет заданные группы, а так же всех пользователей, которые входят в эти
 * группы и все правила в ACL, распространяющиеся на эти группы.
 * @param groups array <p>Группы.</p>
 */
function groups_delete($groups)
{
	db_groups_delete(DataExchange::getDBLink(), $groups);
}
/**
 * Получает все группы.
 * @return array
 * Возвращает группы:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.</p>
 */
function groups_get_all()
{
	return db_groups_get_all(DataExchange::getDBLink());
}

/******************************
 * Работа со скрытыми нитями. *
 ******************************/

/**
 * Скрывает нить.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function hidden_threads_add($thread_id, $user_id)
{
	return db_hidden_threads_add(DataExchange::getDBLink(), $thread_id,
		$user_id);
}
/**
 * Отменяет скрытие нити.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function hidden_threads_delete($thread_id, $user_id)
{
	return db_hidden_threads_delete(DataExchange::getDBLink(), $thread_id,
		$user_id);
}
/**
 * Возвращает отфильтрованные скрытые нити на заданных досках.
 * @param boards array <p>Доски.</p>
 * @param filter object <p>Фильтр (лямбда).</p>
 * @return array
 * Возвращает скрытые нити:<p>
 * 'user' - Пользователь.<br>
 * 'thread' - Нить.<br>
 * 'thread_number' - Номер оригинального сообщения.</p>
 */
function hidden_threads_get_filtred_by_boards($boards, $filter)
{
	$threads = db_hidden_threads_get_by_boards(DataExchange::getDBLink(),
		$boards);
	$filtred_threads = array();
	$filter_args = array();
	$filter_argn = 0;
	$n = func_num_args();
	for($i = 2; $i < $n; $i++)	// Пропустим первые два аргумента функции.
		$filter_args[$filter_argn++] = func_get_arg($i);
	foreach($threads as $t)
	{
		$filter_args[$filter_argn] = $t;
		if(call_user_func_array($filter, $filter_args))
			array_push($filtred_threads, $t);
	}
	return $filtred_threads;
}
/**
 * Получает доступную для просмотра скрытую нить и количество сообщений в ней.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_num mixed <p>Номер нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Доска.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'archived' - Флаг архивирования.<br>
 * 'sage' - Флаг поднятия нити при ответе.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'posts_count' - Число доступных для просмотра сообщений.</p>
 */
function hidden_threads_get_visible($board_id, $thread_num, $user_id)
{
	return db_hidden_threads_get_visible(DataExchange::getDBLink(), $board_id,
			$thread_num, $user_id);
}

/**************************************
 * Работа с вложенными изображениями. *
 **************************************/

/**
 * Получает одинаковые вложенные изображения на заданной доски.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param image_hash string <p>Хеш вложенного изображения.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает изображения:<p>
 * 'id' - Идентификатор.
 * 'hash' - Хеш.<br>
 * 'name' - Имя.<br>
 * 'widht' - Ширина.<br>
 * 'height' - Высота.<br>
 * 'size' - Размер в байтах.<br>
 * 'thumbnail' - Уменьшенная копия.<br>
 * 'thumbnail_w' - Ширина уменьшенной копии.<br>
 * 'thumbnail_h' - Высота уменьшенной копии.</p>
 * 'post_number' - Номер сообщения, в которое вложено изображение.<br>
 * 'thread_number' - Номер нити с сообщением, в которое вложено	изображение.<br>
 * 'view' - Право на просмотр сообщения, в которое вложено изображение.</p>
 */
function images_get_same($board_id, $image_hash, $user_id)
{
	return db_images_get_same(DataExchange::getDBLink(), $board_id, $image_hash,
		$user_id);
}

/*********************
 * Работа с языками. *
 *********************/

/**
 * Добавляет язык.
 * @param code string <p>ISO_639-2 код языка.</p>
 */
function languages_add($code)
{
	db_languages_add(DataExchange::getDBLink(), $code);
}
/**
 * Проверяет корректность ISO_639-2 кода языка.
 * @param code string <p>ISO_639-2 код языка.</p>
 * @return string
 * Возвращает безопасный для использования ISO_639-2 код языка.
 */
function languages_check_code($code)
{
	$length = strlen($code);
	if($length == 3)
	{
		$code = RawUrlEncode($code);
		$length = strlen($code);
		if($length != 3 || (strpos($code, '%') !== false))
			throw new FormatException(FormatException::$messages['LANGUAGE_CODE']);
	}
	else
		throw new FormatException(FormatException::$messages['LANGUAGE_CODE']);
	return $code;
}
/**
 * Проверяет корректность идентификатора языка.
 * @param id mixed <p>Идентификатор языка.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор языка.
 */
function languages_check_id($id)
{
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
 * Удаляет язык с заданным идентификатором.
 * @param id mixed <p>Идентификатор языка.</p>
 */
function languages_delete($id)
{
	db_languages_delete(DataExchange::getDBLink(), $id);
}
/**
 * Получает языки.
 * @return array
 * Возвращает языки:<p>
 * 'id' - Идентификатор.<br>
 * 'code' - Код ISO_639-2.</p>
 */
function languages_get_all()
{
	return db_languages_get_all(DataExchange::getDBLink());
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
 * Получает настройки ползователя по заданному ключевому слову.
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @return array
 * Возвращает настройки:<p>
 * 'id' - идентификатор пользователя.<br>
 * 'posts_per_thread' - количество последних сообщений в нити при просмотре доски.<br>
 * 'threads_per_page' - количество нитей на странице при просмотре доски.<br>
 * 'lines_per_post' - количество строк в урезанном сообщении при просмотре доски.<br>
 * 'language' - язык.<br>
 * 'stylesheet' - стиль оформления.<br>
 * 'password' - пароль для удаления сообщений.<br>
 * 'goto' - перенаправление при постинге.<br>
 * 'groups' - группы, в которые входит пользователь.</p>
 */
function users_get_by_keyword($keyword)
{
	return db_users_get_by_keyword(DataExchange::getDBLink(), $keyword);
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
	{
		return $goto;
	}
	else
	{
		throw new FormatException(FormatException::$messages['GOTO']);
	}
}
/**
 * Редактирует настройки пользователя с заданным ключевым словом или добавляет
 * нового.
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @param threads_per_page mixed <p>Количество нитей на странице предпросмотра доски.</p>
 * @param posts_per_thread mixed <p>Количество сообщений в предпросмотре треда.</p>
 * @param lines_per_post mixed <p>Максимальное количество строк в предпросмотре сообщения.</p>
 * @param stylesheet mixed <p>Стиль оформления.</p>
 * @param language mixed <p>Язык.</p>
 * @param password mixed <p>Пароль для удаления сообщений.</p>
 * @param goto string <p>Перенаправление при постинге.</p>
 */
function users_edit_bykeyword($keyword, $threads_per_page, $posts_per_thread,
	$lines_per_post, $stylesheet, $language, $password, $goto)
{
	db_users_edit_bykeyword(DataExchange::getDBLink(), $keyword,
		$threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet,
		$language, $password, $goto);
}
/**
 * Устанавливает пароль для удаления сообщений заданному пользователю.
 * @param id mixed <p>Идентификатор пользователя.</p>
 * @param password mixed <p>Пароль для удаления сообщений.</p>
 */
function users_set_password($id, $password)
{
	db_users_set_password(DataExchange::getDBLink(), $id, $password);
}
/**
 * Получает всех пользователей.
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

/*************************
 * Работа с сообщениями. *
 *************************/

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
 * Добавляет текст в конец текста заданного сообщения.
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param text string <p>Текст.</p>
 */
function posts_add_text_by_id($id, $text)
{
	db_posts_add_text_by_id(DataExchange::getDBLink(), $id, $text);
}
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
 * Проверяет, удовлетворяет ли имя отправителя ограничениям по размеру.
 * @param name string <p>Имя отправителя.</p>
 */
function posts_check_name_size($name)
{
	if(strlen($name) > Config::MAX_THEME_LENGTH)
		throw new LimitException(LimitException::$messages['MAX_NAME_LENGTH']);
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
 * Проверяет корректность пароля для удаления сообщений.
 * @param password string <p>Пароль.</p>
 * @return string
 * Возвращает безопасный для использования пароль для удаления сообщений.
 */
function posts_check_password($password)
{
	$length = strlen($password);
	if($length <= 12 && $length >= 1)
	{
		$password = RawUrlEncode($password);
		$length = strlen($password);
		if($length > 12 || (strpos($password, '%') !== false) || $length < 1)
		{
			throw new FormatException(FormatException::$messages['POST_PASSWORD']);
		}
	}
	else
	{
		throw new FormatException(FormatException::$messages['POST_PASSWORD']);
	}
	return $password;
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
 * Проверяет корректность текста.
 * @param text string <p>Текст сообщения.</p>
 */
function posts_check_text($text)
{
	if(!check_utf8($text))
		throw new CommonException(CommonException::$messages['TEXT_UNICODE']);
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
 * Удаляет сообщение с заданным идентификатором и все сообщения с ip адреса
 * отправителя, оставленные с заданного момента.
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param date_time mixed <p>Момент времени.</p>
 */
function posts_delete_last($id, $date_time)
{
	db_posts_delete_last(DataExchange::getDBLink(), $id, $date_time);
}
/**
 * Удаляет сообщения, помеченные на удаление.
 */
function posts_delete_marked()
{
	db_posts_delete_marked(DataExchange::getDBLink());
}
/**
 * Получает все сообщения.
 * @return array
 * Возвращает сообщеня:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Доска.<br>
 * 'board_name' - Имя доски.<br>
 * 'thread' - Нить.<br>
 * 'thread_number' - Номер нити.<br>
 * 'number' - Номер.<br>
 * 'password' - Пароль.<br>
 * 'name' - Имя отправителя.<br>
 * 'tripcode' - Трипкод.<br>
 * 'ip' - IP-адрес отправителя.<br>
 * 'subject' - Тема.<br>
 * 'date_time' - Время сохранения.<br>
 * 'text' - Текст.<br>
 * 'sage' - Флаг поднятия нити.</p>
 */
function posts_get_all()
{
	return db_posts_get_all(DataExchange::getDBLink());
}
/**
 * Возвращает сообщения с заданных досок.
 * @param boards array <p>Доски.</p>
 * @return array
 * Возвращает сообщеня:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
 * 'thread_number' - номер нити.<br>
 * 'board' - идентификатор доски.<br>
 * 'board_name' - имя доски.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'tripcode' - трипкод.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function posts_get_by_boards($boards)
{
	return db_posts_get_by_boards(DataExchange::getDBLink(), $boards);
}
/**
 * Получает сообщения заданной нити.
 * @param thread_id array <p>Идентификатор нити.</p>
 * @return array
 * Возвращает сообщения:<p>
 * 'id' - Идентификатор.<br>
 * 'thread' - Нить.<br>
 * 'number' - Номер.<br>
 * 'password' - Пароль.<br>
 * 'name' - Имя отправителя.<br>
 * 'tripcode' - Трипкод.<br>
 * 'ip' - IP-адрес отправителя.<br>
 * 'subject' - Тема.<br>
 * 'date_time' - Время сохранения.<br>
 * 'text' - Текст.<br>
 * 'sage' - Флаг поднятия нити.</p>
 */
function posts_get_by_thread($thread_id)
{
	return db_posts_get_by_thread(DataExchange::getDBLink(), $thread_id);
}
/**
 * Получает отфильтрованные сообщения с заданных досок.
 * @param boards array <p>Доски.</p>
 * @param filter object <p>Фильтр (лямбда).</p>
 * @return array
 * Возвращает сообщеня:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
 * 'thread_number' - номер нити.<br>
 * 'board' - идентификатор доски.<br>
 * 'board_name' - имя доски.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'tripcode' - трипкод.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function posts_get_filtred_by_boards($boards, $filter)
{
	$posts = db_posts_get_by_boards(DataExchange::getDBLink(), $boards);
	$filtred_posts = array();
	$filter_args = array();
	$filter_argn = 0;
	$n = func_num_args();
	for($i = 2; $i < $n; $i++)	// Пропустим первые два аргумента фукнции.
		$filter_args[$filter_argn++] = func_get_arg($i);
	foreach($posts as $post)
	{
		$filter_args[$filter_argn] = $post;
		if(call_user_func_array($filter, $filter_args))
			array_push($filtred_posts, $post);
	}
	return $filtred_posts;
}
/**
 * Получает сообщение, доступное для чтения заданному пользоватею, по
 * идентификатору.
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает сообщение:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
 * 'board' - идентификатор доски.<br>
 * 'board_name' - имя доски.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function posts_get_visible_by_id($post_id, $user_id)
{
	return db_posts_get_visible_by_id(DataExchange::getDBLink(), $post_id,
		$user_id);
}
/**
 * Получает сообщение по заданному номеру.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param post_number mixed <p>Номер сообщения.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает сообщение:<p>
 * 'id' - Идентификатор.<br>
 * 'thread' - Нить.<br>
 * 'number' - Номер.<br>
 * 'password' - Пароль.<br>
 * 'name' - Имя отправителя.<br>
 * 'tripcode' - Трипкод.<br>
 * 'ip' - IP-адрес отправителя.<br>
 * 'subject' - Тема.<br>
 * 'date_time' - Время сохранения.<br>
 * 'text' - Текст.<br>
 * 'sage' - Флаг поднятия нити.</p>
 */
function posts_get_visible_by_number($board_id, $post_number, $user_id)
{
	return db_posts_get_visible_by_number(DataExchange::getDBLink(), $board_id,
			$post_number, $user_id);
}
/**
 * Для каждой нити получает отфильтрованные сообщения, доступные для чтения
 * заданному пользователю.
 * @param threads array <p>Нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param filter mixed <p>Фильтр (лямбда).</p>
 * @return array
 * Возвращает сообщения:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - нить.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'tripcode' - трипкод.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function posts_get_visible_filtred_by_threads($threads, $user_id, $filter)
{

	$numargs = func_num_args();
	$args = array();			// Аргументы для лямбды.
	for($i = 3; $i < $numargs; $i++)	// Пропустим первые 3 аргумента фукнции.
		array_push($args, func_get_arg($i));
	return db_posts_get_visible_filtred_by_threads(DataExchange::getDBLink(), $threads,
		$user_id, $filter, $args);
}
/**
 * Очищает и размечает текст сообщения заданной доски.
 * @param text string <p>Текст сообщения.</p>
 * @param board array <p>Доска.</p>
 */
function posts_prepare_text(&$text, $board)
{
	purify_ascii($text);
	kotoba_mark($text, $board);
	$text = str_replace("</blockquote>\n", '</blockquote>', $text);
	$text = str_replace("\n<blockquote", '<blockquote', $text);
	$text = preg_replace('/\n{3,}/', '\n', $text);
	$text = preg_replace('/\n/', '<br>', $text);
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

/********************
 * Работа с нитями. *
 ********************/

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
 * Проверяет корректность специфичного для нити бамплимита.
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @return string
 * Возвращает безопасный для использования специфичный для нити бамплимит.
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
 * Редактирует номер оригинального сообщения нити.
 * @param id mixed <p>Идентификатор нити.</p>
 * @param original_post mixed <p>Номер оригинального сообщения нити.</p>
 */
function threads_edit_original_post($id, $original_post)
{
	db_threads_edit_original_post(DataExchange::getDBLink(), $id,
			$original_post);
}
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
 * Получает нити, помеченные для архивирования.
 * @return array
 * Возвращает нити:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Доска.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити при ответе.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.</p>
 */
function threads_get_archived()
{
	return db_threads_get_archived(DataExchange::getDBLink());
}
/**
 * Получает нить, доступную для редактирования заданному пользователю.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - идентификатор.<br>
 * 'board' - доска.<br>
 * 'original_post' - номер оригинального сообщения.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sage' - флаг поднятия нити.<br>
 * 'with_attachments' - флаг вложений.<br>
 * 'archived' - флаг архивирования.</p>
 */
function threads_get_changeable_by_id($thread_id, $user_id)
{
	return db_threads_get_changeable_by_id(DataExchange::getDBLink(),
		$thread_id, $user_id);
}
/**
 * Получает нити, доступные для модерирования заданному пользователю.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нити:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Доска.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити при ответе.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.</p>
 */
function threads_get_moderatable($user_id)
{
	return db_threads_get_moderatable(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает заданную нить, если она доступна для модерирования.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return mixed
 * Возвращает нить:<p>
 * 'id' - идентификатор.</p>
 * Или null, если заданная нить не доступна для модерирования.
 */
function threads_get_moderatable_by_id($thread_id, $user_id)
{
	return db_threads_get_moderatable_by_id(DataExchange::getDBLink(),
		$thread_id, $user_id);
}
/**
 * Получает с заданной страницы доски доступные для просмотра пользователю нити
 * и количество сообщений в них.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param page mixed <p>Номер страницы.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param threads_per_page mixed <p>Количество нитей на странице.</p>
 * @return array
 * Возвращает нити:<p>
 * 'id' - идентификатор.<br>
 * 'original_post' - номер оригинального сообщения.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sage' - флаг поднятия нити при ответе.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'with_attachments' - флаг вложений.<br>
 * 'posts_count' - число доступных для просмотра сообщений.</p>
 */
function threads_get_visible_by_board($board_id, $page, $user_id,
	$threads_per_page)
{
	return db_threads_get_visible_by_board(DataExchange::getDBLink(), $board_id,
		$page, $user_id, $threads_per_page);
}
/**
 * Получает доступную для просмотра нить с заданной доски.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_num mixed <p>Номер нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - номер оригинального сообщения.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'archived' - флаг архивирования.<br>
 * 'sage' - флаг поднятия нити.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'with_attachments' - флаг вложений.<br>
 * 'posts_count' - число доступных для просмотра сообщений в нити.</p>
 */
function threads_get_visible_by_id($board_id, $thread_num, $user_id)
{
	return db_threads_get_visible_by_id(DataExchange::getDBLink(), $board_id,
		$thread_num, $user_id);
}
/**
 * Вычисляет количество нитей, доступных для просмотра заданному пользователю
 * на заданной доске.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @return string
 * Возвращает количество нитей.
 */
function threads_get_visible_count($user_id, $board_id)
{
	return db_threads_get_visible_count(DataExchange::getDBLink(), $user_id,
		$board_id);
}

/***************************************************
 * Работа с закреплениями загрузок за сообщениями. *
 ***************************************************/

/**
 * Получает закрепления загрузок за заданными сообщениями.
 * @param posts array <p>Сообщения.</p>
 * @return array
 * Возвращает закрепления:<p>
 * 'post' - идентификатор сообщения.<br>
 * 'upload' - идентификатор загрузки.</p>
 */
function posts_uploads_get_by_posts($posts)
{
	return db_posts_uploads_get_by_posts(DataExchange::getDBLink(), $posts);
}
/**
 * Связывает сообщение с информацией о загрузке.
 * @param post_id mixed <p>идентификатор сообщения.</p>
 * @param upload_id mixed <p>идентификатор записи с информацией о загрузке.</p>
 */
function posts_uploads_add($post_id, $upload_id)
{
	db_posts_uploads_add(DataExchange::getDBLink(), $post_id, $upload_id);
}
/**
 * Удаляет закрепления загрузок за заданным сообщением.
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 */
function posts_uploads_delete_by_post($post_id)
{
	db_posts_uploads_delete_by_post(DataExchange::getDBLink(), $post_id);
}

/************************
 * Работа с загрузками. *
 ************************/

/**
 * Сохраняет данные о загрузке.
 * @param hash string <p>Хеш файла.</p>
 * @param is_image mixed <p>Флаг изображения.</p>
 * @param upload_type mixed <p>Тип загрузки.</p>
 * @param file string <p>Имя файла, URL, код видео.</p>
 * @param image_w mixed <p>Ширина изображения.</p>
 * @param image_h mixed <p>Высота изображения.</p>
 * @param size string <p>Размер файла в байтах.</p>
 * @param thumbnail string <p>Имя уменьшенной копии.</p>
 * @param thumbnail_w mixed <p>Ширина уменьшенной копии.</p>
 * @param thumbnail_h mixed <p>Высота уменьшенной копии.</p>
 * @return string
 * Возвращает идентификатор загрузки.
 */
function uploads_add($hash, $is_image, $upload_type, $file, $image_w, $image_h,
	$size, $thumbnail, $thumbnail_w, $thumbnail_h)
{
	return db_uploads_add(DataExchange::getDBLink(), $hash, $is_image,
		$upload_type, $file, $image_w, $image_h, $size, $thumbnail,
		$thumbnail_w, $thumbnail_h);
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
 * Удаляет заданную загрузку.
 * @param id string <p>Идентификатор загрузки.</p>
 */
function uploads_delete_by_id($id)
{
	db_uploads_delete_by_id(DataExchange::getDBLink(), $id);
}
/**
 * Получает загрузки для заданных сообщений.
 * @param posts array <p>Массив сообщений.</p>
 * @return array
 * Возвращает загрузки:<p>
 * 'id' - идентификатор.<br>
 * 'hash' - хеш файла.<br>
 * 'is_image' - флаг картинки.<br>
 * 'upload_type' - тип загрузки.<br>
 * 'file' - имя файла, URL, код видео.<br>
 * 'image_w' - ширина изображения.<br>
 * 'image_h' - высота изображения.<br>
 * 'size' - размер файла в байтах.<br>
 * 'thumbnail' - имя уменьшенной копии.<br>
 * 'thumbnail_w' - ширина уменьшенной копии.<br>
 * 'thumbnail_h' - высота уменьшенной копии.</p>
 */
function uploads_get_by_posts($posts)
{
	return db_uploads_get_by_posts(DataExchange::getDBLink(), $posts);
}
/**
 * Получает информацию о висячих загрузках (не связанных с сообщениями).
 * @return array
 * Возвращает информацию о висячих загрузках:<p>
 * 'id' - идентификатор.<br>
 * 'hash' - хеш файла.<br>
 * 'is_image' - флаг картинки.<br>
 * 'upload_type' - тип загрузки.<br>
 * 'file' - имя файла, ссылка или код видео.<br>
 * 'image_w' - ширина изображения.<br>
 * 'image_h' - высота изображения.<br>
 * 'size' - размер файла в байтах.<br>
 * 'thumbnail' - имя уменьшенной копии.<br>
 * 'thumbnail_w' - ширина уменьшенной копии.<br>
 * 'thumbnail_h' - высота уменьшенной копии.</p>
 */
function uploads_get_dangling()
{
	return db_uploads_get_dangling(DataExchange::getDBLink());
}
/**
 * Получает одинаковые загрузки для заданной доски.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param hash string <p>Хеш файла.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает загрузки:<p>
 * 'id' - идентификатор.<br>
 * 'hash' - хеш файла.<br>
 * 'is_image' - флаг картинки.<br>
 * 'upload_type' - тип загрузки.<br>
 * 'file' - имя файла, URL, код видео.<br>
 * 'image_w' - ширина изображения.<br>
 * 'image_h' - высота изображения.<br>
 * 'size' - размер файла в байтах.<br>
 * 'thumbnail' - имя уменьшенной копии.<br>
 * 'thumbnail_w' - ширина уменьшенной копии.<br>
 * 'thumbnail_h' - высота уменьшенной копии.</p>
 * 'post_number' - номер сообщения, за которым закреплена загрузка.<br>
 * 'thread_number' - номер нити с сообщением, за которым закреплена
 *		загрузка.<br>
 * 'view' - видно ли сообщение пользователю.</p>
 */
function uploads_get_same($board_id, $hash, $user_id)
{
	return db_uploads_get_same(DataExchange::getDBLink(), $board_id, $hash,
		$user_id);
}
?>