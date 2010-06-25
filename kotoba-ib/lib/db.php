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

// Конечный скрипт должен загрузить конфигурацию!
if(!class_exists("Config"))
    throw new Exception("User-end script MUST load a configuraion!");
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/mysql.php';
// TODO Может быть ещё что-то?

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
 * @param string name $Имя новой доски.
 * @return boolean
 * Возвращает true в случае успешного создания директорий и false в противном
 * случае.
 */
function create_directories($name) { // Java CC
    $base = Config::ABS_PATH . "/$name";
    if(@mkdir ($base)) { // Hide warning when directory exists.
        chmod ($base, 0777);
        foreach (array("arch", "img", "thumb") as $dir) {
            $subdir = "$base/$dir";
            if (@mkdir($subdir)) { // Hide warning when directory exists.
                chmod($subdir, 0777);
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    return true;
}
/**
 * Создаёт необходимые директории при добавлении нового языка.
 * @param string $code ISO_639-2 код языка.
 */
function create_language_directories($code) { // Java CC
    $dir = Config::ABS_PATH . "/smarty/kotoba/templates/locale/$code";
    @mkdir($dir); // Hide warning when directory exists.
    chmod($dir, 0777);
    $dir = Config::ABS_PATH . "/smarty/kotoba/templates_c/locale/$code";
    @mkdir($dir); // Hide warning when directory exists.
    chmod($dir, 0777);
    $dir = Config::ABS_PATH . "/locale/$code";
    @mkdir($dir); // Hide warning when directory exists.
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
 * @param array $posts Сообщения.
 * @return array
 * Возвращает связи:<p>
 * 'post' - Идентификатор сообщения.<br>
 * ... - Идентификатор, зависящий от конкретного типа вложения.<br>
 * 'deleted' - Флаг удаления.<br>
 * 'attachment_type' - Тип вложения.</p>
 */
function posts_attachments_get_by_posts($posts) { // Java CC
    $posts_attachments = array();
    foreach ($posts as $post) {
        foreach (db_posts_files_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_file) {
            array_push($posts_attachments, $post_file);
        }
        foreach (db_posts_images_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_image) {
            array_push($posts_attachments, $post_image);
        }
        foreach (db_posts_links_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_link) {
            array_push($posts_attachments, $post_link);
        }
        foreach (db_posts_videos_get_by_post(DataExchange::getDBLink(), $post['id']) as $post_video) {
            array_push($posts_attachments, $post_video);
        }
    }
    return $posts_attachments;
}
/**
 * Получает вложения заданных сообщений.
 * @param array $posts Сообщения.
 * @return array
 * Возвращает вложения:<p>
 * 'id' - Идентификатор.<br>
 * ... - Атрибуты, зависящие от конкретного типа вложения.<br>
 * 'attachment_type' - Тип вложения.</p>
 */
function attachments_get_by_posts($posts) { // Java CC
    $attachments = array();
    foreach ($posts as $post) {
        foreach (db_files_get_by_post(DataExchange::getDBLink(), $post['id']) as $file) {
            array_push($attachments, $file);
        }
        foreach (db_images_get_by_post(DataExchange::getDBLink(), $post['id']) as $image) {
            array_push($attachments, $image);
        }
        foreach (db_links_get_by_post(DataExchange::getDBLink(), $post['id']) as $link) {
            array_push($attachments, $link);
        }
        foreach (db_videos_get_by_post(DataExchange::getDBLink(), $post['id']) as $video) {
            array_push($attachments, $video);
        }
    }
    return $attachments;
}
/**
 * Получает одинаковые вложения на заданной доске.
 * @param int $board_id Идентификатор доски.
 * @param int $user_id Идентификатор пользователя.
 * @param string $hash Хеш файла.
 * @return array
 * Возвращает вложения:<p>
 * 'id' - Идентификатор.<br>
 * ... - Атрибуты, зависимые от конкретного типа вложения.<br>
 * 'attachment_type' - Тип вложения.<br>
 * 'visible' - Право на просмотр сообщения, в которое вложено изображение.</p>
 */
function attachments_get_same($board_id, $user_id, $hash) { // Java CC
	$attachments = array();

    $files = db_files_get_same(DataExchange::getDBLink(), $board_id, $user_id,
        $hash);
    $images = db_images_get_same(DataExchange::getDBLink(), $board_id, $user_id,
        $hash);

    foreach ($files as $file) {
        array_push($attachments, $file);
    }
    foreach ($images as $image) {
        array_push($attachments, $image);
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
 * @param int $ip IP-адрес.
 * @return boolean|array
 * Возвращает false, если адрес не заблокирован и массив, если заблокирован:<p>
 * 'range_beg' - Начало диапазона IP-адресов.<br>
 * 'range_end' - Конец диапазона IP-адресов.<br>
 * 'reason' - Причина блокировки.<br>
 * 'untill' - Время истечения блокировки.</p>
 */
function bans_check($ip) { // Java CC
    return db_bans_check(DataExchange::getDBLink(), $ip);
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

/**************************
 * Работа с вордфильтром. *
 **************************/

/**
 * Добавляет слово.
 * @param board_id int <p>Идентификатор доски.</p>
 * @param word mixed <p>Слово.</p>
 * @param replace string <p>Слово-замена.</p>
 */
function words_add($board_id, $word, $replace)
{
	db_words_add(DataExchange::getDBLink(), $board_id, $word, $replace);
}
/**
 * Удаляет заданное слово.
 * @param id mixed <p>Идентификатор доски.</p>
 */
function words_delete($id)
{
	db_words_delete(DataExchange::getDBLink(), $id);
}
/**
 * Редактирует слово.
 * @param id int <p>Идентификатор.</p>
 * @param board_id int <p>Идентификатор доски.</p>
 * @param word mixed <p>Слово.</p>
 * @param replace string <p>Слово-замена.</p>
 */
function words_edit($id, $word, $replace)
{
	db_words_edit(DataExchange::getDBLink(), $id, $word, $replace);
}
/**
 * Получает все слова.
 * @return array
 * Возвращает слова:<p>
 * 'id' - идентификатор.<br>
 * 'word' - слово для замены.<br>
 * 'replace' - замена.</p>
 */
function words_get_all()
{
	return db_words_get_all(DataExchange::getDBLink());
}
/**
 * Получает все слова по идентификатору доски.
 * @param string|int $board_id Идентификатор доски.
 * @return array
 * Возвращает слова:<p>
 * 'id' - идентификатор.<br>
 * 'word' - слово для замены.<br>
 * 'replace' - замена.</p>
 */
function words_get_all_by_board($board_id) { // Java CC
    return db_words_get_all_by_board(DataExchange::getDBLink(), $board_id);
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
 * @param string $annotation Аннотация.
 * @return string|null
 * Возвращает аннотацию.
 */
function boards_check_annotation($annotation) { // Java CC.
    $tmp = htmlentities($annotation, ENT_QUOTES, Config::MB_ENCODING);
    $len = strlen($tmp);

    if ($len == 0) {
        return null;
    }
	if ($len > Config::MAX_ANNOTATION_LENGTH) {
		throw new LimitException(LimitException::$messages['MAX_ANNOTATION']);
    }
	return $tmp;
}
/**
 * Проверяет корректность специфичного для доски бамплимита.
 * @param string $bump_limit Специфичный для доски бамплимит.
 * @return string
 * Возвращает безопасный для использования специфичный для доски бамплимит.
 */
function boards_check_bump_limit($bump_limit) { // Java CC
	$length = strlen($bump_limit);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if ($length <= $max_int_length && $length >= 1) {
		$bump_limit = RawUrlEncode($bump_limit);
		$length = strlen($bump_limit);
		if ($length > $max_int_length || (ctype_digit($bump_limit) === false)
                || $length < 1) {
			throw new FormatException(FormatException::$messages['BOARD_BUMP_LIMIT']);
		}
	} else {
		throw new FormatException(FormatException::$messages['BOARD_BUMP_LIMIT']);
    }
	return $bump_limit;
}
/**
 * Проверяет корректность имени отправителя по умолчанию.
 * @param string $name Имя отправителя по умолчанию.
 * @return string|null
 * Возвращает безопасное для использования имя отправителя по умолчанию.
 */
function boards_check_default_name($name) { // Java CC
    if (strlen($name) == 0) {
        return null;
    }
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
 * @param string $name Имя доски.
 * @return string
 * Возвращает безопасное для использования имя доски.
 */
function boards_check_name($name) { // Java CC
    $length = strlen($name);
    if ($length <= 16 && $length >= 1) {
        $name = RawUrlEncode($name);
        $length = strlen($name);
        if ($length > 16 || (strpos($name, '%') !== false) || $length < 1) {
            throw new FormatException(FormatException::$messages['BOARD_NAME']);
        }
    } else {
        throw new FormatException(FormatException::$messages['BOARD_NAME']);
    }
    return $name;
}
/**
 * Проверяет корректность политики загрузки одинаковых файлов.
 * @param string $same_upload Политика загрузки одинаковых файлов.
 * @return string
 * Возвращает безопасную для использования политику загрузки одинаковых файлов.
 */
function boards_check_same_upload($same_upload) { // Java CC
	$length = strlen($same_upload);
	if ($length <= 32 && $length >= 1) {
		$same_upload = RawUrlEncode($same_upload);
		$length = strlen($same_upload);
		if ($length > 32 || (strpos($same_upload, '%') !== false)
                || $length < 1) {
			throw new FormatException(FormatException::$messages['BOARD_SAME_UPLOAD']);
        }
	} else {
		throw new FormatException(FormatException::$messages['BOARD_SAME_UPLOAD']);
    }
	return $same_upload;
}
/**
 * Проверяет корректность заголовка доски.
 * @param string $title Заголовок доски.
 * @return string|null
 * Возвращает безопасный для использования заголовок доски.
 */
function boards_check_title($title) { // Java CC
	$length = strlen($title);
    if ($length == 0) {
        return null;
    }
	if ($length <= 50 && $length >= 1) {
		$title = htmlentities($title, ENT_QUOTES, Config::MB_ENCODING);
		$length = strlen($title);
		if ($length > 50 || $length < 1) {
			throw new FormatException(FormatException::$messages['BOARD_TITLE']);
        }
	} else {
		throw new FormatException(FormatException::$messages['BOARD_TITLE']);
    }
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
 * @param string|int $board_id Идентификатор доски.
 * @param string|int $user_id Идентификатор пользователя.
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
function boards_get_changeable_by_id($board_id, $user_id) { // Java CC
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
 * @param string|int $user_id Идентификатор пользователя.
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
function boards_get_visible($user_id) { // Java CC
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
 * @param mixed $id Идентификатор.
 * @return string
 * Возвращает безопасный для использования идентификатор категории.
 */
function categories_check_id($id) { // Java CC
    $length = strlen($id);
    $max_int_length = strlen('' . PHP_INT_MAX);
    if ($length <= $max_int_length && $length >= 1) {
        $id = RawUrlEncode($id);
        $length = strlen($id);
        if ($length > $max_int_length || (ctype_digit($id) === false)
                || $length < 1) {
            throw new FormatException(FormatException::$messages['CATEGORY_ID']);
        }
    } else {
        throw new FormatException(FormatException::$messages['CATEGORY_ID']);
    }
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

/********************************
 * Работа с вложенными файлами. *
 ********************************/

/**
 * Добавляет файл.
 * @param string $hash Хеш.
 * @param string $name Имя.
 * @param int $size Размер в байтах.
 * @param string $thumbnail Уменьшенная копия.
 * @param int $thumbnail_w Ширина уменьшенной копии.
 * @param int $thumbnail_h Высота уменьшенной копии.
 * @return string
 * Возвращает идентификатор вложенного файла.
 */
function files_add($hash, $name, $size, $thumbnail, $thumbnail_w,
        $thumbnail_h) { // Java CC
    return db_files_add(DataExchange::getDBLink(), $hash, $name, $size,
        $thumbnail, $thumbnail_w, $thumbnail_h);
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
 * @param array $boards Доски.
 * @param object $filter Фильтр (лямбда).
 * @param mixed $paramname,... Аргументы для фильтра (не обязательны).
 * @return array
 * Возвращает скрытые нити:<p>
 * 'user' - Пользователь.<br>
 * 'thread' - Нить.<br>
 * 'thread_number' - Номер оригинального сообщения.</p>
 */
function hidden_threads_get_filtred_by_boards($boards, $filter) { // Java CC
    $threads = db_hidden_threads_get_by_boards(DataExchange::getDBLink(),
            $boards);
    $filtred_threads = array();
    $filter_args = array();
    $filter_argn = 0;
    $n = func_num_args();
    for ($i = 2; $i < $n; $i++) { // Пропустим первые два аргумента функции.
        $filter_args[$filter_argn++] = func_get_arg($i);
    }
    foreach ($threads as $t) {
        $filter_args[$filter_argn] = $t;
        if (call_user_func_array($filter, $filter_args)) {
            array_push($filtred_threads, $t);
        }
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
 * Добавляет вложенное изображение.
 * @param string|null $hash Хеш.
 * @param string $name Имя.
 * @param int $widht Ширина.
 * @param int $height Высота.
 * @param int $size Размер в байтах.
 * @param string $thumbnail Уменьшенная копия.
 * @param int $thumbnail_w Ширина уменьшенной копии.
 * @param int $thumbnail_h Высота уменьшенной копии.
 * @return string
 * Возвращает идентификатор вложенного изображения.
 */
function images_add($hash, $name, $widht, $height, $size, $thumbnail,
        $thumbnail_w, $thumbnail_h) { // Java CC
    return db_images_add(DataExchange::getDBLink(), $hash, $name, $widht,
        $height, $size, $thumbnail, $thumbnail_w, $thumbnail_h);
}
/**
 * Проверяет, удовлетворяет ли загружаемое изображение ограничениям по размеру.
 * @param int $img_size Размер изображения в байтах.
 */
function images_check_size($size) { // Java CC
    if ($size < Config::MIN_IMGSIZE) {
        throw new LimitException(LimitException::$messages['MIN_IMG_SIZE']);
    }
}
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
function images_get_same($board_id, $image_hash, $user_id) { // Java CC
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
 * Получает все языки.
 * @return array
 * Возвращает языки:<p>
 * 'id' - Идентификатор.<br>
 * 'code' - Код ISO_639-2.</p>
 */
function languages_get_all()
{
	return db_languages_get_all(DataExchange::getDBLink());
}

/************************************************
 * Работа с вложенными ссылками на изображения. *
 ************************************************/

/**
 * Добавляет вложенную ссылку на изображение.
 * @param string $name Имя.
 * @param int $widht Ширина.
 * @param int $height Высота.
 * @param int $size Размер в байтах.
 * @param string $thumbnail Уменьшенная копия.
 * @param int $thumbnail_w Ширина уменьшенной копии.
 * @param int $thumbnail_h Высота уменьшенной копии.
 * @return string
 * Возвращает идентификатор вложенной ссылки на изображение.
 */
function links_add($name, $widht, $height, $size, $thumbnail, $thumbnail_w,
        $thumbnail_h) { // Java CC
    return db_links_add(DataExchange::getDBLink(), $name, $widht, $height,
            $size, $thumbnail, $thumbnail_w, $thumbnail_h);
}
/******************************
 * Работа с тегами макрочана. *
 ******************************/

/**
 * Добавляет тег макрочана.
 * @param string $name Имя.
 */
function macrochan_tags_add($name) { // Java CC
    db_macrochan_tags_add(DataExchange::getDBLink(), $name);
}
/**
 * Удаляет тег по заданному имени.
 * @param string $name Имя.
 */
function macrochan_tags_delete_by_name($name) { // Java CC
    db_macrochan_tags_delete_by_name(DataExchange::getDBLink(), $name);
}
/**
 * Получает все теги макрочана.
 * @return array
 * Возвращает теги макрочана:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.</p>
 */
function macrochan_tags_get_all() { // Java CC
    return db_macrochan_tags_get_all(DataExchange::getDBLink());
}

/*************************************
 * Работа с изображениями макрочана. *
 *************************************/

/**
 * Добавляет изображение макрочана.
 * @param string $name Имя.
 * @param string|int $width Ширина.
 * @param string|int $height Высота.
 * @param string|int $size Размер в байтах.
 * @param string $thumbnail Уменьшенная копия.
 * @param string|int $thumbnail_w Ширина уменьшенной копии.
 * @param string|int $thumbnail_h Высота уменьшенной копии.
 */
function macrochan_images_add($name, $width, $height, $size, $thumbnail,
        $thumbnail_w, $thumbnail_h) { // Java CC
    db_macrochan_images_add(DataExchange::getDBLink(), $name, $width, $height,
            $size, $thumbnail, $thumbnail_w, $thumbnail_h);
}
/**
 * Удаляет изображение по заданному имени.
 * @param string $name Имя.
 */
function macrochan_images_delete_by_name($name) { // Java CC
    db_macrochan_images_delete_by_name(DataExchange::getDBLink(), $name);
}
/**
 * Получает все изображения макрочана.
 * @return array
 * Возвращает изображения макрочана:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.<br>
 * 'width' - Ширина.<br>
 * 'height' - Высота.<br>
 * 'size' - Размер в байтах.<br>
 * 'thumbnail' - Уменьшенная копия.<br>
 * 'thumbnail_w' - Ширина уменьшенной копии.<br>
 * 'thumbnail_h' - Высота уменьшенной копии.</p>
 */
function macrochan_images_get_all() { // Java CC
    return db_macrochan_images_get_all(DataExchange::getDBLink());
}
function macrochan_images_get_by_tag() {
    throw new CommonException('No Implemented yet.');
}
/**
 * Получает случайное изображение макрочана с заданным именем тега макрочана.
 * @param string $name Имя тега макрочана.
 * @return array
 * Возвращает изображение макрочана:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.<br>
 * 'width' - Ширина.<br>
 * 'height' - Высота.<br>
 * 'size' - Размер в байтах.<br>
 * 'thumbnail' - Уменьшенная копия.<br>
 * 'thumbnail_w' - Ширина уменьшенной копии.<br>
 * 'thumbnail_h' - Высота уменьшенной копии.</p>
 */
function macrochan_images_get_random($name) {
    return db_macrochan_images_get_random(DataExchange::getDBLink(), $name);
}

/****************************************************
 * Работа со связями тегов и изображений макрочана. *
 ****************************************************/

/**
 * Добавляет связь тега и изображения макрочана.
 * @param string $tag_name Имя тега макрочана.
 * @param string $image_name Имя изображения макрочана.
 */
function macrochan_tags_images_add($tag_name, $image_name) { // Java CC
    db_macrochan_tags_images_add(DataExchange::getDBLink(), $tag_name,
            $image_name);
}
/**
 * Получает связь тега и изображением макрочана по заданному имени тега
 * и изображения.
 * @param string $tag_name Имя тега макрочана.
 * @param string $image_name Имя изображения макрочана.
 * @return array|null
 * Возвращает связь тега и изображения макрочана:<p>
 * 'tag' - Идентификатор тега макрочана.<br>
 * 'image' - Идентификатор изображения макрочана.</p>
 * Или null, если связи не существует.
 */
function macrochan_tags_images_get($tag_name, $image_name) { // Java CC
    return db_macrochan_tags_images_get(DataExchange::getDBLink(), $tag_name,
            $image_name);
}
/**
 * Получает все связи тегов и изображениями макрочана.
 * @return array
 * Возвращает связи тегов и изображениями макрочана:<p>
 * 'tag' - Идентификатор тега макрочана.<br>
 * 'image' - Идентификатор изображения макрочана.</p>
 */
function macrochan_tags_images_get_all() { // Java CC
    return db_macrochan_tags_images_get_all(DataExchange::getDBLink());
}

/**********************************************************
 * Работа с обработчиками автоматического удаления нитей. *
 **********************************************************/

/**
 * Добавляет обработчик автоматического удаления нитей.
 * @param name string <p>Имя функции обработчика автоматического удаления
 * нитей.</p>
 */
function popdown_handlers_add($name)
{
	db_popdown_handlers_add(DataExchange::getDBLink(), $name);
}
/**
 * Проверяет корректность идентификатора обработчика автоматического
 * удаления нитей.
 * @param mixed $id Идентификатор обработчика автоматического удаления  нитей.
 * @return string
 * Возвращает безопасный для использования идентификатор обработчика
 * автоматического удаления нитей.
 */
function popdown_handlers_check_id($id) { // Java CC
    $length = strlen($id);
    $max_int_length = strlen('' . PHP_INT_MAX);
    if ($length <= $max_int_length && $length >= 1) {
        $id = RawUrlEncode($id);
        $length = strlen($id);
        if ($length > $max_int_length || (ctype_digit($id) === false)
                || $length < 1) {
            throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_ID']);
        }
    } else {
        throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_ID']);
    }
    return $id;
}
/**
 * Проверяет корректность имени функции обработчика автоматического удаления
 * нитей.
 * @param name string <p>Имя функции обработчика автоматического удаления
 * нитей.</p>
 * Возвращает безопасное для использования имя функции обработчика
 * автоматического удаления нитей.
 */
function popdown_handlers_check_name($name)
{
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false)
			|| $length < 1 || ctype_digit($name[0]))
		{
			throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_NAME']);
		}
	}
	else
		throw new FormatException(FormatException::$messages['POPDOWN_HANDLER_NAME']);
	return $name;
}
/**
 * Удаляет обработчик автоматического удаления нитей.
 * @param id mixed <p>Идентификатор обработчика автоматического удаления
 * нитей.</p>
 */
function popdown_handlers_delete($id)
{
	db_popdown_handlers_delete(DataExchange::getDBLink(), $id);
}
/**
 * Получает все обработчики автоматического удаления нитей.
 * @return array
 * Возвращает обработчики автоматического удаления нитей:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя функции.</p>
 */
function popdown_handlers_get_all() { // Java CC
    return db_popdown_handlers_get_all(DataExchange::getDBLink());
}

/*************************
 * Работа с сообщениями. *
 *************************/

/**
 * Добавляет сообщение.
 * @param int $board_id Идентификатор доски.
 * @param int $thread_id Идентификатор нити.
 * @param int $user_id Идентификатор пользователя.
 * @param string|null $password Пароль на удаление сообщения.
 * @param string|null $name Имя отправителя.
 * @param string|null $tripcode Трипкод.
 * @param int $ip IP-адрес отправителя.
 * @param string|null $subject Тема.
 * @param string $date_time Время сохранения.
 * @param string|null $text Текст.
 * @param int|null $sage Флаг поднятия нити.
 * @return array
 * Возвращает добавленное сообщение:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Доска.<br>
 * 'thread' - Нить.<br>
 * 'number' - Номер.<br>
 * 'user' - Пользователь.<br>
 * 'password' - Пароль.<br>
 * 'name' - Имя отправителя.<br>
 * 'tripcode' - Трипкод.<br>
 * 'ip'- IP-адрес отправителя.<br>
 * 'subject' - Тема.<br>
 * 'date_time' - Время сохранения.<br>
 * 'text' - Текст.<br>
 * 'sage' - Флаг поднятия нити.</p>
 */
function posts_add($board_id, $thread_id, $user_id, $password, $name, $tripcode,
        $ip, $subject, $date_time, $text, $sage) { // Java CC
    return db_posts_add(DataExchange::getDBLink(), $board_id, $thread_id,
            $user_id, $password, $name, $tripcode, $ip, $subject, $date_time,
            $text, $sage);
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
 * @param string|int $id Идентификатор сообщения.
 * @return string
 * Возвращает безопасный для использования идентификатор сообщения.
 */
function posts_check_id($id) {
    $length = strlen($id);
    $max_int_length = strlen('' . PHP_INT_MAX);
    if ($length <= $max_int_length && $length >= 1) {
        $id = RawUrlEncode($id);
        $length = strlen($id);
        if ($length > $max_int_length || (ctype_digit($id) === false)
                || $length < 1) {
            throw new FormatException(FormatException::$messages['POST_ID']);
        }
    } else {
        throw new FormatException(FormatException::$messages['POST_ID']);
    }
    return $id;
}
/**
 * Проверяет, удовлетворяет ли имя отправителя ограничениям по размеру.
 * @param string $name Имя отправителя.
 */
function posts_check_name_size($name) { // Java CC
    if (strlen($name) > Config::MAX_THEME_LENGTH) {
        throw new LimitException(LimitException::$messages['MAX_NAME_LENGTH']);
    }
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
 * Проверяет корректность пароля для удаления сообщения.
 * @param string $password Пароль.
 * @return string
 * Возвращает безопасный для использования пароль для удаления сообщения.
 */
function posts_check_password($password) { // Java CC
    $length = strlen($password);
    if ($length <= 12 && $length >= 1) {
        $password = RawUrlEncode($password);
        $length = strlen($password);
        if ($length > 12 || (strpos($password, '%') !== false) || $length < 1) {
            throw new FormatException(FormatException::$messages['POST_PASSWORD']);
        }
    } else {
        throw new FormatException(FormatException::$messages['POST_PASSWORD']);
    }
    return $password;
}
/**
 * Проверяет, удовлетворяет ли тема сообщения ограничениям по размеру.
 * @param string $subject Тема сообщения.
 */
function posts_check_subject_size($subject) { // Java CC
    if (strlen($subject) > Config::MAX_THEME_LENGTH) {
        throw new LimitException(LimitException::$messages['MAX_SUBJECT_LENGTH']);
    }
}
/**
 * Проверяет корректность текста.
 * @param string $text Текст сообщения.
 */
function posts_check_text($text) { // Java CC
    if (!check_utf8($text)) {
        throw new CommonException(CommonException::$messages['TEXT_UNICODE']);
    }
}
/**
 * Проверяет, удовлетворяет ли текст сообщения ограничениям по размеру.
 * @param string $text Текст сообщения.
 */
function posts_check_text_size($text) { // Java CC
    if (mb_strlen($text) > Config::MAX_MESSAGE_LENGTH) {
        throw new LimitException(LimitException::$messages['MAX_TEXT_LENGTH']);
    }
}
/**
 * Урезает текст сообщения.
 * TODO: Урезание в длину.
 * @param message string <p>Текст сообщения.</p>
 * @param preview_lines mixed <p>Количество строк, которые нужно оставить.</p>
 * @param is_cutted boolean <p>Ссылка на флаг урезанного сообщения.</p>
 * @return string
 * Возвращает урезанный текст.
 */
function posts_corp_text(&$message, $preview_lines, &$is_cutted)
{
	$lines = explode('<br>', $message);
	if(count($lines) > $preview_lines)
	{
		$is_cutted = 1;
		return implode('<br>', array_slice($lines, 0, $preview_lines));
	}
	else
	{
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
 * отправителя, оставленные с заданного момента времени.
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
 * Возвращает сообщения:<p>
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
 * Получает сообщения с заданных досок.
 * @param boards array <p>Доски.</p>
 * @return array
 * Возвращает сообщения:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'board_name' - Имя доски.<br>
 * 'thread' - Идентификатор нити.<br>
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
 * 'thread' - Идентификатор нити.<br>
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
 * Возвращает сообщения:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'board_name' - Имя доски.<br>
 * 'thread' - Идентификатор нити.<br>
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
 * Получает заданное сообщение, доступное для просмотра заданному пользователю.
 * @param string|int post_id Идентификатор сообщения.
 * @param string|int user_id Идентификатор пользователя.
 * @return array
 * Возвращает сообщение:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'thread' - Идентификатор нити.<br>
 * 'number' - Номер.<br>
 * 'password' - Пароль.<br>
 * 'name' - Имя отправителя.<br>
 * 'tripcode' - Трипкод.<br>
 * 'ip' - IP-адрес отправителя.<br>
 * 'subject' - Тема.<br>
 * 'date_time' - Время сохранения.<br>
 * 'text' - Текст.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'board_name' - Имя доски.</p>
 */
function posts_get_visible_by_id($post_id, $user_id) { // Java CC
    return db_posts_get_visible_by_id(DataExchange::getDBLink(), $post_id,
            $user_id);
}
/**
 * Получает заданное сообщение, доступное для просмотра заданному пользователю.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param post_number mixed <p>Номер сообщения.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает сообщение:<p>
 * 'id' - Идентификатор.<br>
 * 'thread' - Идентификатор нити.<br>
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
 * Для каждой нити получает отфильтрованные сообщения, доступные для просмотра
 * заданному пользователю.
 * @param array $threads Нити.
 * @param string|int $user_id Идентификатор пользователя.
 * @param object $filter Фильтр (лямбда).
 * @param mixed $paramname,... Аргументы для фильтра (не обязательны).
 * @return array
 * Возвращает сообщения:<p>
 * 'id' - Идентификатор.<br>
 * 'thread' - Идентификатор нити.<br>
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
function posts_get_visible_filtred_by_threads($threads, $user_id, $filter) { // Java CC
    $numargs = func_num_args();
    $args = array(); // Аргументы для лямбды.
    for ($i = 3; $i < $numargs; $i++) { // Пропустим первые 3 аргумента фукнции.
        array_push($args, func_get_arg($i));
    }
    return db_posts_get_visible_filtred_by_threads(DataExchange::getDBLink(),
        $threads, $user_id, $filter, $args);
}
/**
 * Очищает и размечает текст сообщения заданной доски.
 * @param string $text Текст сообщения.
 * @param array $board Доска.
 */
function posts_prepare_text(&$text, $board) { // Java CC
    purify_ascii($text);
    kotoba_mark($text, $board);
    $text = str_replace("</blockquote>\n", '</blockquote>', $text);
    $text = str_replace("\n<blockquote", '<blockquote', $text);
    $text = preg_replace('/\n{3,}/', '\n', $text);
    $text = preg_replace('/\n/', '<br>', $text);
}

/***************************************************
 * Работа со связями сообщений и вложенных файлов. *
 ***************************************************/

/**
 * Добавляет связь сообщения с вложенным файлом.
 * @param int $post Идентификатор сообщения.
 * @param int file Идентификатор вложенного файла.
 * @param int $deleted Флаг удаления.
 */
function posts_files_add($post, $file, $deleted) { // Java CC
    db_posts_files_add(DataExchange::getDBLink(), $post, $file, $deleted);
}

/********************************************************
 * Работа со связями сообщений и вложенных изображений. *
 ********************************************************/

/**
 * Добавляет связь сообщения с вложенным изображением.
 * @param int $post Идентификатор сообщения.
 * @param int $image Идентификатор вложенного изображения.
 * @param int $deleted Флаг удаления.
 */
function posts_images_add($post, $image, $deleted) { // Java CC
    db_posts_images_add(DataExchange::getDBLink(), $post, $image, $deleted);
}

/******************************************************************
 * Работа со связями сообщений и вложенных ссылок на изображения. *
 ******************************************************************/

/**
 * Добавляет связь сообщения с вложенной ссылкой на изображение.
 * @param int $post Идентификатор сообщения.
 * @param int $link Идентификатор вложенной ссылки на изображение.
 * @param int $deleted Флаг удаления.
 */
function posts_links_add($post, $link, $deleted) { // Java CC
    db_posts_links_add(DataExchange::getDBLink(), $post, $link, $deleted);
}

/***************************************************
 * Работа со связями сообщений и вложенного видео. *
 ***************************************************/

/**
 * Добавляет связь сообщения с вложенным видео.
 * @param int $post Идентификатор сообщения.
 * @param int $video Идентификатор вложенного видео.
 * @param int $deleted Флаг удаления.
 */
function posts_videos_add($post, $video, $deleted) { // Java CC
    db_posts_videos_add(DataExchange::getDBLink(), $post, $video, $deleted);
}

/**********************
 * Работа со стилями. *
 **********************/

/**
 * Добавляет стиль.
 * @param name string <p>Имя файла стиля.</p>
 */
function stylesheets_add($name)
{
	db_stylesheets_add(DataExchange::getDBLink(), $name);
}
/**
 * Проверяет корректность идентификатора стиля.
 * @param id mixed <p>Идентификатор стиля.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор стиля.
 */
function stylesheets_check_id($id)
{
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false)
			|| $length < 1)
		{
			throw new FormatException(FormatException::$messages['STYLESHEET_ID']);
		}
	}
	else
		throw new FormatException(FormatException::$messages['STYLESHEET_ID']);
	return $id;
}
/**
 * Проверяет корректность имени файла стиля.
 * @param name string <p>Имя файла стиля.</p>
 * @return string
 * Возвращает безопасное для использования имя файла стиля.
 */
function stylesheets_check_name($name)
{
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
 * Удаляет заданный стиль.
 * @param id mixed <p>Идентификатор стиля.</p>
 */
function stylesheets_delete($id)
{
	db_stylesheets_delete(DataExchange::getDBLink(), $id);
}
/**
 * Получает все стили.
 * @return array
 * Возвращает стили:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя файла.</p>
 */
function stylesheets_get_all()
{
	return db_stylesheets_get_all(DataExchange::getDBLink());
}

/********************
 * Работа с нитями. *
 ********************/

/**
 * Добавляет нить. Если номер оригинального сообщения null, то будет создана
 * пустая нить.
 * @param int $board_id Идентификатор доски.
 * @param int|null $original_post Номер оригинального сообщения.
 * @param int|null $bump_limit Специфичный для нити бамплимит.
 * @param int $sage Флаг поднятия нити.
 * @param int|null $with_attachments Флаг вложений.
 * @return array|null
 * Возвращает нить:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.</p>
 * Или null, если что-то пошло не так.
 */
function threads_add($board_id, $original_post, $bump_limit, $sage,
        $with_attachments) { // Java CC
    return db_threads_add(DataExchange::getDBLink(), $board_id, $original_post,
            $bump_limit, $sage, $with_attachments);
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
		{
			throw new FormatException(FormatException::$messages['THREAD_BUMP_LIMIT']);
		}
	}
	else
		throw new FormatException(FormatException::$messages['THREAD_BUMP_LIMIT']);
	return $bump_limit;
}
/**
 * Проверяет корректность идентификатора нити.
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
		{
			throw new FormatException(FormatException::$messages['THREAD_ID']);
		}
	}
	else
		throw new FormatException(FormatException::$messages['THREAD_ID']);
	return $id;
}
/**
 * Проверяет корректность номера оригинального сообщения.
 * @param string|int $original_post Номер оригинального сообщения.
 * @return string
 * Возвращает безопасный для использования номер оригинального сообщения.
 */
function threads_check_original_post($original_post) { // Java CC
    $length = strlen($original_post);
    $max_int_length = strlen('' . PHP_INT_MAX);
    if ($length <= $max_int_length && $length >= 1) {
        $original_post = RawUrlEncode($original_post);
        $length = strlen($original_post);
        if($length > $max_int_length || (ctype_digit($original_post) === false)
                || $length < 1) {
            throw new FormatException(FormatException::$messages['THREAD_NUMBER']);
        }
    } else {
        throw new FormatException(FormatException::$messages['THREAD_NUMBER']);
    }
    return $original_post;
}
/**
 * Редактирует заданную нить.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sage mixed <p>Флаг поднятия нити.</p>
 * @param sticky mixed <p>Флаг закрепления.</p>
 * @param with_attachments mixed <p>Флаг вложений.</p>
 */
function threads_edit($thread_id, $bump_limit, $sticky, $sage,
		$with_attachments)
{
	db_threads_edit(DataExchange::getDBLink(), $thread_id, $bump_limit, $sticky,
		$sage, $with_attachments);
}
/**
 * Редактирует номер оригинального сообщения нити.
 * @param int $id Идентификатор нити.
 * @param int $original_post Номер оригинального сообщения нити.
 */
function threads_edit_original_post($id, $original_post) { // Java CC
    db_threads_edit_original_post(DataExchange::getDBLink(), $id,
        $original_post);
}
/**
 * Получает все нити.
 * @return array
 * Возвращает нити:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.</p>
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
 * Получает заданную нить, доступную для изменения заданному пользователю.
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'archived' - Флаг архивирования.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'with_attachments' - Флаг вложений.</p>
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
 * 'board' - Идентификатор доски.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.</p>
 */
function threads_get_moderatable($user_id)
{
	return db_threads_get_moderatable(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает заданную нить, доступную для модерирования заданному пользователю.
 * @param string|int $thread_id Идентификатор нити.
 * @param string|int $user_id Идентификатор пользователя.
 * @return mixed
 * Возвращает нить:<p>
 * 'id' - Идентификатор.</p>
 * Или null, если заданная нить не доступна для модерирования.
 */
function threads_get_moderatable_by_id($thread_id, $user_id) { // Java CC
    return db_threads_get_moderatable_by_id(DataExchange::getDBLink(),
            $thread_id, $user_id);
}
/**
 * Получает с заданной страницы доски доступные для просмотра пользователю нити
 * и количество сообщений в них.
 * @param string|int $board_id Идентификатор доски.
 * @param string|int $page Номер страницы.
 * @param string|int $user_id Идентификатор пользователя.
 * @param string|int $threads_per_page Количество нитей на странице.
 * @return array
 * Возвращает нити:<p>
 * 'id' - Идентификатор.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'posts_count' - Число доступных для просмотра сообщений.</p>
 */
function threads_get_visible_by_board($board_id, $page, $user_id,
        $threads_per_page) { // Java CC
    return db_threads_get_visible_by_board(DataExchange::getDBLink(), $board_id,
        $page, $user_id, $threads_per_page);
}
/**
 * Ищет с заданной страницы доски доступные для просмотра пользователю нити
 * и количество сообщений в них.
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param page mixed <p>Номер страницы.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param threads_per_page mixed <p>Количество нитей на странице.</p>
 * @param string mixed <p>Слова для поиска.</p>
 * @return array
 * Возвращает нити:<p>
 * 'id' - Идентификатор.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'posts_count' - Число доступных для просмотра сообщений.</p>
 */
function threads_search_visible_by_board($board_id, $page, $user_id,
	$threads_per_page, $string)
{
	return db_threads_search_visible_by_board(DataExchange::getDBLink(), $board_id,
		$page, $user_id, $threads_per_page, $string);
}
/**
 * Получает заданную нить, доступную для просмотра заданному пользователю.
 * @param string|int $board Идентификатор доски.
 * @param string|int $original_post Номер нити.
 * @param string|int $user_id Идентификатор пользователя.
 * @return array
 * Возвращает нить:<p>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'archived' - Флаг архивирования.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * 'posts_count' - Число доступных для просмотра сообщений в нити.</p>
 */
function threads_get_visible_by_original_post($board, $original_post, $user_id)
{
    return db_threads_get_visible_by_original_post(DataExchange::getDBLink(),
            $board, $original_post, $user_id);
}
/**
 * Вычисляет количество нитей, доступных для просмотра заданному пользователю
 * на заданной доске.
 * @param string|int $user_id Идентификатор пользователя.
 * @param string|int $board_id Идентификатор доски.
 * @return string
 * Возвращает количество нитей.
 */
function threads_get_visible_count($user_id, $board_id) { // Java CC
    return db_threads_get_visible_count(DataExchange::getDBLink(), $user_id,
        $board_id);
}

/**********************************************
 * Работа с обработчиками загружаемых файлов. *
 **********************************************/

/**
 * Добавляет обработчик загружаемых файлов.
 * @param name string <p>Имя фукнции обработчика загружаемых файлов.</p>
 */
function upload_handlers_add($name)
{
	db_upload_handlers_add(DataExchange::getDBLink(), $name);
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
	$length = strlen($id);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$id = RawUrlEncode($id);
		$length = strlen($id);
		if($length > $max_int_length || (ctype_digit($id) === false)
			|| $length < 1)
		{
			throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_ID']);
		}
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_ID']);
	return $id;
}
/**
 * Проверяет корректность имени фукнции обработчика загружаемых файлов.
 * @param name string <p>Имя фукнции обработчика загружаемых файлов.</p>
 * @return string
 * Возвращает безопасное для использования имя фукнции обработчика загружаемых
 * файлов.
 */
function upload_handlers_check_name($name)
{
	$length = strlen($name);
	if($length <= 50 && $length >= 1)
	{
		$name = RawUrlEncode($name);
		$length = strlen($name);
		if($length > 50 || (strpos($name, '%') !== false)
			|| $length < 1 || ctype_digit($name[0]))
		{
			throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_NAME']);
		}
	}
	else
		throw new FormatException(FormatException::$messages['UPLOAD_HANDLER_NAME']);
	return $name;
}
/**
 * Удаляет обработчик загружаемых файлов.
 * @param id mixed <p>Идентификатор обработчика загружаемых файлов.</p>
 */
function upload_handlers_delete($id)
{
	db_upload_handlers_delete(DataExchange::getDBLink(), $id);
}
/**
 * Получает все обработчики загружаемых файлов.
 * @return array
 * Возвращает обработчики загружаемых файлов:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя фукнции.</p>
 */
function upload_handlers_get_all()
{
	return db_upload_handlers_get_all(DataExchange::getDBLink());
}

/***************************************
 * Работа с типами загружаемых файлов. *
 ***************************************/

/**
 * Добавляет тип загружаемых файлов.
 * @param extension string <p>Расширение.</p>
 * @param store_extension string <p>Сохраняемое расширение.</p>
 * @param is_image mixed <p>Флаг изображения.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемого
 * файла.</p>
 * @param thumbnail_image string <p>Уменьшенная копия.</p>
 */
function upload_types_add($extension, $store_extension, $is_image,
    $upload_handler_id, $thumbnail_image)
{
    db_upload_types_add(DataExchange::getDBLink(), $extension, $store_extension,
        $is_image, $upload_handler_id, $thumbnail_image);
}
/**
 * Проверяет корректность расширения загружаемого файла.
 * @param ext string <p>Расширение.</p>
 * @return string
 * Возвращает безопасное для использования расширение загружаемого файла.
 */
function upload_types_check_extension($ext)
{
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
 * Проверяет корректность идентификатора типа загружаемых файлов.
 * @param id mixed <p>Идентификатор.</p>
 * @return string
 * Возвращает безопасный для использования идентификатор типа загружаемых файлов.
 */
function upload_types_check_id($id)
{
    $length = strlen($id);
    $max_int_length = strlen('' . PHP_INT_MAX);
    if($length <= $max_int_length && $length >= 1)
    {
        $id = RawUrlEncode($id);
        $length = strlen($id);
        if($length > $max_int_length || (ctype_digit($id) === false)
            || $length < 1)
        {
            throw new FormatException(FormatException::$messages['UPLOAD_TYPE_ID']);
        }
    }
    else
        throw new FormatException(FormatException::$messages['UPLOAD_TYPE_ID']);
    return $id;
}
/**
 * Проверяет корректность сохраняемого расширения загружаемого файла.
 * @param store_ext string <p>Сохраняемое расширение.</p>
 * @return string
 * Возвращает безопасное для использования сохраняемое расширение загружаемого
 * файла.
 */
function upload_types_check_store_extension($store_ext)
{
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
 * Проверяет корректность имени файла уменьшенной копии типа загружаемых файлов.
 * Подробнее см. заметки к таблице upload_types.
 * @param string $thumbnail_image Имя файла уменьшенной копии.
 * @return string
 * Возвращает безопасное для использования имя файла уменьшенной копии типа
 * загружаемых файлов.
 */
function upload_types_check_thumbnail_image($thumbnail_image) { // Java CC
    $length = strlen($thumbnail_image);
    if ($length <= 256 && $length >= 1) {
        $thumbnail_image = RawUrlEncode($thumbnail_image);
        $length = strlen($thumbnail_image);
        if ($length > 256 || (strpos($thumbnail_image, '%') !== false)
                || $length < 1) {
            throw new FormatException(FormatException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE']);
        }
    } else {
        throw new FormatException(FormatException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE']);
    }
    return $thumbnail_image;
}
/**
 * Удаляет тип загружаемых файлов.
 * @param id mixed <p>Идентифаикатор.</p>
 */
function upload_types_delete($id)
{
    db_upload_types_delete(DataExchange::getDBLink(), $id);
}
/**
 * Редактирует тип загружаемых файлов.
 * @param id mixed <p>Идентификатор.</p>
 * @param store_extension string <p>Сохраняемое расширение.</p>
 * @param is_image mixed <p>Флаг изображения.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемых
 * файлов.</p>
 * @param thumbnail_image string <p>Имя файла уменьшенной копии.</p>
 */
function upload_types_edit($id, $store_extension, $is_image,
    $upload_handler_id, $thumbnail_image)
{
    db_upload_types_edit(DataExchange::getDBLink(), $id, $store_extension,
        $is_image, $upload_handler_id, $thumbnail_image);
}
/**
 * Получает все типы загружаемых файлов.
 * @return array
 * Возвращает типы загружаемых файлов:<p>
 * 'id' - Идентификатор.<br>
 * 'extension' - Расширение.<br>
 * 'store_extension' - Сохраняемое расширение.<br>
 * 'is_image' - Флаг изображения.<br>
 * 'upload_handler' - Идентификатор обработчика загружаемых файлов.<br>
 * 'thumbnail_image' - Имя файла уменьшенной копии.</p>
 */
function upload_types_get_all()
{
    return db_upload_types_get_all(DataExchange::getDBLink());
}
/**
 * Получает типы загружаемых файлов, доступных для загрузки на заданной доске.
 * @param string|int $board_id Идентификатор доски.
 * @return array
 * Возвращает типы загружаемых файлов:<p>
 * 'id' - Идентификатор.<br>
 * 'extension' - Расширение.<br>
 * 'store_extension' - Сохраняемое расширение.<br>
 * 'is_image' - Флаг изображения.<br>
 * 'upload_handler' - Идентификатор обработчика загружаемых файлов.<br>
 * 'upload_handler_name' - Имя обработчика загружаемых файлов.<br>
 * 'thumbnail_image' - Имя файла уменьшенной копии.</p>
 */
function upload_types_get_by_board($board_id) { // Java CC
    return db_upload_types_get_by_board(DataExchange::getDBLink(), $board_id);
}

/***********************************************
 * Работа со связями пользователей с группами. *
 ***********************************************/

/**
 * Добавляет пользователя в группу.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param group_id mixed <p>Идентификатор группы.</p>
 */
function user_groups_add($user_id, $group_id)
{
    db_user_groups_add(DataExchange::getDBLink(), $user_id, $group_id);
}
/**
 * Удаляет заданного пользователя из заданной группы.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param group_id mixed <p>Идентификатор группы.</p>
 */
function user_groups_delete($user_id, $group_id)
{
    db_user_groups_delete(DataExchange::getDBLink(), $user_id, $group_id);
}
/**
 * Переносит заданного пользователя из одной группы в другую.
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param old_group_id mixed <p>Идентификатор старой группы.</p>
 * @param new_group_id mixed <p>Идентификатор новой группы.</p>
 */
function user_groups_edit($user_id, $old_group_id, $new_group_id)
{
    db_user_groups_edit(DataExchange::getDBLink(), $user_id, $old_group_id,
        $new_group_id);
}
/**
 * Получает все связи пользователей с группами.
 * @return array
 * Возвращает связи:<p>
 * 'user' - Идентификатор пользователя.<br>
 * 'group' - Идентификатор группы.</p>
 */
function user_groups_get_all()
{
    return db_user_groups_get_all(DataExchange::getDBLink());
}

/****************************
 * Работа с пользователями. *
 ****************************/

/**
 * Проверяет корректность перенаправления.
 * @param string $goto Перенаправление.
 * @return string
 * Возвращает безопасное для использования перенаправление.
 */
function users_check_goto($goto) { // Java CC
    if ($goto === 'b' || $goto === 't') {
        return $goto;
    } else {
        throw new FormatException(FormatException::$messages['USER_GOTO']);
    }
}
/**
 * Проверяет корректность идентификатора пользователя.
 * @param string|id $id Идентификатор пользователя.
 * @return string
 * Возвращает безопасный для использования идентификатор пользователя.
 */
function users_check_id($id) { // Java CC
    $length = strlen($id);
    $max_int_length = strlen('' . PHP_INT_MAX);
    if ($length <= $max_int_length && $length >= 1) {
        $id = RawUrlEncode($id);
        $length = strlen($id);
        if($length > $max_int_length || (ctype_digit($id) === false)
                || $length < 1) {
            throw new FormatException(FormatException::$messages['USER_ID']);
        }
    } else {
        throw new FormatException(FormatException::$messages['USER_ID']);
    }
    return $id;
}
/**
 * Проверяет корректность хеша ключевого слова.
 * @param string $keyword Хеш ключевого слова.
 * @return string
 * Возвращает безопасный для использования хеш ключевого слова.
 */
function users_check_keyword($keyword) { // Java CC
    $length = strlen($keyword);
    if ($length <= 32 && $length >= 16) {
        $keyword = RawUrlEncode($keyword);
        $length = strlen($keyword);
        if ($length > 32 || (strpos($keyword, '%') !== false) || $length < 16) {
            throw new FormatException(FormatException::$messages['USER_KEYWORD']);
        }
    } else {
        throw new FormatException(FormatException::$messages['USER_KEYWORD']);
    }
    return $keyword;
}
/**
 * Проверяет корректность количества строк в предпросмотре сообщения.
 * @param string|int $lines_per_post Количество строк в предпросмотре сообщения.
 * @return string
 * Возвращает безопасное для использования количество строк в предпросмотре
 * сообщения.
 */
function users_check_lines_per_post($lines_per_post) { // Java CC
    $length = strlen($lines_per_post);
    if ($length <= 2 && $length >= 1) {
        $lines_per_post = RawUrlEncode($lines_per_post);
        $length = strlen($lines_per_post);
        if($length > 2 || (ctype_digit($lines_per_post) === false)
                || $length < 1) {
            throw new FormatException(sprintf(FormatException::$messages['USER_LINES_PER_POST'],
                Config::MIN_LINESPERPOST, Config::MAX_LINESPERPOST));
        }
    } else {
        throw new FormatException(sprintf(FormatException::$messages['USER_LINES_PER_POST'],
            Config::MIN_LINESPERPOST, Config::MAX_LINESPERPOST));
    }
    return $lines_per_post;
}
/**
 * Проверяет корректность числа сообщений в нити на странице просмотра доски.
 * @param string|int $posts_per_thread Число сообщений в нити на странице просмотра
 * доски.
 * @return string
 * Возвращает безопасное для использования число сообщений в нити на странице
 * просмотра доски.
 */
function users_check_posts_per_thread($posts_per_thread) { // Java CC
    $length = strlen($posts_per_thread);
    if ($length <= 2 && $length >= 1) {
        $posts_per_thread = RawUrlEncode($posts_per_thread);
        $length = strlen($posts_per_thread);
        if($length > 2 || (ctype_digit($posts_per_thread) === false)
                || $length < 1) {
            throw new FormatException(sprintf(FormatException::$messages['USER_POSTS_PER_THREAD'],
                Config::MIN_POSTSPERTHREAD, Config::MAX_POSTSPERTHREAD));
        }
    } else {
        throw new FormatException(sprintf(FormatException::$messages['USER_POSTS_PER_THREAD'],
            Config::MIN_POSTSPERTHREAD, Config::MAX_POSTSPERTHREAD));
    }
    return $posts_per_thread;
}
/**
 * Проверяет корректность числа нитей на странице просмотра доски.
 * @param string|int $threads_per_page Число нитей на странице просмотра доски.
 * @return string
 * Возвращает безопасное для использования число нитей на странице просмотра
 * доски.
 */
function users_check_threads_per_page($threads_per_page) { // Java CC
    $length = strlen($threads_per_page);
    if ($length <= 2 && $length >= 1) {
        $threads_per_page = RawUrlEncode($threads_per_page);
        $length = strlen($threads_per_page);
        if ($length > 2 || (ctype_digit($threads_per_page) === false)
                || $length < 1) {
            throw new FormatException(sprintf(FormatException::$messages['USER_THREADS_PER_PAGE'],
                Config::MIN_THREADSPERPAGE, Config::MAX_THREADSPERPAGE));
        }
    } else {
        throw new FormatException(sprintf(FormatException::$messages['USER_THREADS_PER_PAGE'],
            Config::MIN_THREADSPERPAGE, Config::MAX_THREADSPERPAGE));
    }
    return $threads_per_page;
}
/**
 * Редактирует пользователя с заданным ключевым словом или добавляет нового.
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @param posts_per_thread mixed <p>Число сообщений в нити на странице просмотра доски.</p>
 * @param threads_per_page mixed <p>Число нитей на странице просмотра доски.</p>
 * @param lines_per_post mixed <p>Количество строк в предпросмотре сообщения.</p>
 * @param language mixed <p>Идентификатор языка.</p>
 * @param stylesheet mixed <p>Идентификатор стиля.</p>
 * @param password mixed <p>Пароль для удаления сообщений.</p>
 * @param goto string <p>Перенаправление.</p>
 */
function users_edit_by_keyword($keyword, $posts_per_thread, $threads_per_page,
    $lines_per_post, $language, $stylesheet, $password, $goto)
{
    db_users_edit_by_keyword(DataExchange::getDBLink(), $keyword,
        $posts_per_thread, $threads_per_page, $lines_per_post, $language,
        $stylesheet, $password, $goto);
}
/**
 * Получает всех пользователей.
 * @return array
 * Возвращает идентификаторы пользователей:<p>
 * 'id' - Идентификатор пользователя.</p>
 */
function users_get_all()
{
	return db_users_get_all(DataExchange::getDBLink());
}
/**
 * Получает ползователя с заданным ключевым словом.
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @return array
 * Возвращает настройки:<p>
 * 'id' - Идентификатор.<br>
 * 'posts_per_thread' - Число сообщений в нити на странице просмотра доски.<br>
 * 'threads_per_page' - Число нитей на странице просмотра доски.<br>
 * 'lines_per_post' - Количество строк в предпросмотре сообщения.<br>
 * 'language' - Идентификатор языка.<br>
 * 'stylesheet' - Идентификатор стиля.<br>
 * 'password' - Пароль для удаления сообщений.<br>
 * 'goto' - Перенаправление.<br>
 * 'groups' - Группы, в которые входит пользователь.</p>
 */
function users_get_by_keyword($keyword)
{
    return db_users_get_by_keyword(DataExchange::getDBLink(), $keyword);
}
/**
 * Устанавливает перенаправление заданному пользователю.
 * @param int $id Идентификатор пользователя.
 * @param string $goto Перенаправление.
 */
function users_set_goto($id, $goto) { // Java CC
    db_users_set_goto(DataExchange::getDBLink(), $id, $goto);
}
/**
 * Устанавливает пароль для удаления сообщений заданному пользователю.
 * @param int $id Идентификатор пользователя.
 * @param string $password Пароль для удаления сообщений.
 */
function users_set_password($id, $password) { // Java CC
    db_users_set_password(DataExchange::getDBLink(), $id, $password);
}

/*****************************
 * Работа с вложенным видео. *
 *****************************/

/**
 * Добавляет вложенное видео.
 * @param string $code HTML-код.
 * @param int $widht Ширина.
 * @param int $height Высота.
 * @return string
 * Возвращает идентификатор вложенного видео.
 */
function videos_add($code, $widht, $height) { // Java CC
    return db_videos_add(DataExchange::getDBLink(), $code, $widht, $height);
}
/**
 * Проверяет корректность кода видео.
 * @param string $code Код видео.
 * @return string
 * Возвращает безопасный для использования код видео.
 */
function videos_check_code($code) { // Java CC
    $code = RawURLEncode($code);
    if (strlen($code) > Config::MAX_FILE_LINK) {
        throw new LimitException(LimitException::$messages['MAX_FILE_LINK']);
    }
    return RawURLEncode($code);
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
/*function posts_uploads_get_by_posts($posts)
{
	return db_posts_uploads_get_by_posts(DataExchange::getDBLink(), $posts);
}*/
/**
 * Связывает сообщение с информацией о загрузке.
 * @param post_id mixed <p>идентификатор сообщения.</p>
 * @param upload_id mixed <p>идентификатор записи с информацией о загрузке.</p>
 */
/*function posts_uploads_add($post_id, $upload_id)
{
	db_posts_uploads_add(DataExchange::getDBLink(), $post_id, $upload_id);
}*/
/**
 * Удаляет закрепления загрузок за заданным сообщением.
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 */
/*function posts_uploads_delete_by_post($post_id)
{
	db_posts_uploads_delete_by_post(DataExchange::getDBLink(), $post_id);
}*/

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
/*function uploads_add($hash, $is_image, $upload_type, $file, $image_w, $image_h,
	$size, $thumbnail, $thumbnail_w, $thumbnail_h)
{
	return db_uploads_add(DataExchange::getDBLink(), $hash, $is_image,
		$upload_type, $file, $image_w, $image_h, $size, $thumbnail,
		$thumbnail_w, $thumbnail_h);
}*/
/**
 * Проверяет, удовлетворяет ли загружаемое изображение ограничениям по размеру.
 * @param img_size mixed <p>Размер изображения в байтах.</p>
 */
/*function uploads_check_image_size($img_size)
{
	if($img_size < Config::MIN_IMGSIZE)
		throw new LimitException(LimitException::$messages['MIN_IMG_SIZE']);
}*/
/**
 * Удаляет заданную загрузку.
 * @param id string <p>Идентификатор загрузки.</p>
 */
/*function uploads_delete_by_id($id)
{
	db_uploads_delete_by_id(DataExchange::getDBLink(), $id);
}*/
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
/*function uploads_get_by_posts($posts)
{
	return db_uploads_get_by_posts(DataExchange::getDBLink(), $posts);
}*/
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
/*function uploads_get_dangling()
{
	return db_uploads_get_dangling(DataExchange::getDBLink());
}*/
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
/*function uploads_get_same($board_id, $hash, $user_id)
{
	return db_uploads_get_same(DataExchange::getDBLink(), $board_id, $hash,
		$user_id);
}*/
?>
