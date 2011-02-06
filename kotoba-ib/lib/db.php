<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

/**
 * Скрипт, предоставляющий прослойку из фукнций для фукнций работы с БД.
 * @package api
 */

/***/
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'errors.php'; })) {
    throw new Exception('Error handing file <b>errors.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}
require_once Config::ABS_PATH . '/lib/mysql.php';

/**********
 * Common *
 **********/

/**
 * Wrapper class to simplify get and release database connection.
 * @package database
 */
class DataExchange {

    /**
     * Link to database.
     */
    private static $link = null;

    /**
     * Returns link to database.
     */
    static function getDBLink() {
        if (self::$link == null) {
            self::$link = mysqli_connect(Config::DB_HOST, Config::DB_USER, Config::DB_PASS, Config::DB_BASENAME);
            if (!self::$link) {
                throw new CommonException(mysqli_connect_error());
            }
            if (!mysqli_set_charset(self::$link, Config::SQL_ENCODING)) {
                throw new CommonException(mysqli_error(self::$link));
            }
        }

        return self::$link;
    }

    /**
     * Release connection to database.
     */
    static function releaseResources() {
        if (self::$link != null && self::$link instanceof MySQLi) {
            mysqli_close(self::$link);
        }
    }
}
/**
 * Create directories requied to new board.
 * @param string $name New board name.
 * @return boolean
 * TRUE is directories was successfully created. FALSE otherwise.
 */
function create_directories($name) {
    $base = Config::ABS_PATH . "/$name";
    if(mkdir ($base)) {
        chmod ($base, 0777);
        foreach (array('arch', 'img', 'thumb', 'other') as $dir) {
            $subdir = "$base/$dir";
            if (mkdir($subdir)) {
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

/*******************************
 * Работа с избранными нитями. *
 *******************************/

/**
 * Adds thread to user's favorites.
 * @param string|int $user User id.
 * @param string|int $thread Thread id.
 */
function favorites_add($user, $thread) {
    db_favorites_add(DataExchange::getDBLink(), $user, $thread);
}

/**
 * Removes thread from user's favorites.
 * @param string|int $user User id.
 * @param string|int $thread Thread id.
 */
function favorites_delete($user, $thread) {
    db_favorites_delete(DataExchange::getDBLink(), $user, $thread);
}

/**
 * Get favorite threads.
 * @param int $user User id.
 * @return array
 * threads.
 */
function favorites_get_by_user($user) {
    return db_favorites_get_by_user(DataExchange::getDBLink(), $user);
}

/**
 * Mark thread as readed in user favorites. If thread is null then marks all
 * threads as readed.
 * @param int $user User id.
 * @param int|null $thread Thread id or NULL.
 */
function favorites_mark_readed($user, $thread = null) {
    db_favorites_mark_readed(DataExchange::getDBLink(), $user, $thread);
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
 * Удаляет связи заданного сообщения с вложениями.
 * @param string|int $post_id Идентификатор сообщения.
 */
function posts_attachments_delete_by_post($post_id) {
    db_posts_files_delete_by_post(DataExchange::getDBLink(), $post_id);
    db_posts_images_delete_by_post(DataExchange::getDBLink(), $post_id);
    db_posts_links_delete_by_post(DataExchange::getDBLink(), $post_id);
    db_posts_videos_delete_by_post(DataExchange::getDBLink(), $post_id);
}
/**
 * Удаляет связи сообщений с вложениями, помеченные на удаление.
 */
function posts_attachments_delete_marked() { // Java CC
    db_posts_files_delete_marked(DataExchange::getDBLink());
    db_posts_images_delete_marked(DataExchange::getDBLink());
    db_posts_links_delete_marked(DataExchange::getDBLink());
    db_posts_videos_delete_marked(DataExchange::getDBLink());
}
/**
 * Get posts attachments relations.
 * @param array $posts Posts.
 * @return array
 * posts attachments relations.
 */
function posts_attachments_get_by_posts($posts) {
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
 * Get attachments.
 * @param array $posts Posts.
 * @return array
 * attachments.
 */
function attachments_get_by_posts($posts) {
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
 * Получает вложения нити.
 * @param int|string $thread_id Идентификатор нити.
 * @return array
 * Возвращает вложения:<br>
 * 'id' - Идентификатор.<br>
 * ... - Атрибуты, зависящие от конкретного типа вложения.<br>
 * 'attachment_type' - Тип вложения.
 */
function attachments_get_by_thread($thread_id) { // Java CC
    $attachments = array();
    $tmp = null;

    $tmp = db_files_get_by_thread(DataExchange::getDBLink(), $thread_id);
    if (count($tmp) > 0) {
        $attachments = array_merge($attachments, $tmp);
    }
    $tmp = db_images_get_by_thread(DataExchange::getDBLink(), $thread_id);
    if (count($tmp) > 0) {
        $attachments = array_merge($attachments, $tmp);
    }
    $tmp = db_links_get_by_thread(DataExchange::getDBLink(), $thread_id);
    if (count($tmp) > 0) {
        $attachments = array_merge($attachments, $tmp);
    }
    $tmp = db_videos_get_by_thread(DataExchange::getDBLink(), $thread_id);
    if (count($tmp) > 0) {
        $attachments = array_merge($attachments, $tmp);
    }

    return $attachments;
}
/**
 * Получает висячие вложения.
 * @return array
 * Возвращает вложения:<br>
 * 'id' - Идентификатор.<br>
 * ... - Атрибуты, зависящие от конкретного типа вложения.<br>
 * 'attachment_type' - Тип вложения.
 */
function attachments_get_dangling() {
    $attachments = array();
    foreach (db_files_get_dangling(DataExchange::getDBLink()) as $file) {
        array_push($attachments, $file);
    }
    foreach (db_images_get_dangling(DataExchange::getDBLink()) as $image) {
        array_push($attachments, $image);
    }
    foreach (db_links_get_dangling(DataExchange::getDBLink()) as $link) {
        array_push($attachments, $link);
    }
    foreach (db_videos_get_dangling(DataExchange::getDBLink()) as $video) {
        array_push($attachments, $video);
    }
    return $attachments;
}
/**
 * Get same attachments (files and images).
 * @param int $board_id Board id.
 * @param int $user_id User id.
 * @param string $hash File hash.
 * @return array
 * attachments.
 */
function attachments_get_same($board_id, $user_id, $hash) {
	$attachments = array();

    $files = db_files_get_same(DataExchange::getDBLink(), $board_id, $user_id, $hash);
    $images = db_images_get_same(DataExchange::getDBLink(), $board_id, $user_id, $hash);

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
 * @param int $range_beg Начало диапазона IP-адресов.
 * @param int $range_end Конец диапазона IP-адресов.
 * @param string $reason Причина блокировки.
 * @param string $untill Время истечения блокировки.
 */
function bans_add($range_beg, $range_end, $reason, $untill) { // Java CC
    db_bans_add(DataExchange::getDBLink(), $range_beg, $range_end, $reason, $untill);
}
/**
 * Checks if IP-address banned.
 * @param int $ip IP-address.
 * @return boolean|array
 * Return FALSE if IP-address not banned. Otherwise return ban information.
 */
function bans_check($ip) {
    return db_bans_check(DataExchange::getDBLink(), $ip);
}
/**
 * Проверяет корректность начала диапазона IP-адресов.
 * @param string $range_beg Начало диапазона IP-адресов.
 * @return string Возвращает безопасное для использования начало диапазона
 * IP-адресов.
 */
function bans_check_range_beg($range_beg) {
    if ( ($range_beg = ip2long($range_beg)) == false) {
        throw new FormatException(FormatException::$messages['BANS_RANGE_BEG']);
    }
    return $range_beg;
}
/**
 * Проверяет корректность конца диапазона IP-адресов.
 * @param string $range_end Конец диапазона IP-адресов.
 * @return string Возвращает безопасный для использования конец диапазона
 * IP-адресов.
 */
function bans_check_range_end($range_end) {
    if ( ($range_end = ip2long($range_end)) == false) {
        throw new FormatException(FormatException::$messages['BANS_RANGE_END']);
    }
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
 * Возвращает слова:<br>
 * 'id' - идентификатор.<br>
 * 'board_id' - Идентификатор доски.<br>
 * 'word' - слово для замены.<br>
 * 'replace' - замена.
 */
function words_get_all() {
    return db_words_get_all(DataExchange::getDBLink());
}
/**
 * Get all words from wordfilter.
 * @param int $board_id Board id.
 * @return array
 * words.
 */
function words_get_all_by_board($board_id) {
    return db_words_get_all_by_board(DataExchange::getDBLink(), $board_id);
}

/*********************
 * Работа с досками. *
 *********************/

/**
 * Add board.
 * @param array $new_board Board.
 */
function boards_add($new_board) {
    db_boards_add(DataExchange::getDBLink(), $new_board);
}
/**
 * Check annotation.
 * @param string $annotation Annotation.
 * @return string|null
 * safe annotation or NULL if annotation is empty.
 */
function boards_check_annotation($annotation) {
    $annotation = htmlentities(kotoba_strval($annotation), ENT_QUOTES, Config::MB_ENCODING);
    $len = strlen($annotation);

    if ($len == 0) {
        return null;
    }
    if ($len > Config::MAX_ANNOTATION_LENGTH) {
        throw new LimitException(LimitException::$messages['MAX_ANNOTATION']);
    }

    return $annotation;
}
/**
 * Check bump limit.
 * @param int $bump_limit Bump limit.
 * @return int
 * safe bump limit.
 */
function boards_check_bump_limit($bump_limit) {
    if ( ($intval = kotoba_intval($bump_limit)) > 0) {
        return $intval;
    }

    throw new FormatException(FormatException::$messages['BOARD_BUMP_LIMIT']);
}
/**
 * Check default name.
 * @param string $name Default name.
 * @return string|null
 * safe default name or NULL if default name is empty.
 */
function boards_check_default_name($name) {
    $name = htmlentities(kotoba_strval($name), ENT_QUOTES, Config::MB_ENCODING);
    $l = strlen($name);

    if ($l == 0) {
        return NULL;
    }
    if ($l > Config::MAX_NAME_LENGTH) {
        throw new LimitException(LimitException::$messages['MAX_NAME_LENGTH']);
    }

	return $name;
}
/**
 * Проверяет корректность идентификатора доски.
 * @param mixed $id Идентификатор доски.
 * @return int
 * Возвращает безопасный для использования идентификатор доски.
 */
function boards_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check board name.
 * @param string $name Board name.
 * @return string
 * safe board name.
 */
function boards_check_name($name) {
    $name = kotoba_strval($name);
    $l = strlen($name);

    if ($l <= 16 && $l >= 1) {

        // Symbols must be digits 0-9 or latin letters a-z or A-Z.
        for ($i = 0; $i < $l; $i++) {
            $code = ord($name[$i]);
            if ($code < 0x30 || $code > 0x39 && $code < 0x41 || $code > 0x5A && $code < 0x61 || $code > 0x7A) {
                throw new FormatException(FormatException::$messages['BOARD_NAME']);
            }
        }
        return $name;
    }

    throw new FormatException(FormatException::$messages['BOARD_NAME']);
}
/**
 * Check upload policy from same files.
 * @param mixed $same_upload Upload policy from same files.
 * @return string
 * safe upload policy from same files.
 */
function boards_check_same_upload($same_upload) {
    $same_upload = kotoba_strval($same_upload);
    $l = strlen($same_upload);

    if ($l <= 32 && $l >= 1) {

        // Symbols must be latin letters a-z or A-Z.
        for ($i = 0; $i < $l; $i++) {
            $code = ord($same_upload[$i]);
            if ($code < 0x41 || $code > 0x5A && $code < 0x61 || $code > 0x7A) {
                throw new FormatException(FormatException::$messages['BOARD_SAME_UPLOAD']);
            }
        }
        return $same_upload;
    }

    throw new FormatException(FormatException::$messages['BOARD_SAME_UPLOAD']);
}
/**
 * Check board title.
 * @param mixed $title Board title.
 * @return string|null
 * safe board title or NULL if title is empty string.
 */
function boards_check_title($title) {
    $title = htmlentities(kotoba_strval($title), ENT_QUOTES, Config::MB_ENCODING);
    $l = strlen($title);

    if ($l == 0) {
        return null;
    }
    if ($l > 50) {
        throw new LimitException(LimitException::$messages['MAX_BOARD_TITLE']);
    }

	return $title;
}
/**
 * Deletes board.
 * @param int $id Board id.
 */
function boards_delete($id) {
    db_boards_delete(DataExchange::getDBLink(), $id);
}
/**
 * Edit board.
 * @param array $board Board.
 */
function boards_edit($board) {
    db_boards_edit(DataExchange::getDBLink(), $board);
}
/**
 * Get boards.
 * @return array
 * boards.
 */
function boards_get_all() {
    return db_boards_get_all(DataExchange::getDBLink());
}
/**
 * Get board.
 * @param int $board_id Board id.
 * @return array
 * board.
 */
function boards_get_by_id($board_id) {
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
 * Get changeable board.
 * @param int $board_id Board id.
 * @param int $user_id User id.
 * @return array
 * board.
 */
function boards_get_changeable_by_id($board_id, $user_id) {
    return db_boards_get_changeable_by_id(DataExchange::getDBLink(), $board_id, $user_id);
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
 * Returns boards visible to user.
 * @param int $user_id User id.
 * @return array
 * boards visible to user.
 */
function boards_get_visible($user_id) {
    return db_boards_get_visible(DataExchange::getDBLink(), $user_id);
}
/**
 * Получает доски, доступные для просмотра пользователю, и фильтрует их.
 * @param int $user_id Идентификатор пользователя.
 * @param Object $filter Фильтр (лямбда).
 * @return array
 * Возвращает доски.
 */
function boards_get_visible_filtred($user_id, $filter) { // Java CC
    $filtred_boards = array();
    $boards = db_boards_get_visible(DataExchange::getDBLink(), $user_id);

    /*
     * Аргументы для лямбды.
     * Пропустим первые два аргумента $user_id и $filter; индекс 0 в массиве
     * аргументов для лямбды зарезервирован.
     */
    $fargs = array_slice(func_get_args(), 2 - 1, func_num_args());

    foreach ($boards as $b) {
        $fargs[0] = $b;
		if (call_user_func_array($filter, $fargs)) {
			array_push($filtred_boards, $b);
        }
    }

    return $filtred_boards;
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
 * Check category id.
 * @param int $id Category id.
 * @return int
 * safe category id.
 */
function categories_check_id($id) {
    return kotoba_intval($id);
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
 * Get categories.
 * @return array
 * category.
 */
function categories_get_all() {
    return db_categories_get_all(DataExchange::getDBLink());
}

/* ********
 * Files. *
 **********/

/**
 * Add file.
 * @param string $hash Hash.
 * @param string $name Name.
 * @param int $size Size in bytes..
 * @param string $thumbnail Thumbnail.
 * @param int $thumbnail_w Thumbnail width.
 * @param int $thumbnail_h Thumbnail height.
 * @return int
 * added file id.
 */
function files_add($hash, $name, $size, $thumbnail, $thumbnail_w, $thumbnail_h) {
    return db_files_add(DataExchange::getDBLink(),
                        $hash,
                        $name,
                        $size,
                        $thumbnail,
                        $thumbnail_w,
                        $thumbnail_h);
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
/**
 * Получает группы, в которые входит пользователь.
 * @param int $user_id Идентификатор пользователя.
 * @return array
 * Возвращает группы:<br>
 * id - идентификатор.<br>
 * name - имя.
 */
function groups_get_by_user($user_id) {
    return db_groups_get_by_user(DataExchange::getDBLink(), $user_id);
}

/***********************************
  Работа с блокировками в фаерволе. 
 ***********************************/

/**
 * Блокирует диапазон IP-адресов в фаерволе.
 * @param string $range_beg Начало диапазона IP-адресов.
 * @param string $range_end Конец диапазона IP-адресов.
 */
function hard_ban_add($range_beg, $range_end) {
    db_hard_ban_add(DataExchange::getDBLink(), $range_beg, $range_end);
}

/*******************
 * Hidden threads. *
 *******************/

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
 * Get hidden threads and filter it.
 * @param array $boards Boards.
 * @param object $filter Filter functions.
 * @return array
 * hidden threads.
 */
function hidden_threads_get_filtred_by_boards($boards, $filter) {
    $threads = db_hidden_threads_get_by_boards(DataExchange::getDBLink(), $boards);

    $filter_args = array_slice(func_get_args(), 2 - 1, func_num_args());
    $filter_args[0] = NULL; // Reserved.

    $filtred_threads = array();
    foreach ($threads as $t) {
        $filter_args[0] = $t;
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

/* *********
 * Images. *
 ***********/

/**
 * Add image.
 * @param string|null $hash Hash.
 * @param string $name Name.
 * @param int $widht Width.
 * @param int $height Height.
 * @param int $size Size in bytes.
 * @param string $thumbnail Thumbnail.
 * @param int $thumbnail_w Thumbnail width.
 * @param int $thumbnail_h Thumbnail height.
 * @param boolean $spoiler Spoiler flag.
 * @return int
 * added image id.
 */
function images_add($hash,
                    $name,
                    $widht,
                    $height,
                    $size,
                    $thumbnail,
                    $thumbnail_w,
                    $thumbnail_h,
                    $spoiler) {

    return db_images_add(DataExchange::getDBLink(),
                         $hash,
                         $name,
                         $widht,
                         $height,
                         $size,
                         $thumbnail,
                         $thumbnail_w,
                         $thumbnail_h,
                         $spoiler);
}
/**
 * Check image size.
 * @param int $img_size image size.
 */
function images_check_size($size) {
    if ($size < Config::MIN_IMGSIZE) {
        throw new LimitException(LimitException::$messages['MIN_IMG_SIZE']);
    }
}
/**
 * Get images.
 * @param int $board_id Board id.
 * @return array
 * images.
 */
function images_get_by_board($board_id) {
    return db_images_get_by_board(DataExchange::getDBLink(), $board_id);
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
 * Check language id.
 * @param int $id Language id.
 * @return string
 * safe language id.
 */
function languages_check_id($id) {
    return kotoba_intval($id);
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
 * Get languages.
 * @return array
 * languages.
 */
function languages_get_all() {
    return db_languages_get_all(DataExchange::getDBLink());
}

/* ********
 * Links. *
 **********/

/**
 * Add link.
 * @param string $url URL.
 * @param int $widht Width.
 * @param int $height Height.
 * @param int $size Size in bytes.
 * @param string $thumbnail Thumbnail URL.
 * @param int $thumbnail_w Thumbnail width.
 * @param int $thumbnail_h Thumbnail height.
 * @return int
 * added link id.
 */
function links_add($url,
                   $widht,
                   $height,
                   $size,
                   $thumbnail,
                   $thumbnail_w,
                   $thumbnail_h) {

    return db_links_add(DataExchange::getDBLink(),
                        $url,
                        $widht,
                        $height,
                        $size,
                        $thumbnail,
                        $thumbnail_w,
                        $thumbnail_h);
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
 * Check if macrochan tag valid.
 * @param string $name Tag name.
 * @return string
 * safe macrochan tag name.
 */
function macrochan_tags_check($name) {
    $macrochan_tags = macrochan_tags_get_all();
    foreach ($macrochan_tags as $tag) {
        if ($tag['name'] === $name) {
            return $tag['name'];
        }
    }
    throw new FormatException(FormatException::$messages['MACROCHAN_TAG_NAME']);
}
/**
 * Удаляет тег по заданному имени.
 * @param string $name Имя.
 */
function macrochan_tags_delete_by_name($name) { // Java CC
    db_macrochan_tags_delete_by_name(DataExchange::getDBLink(), $name);
}
/**
 * Get macrochan tags.
 * @return array
 * macrochan tags.
 */
function macrochan_tags_get_all() {
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
function macrochan_images_add($name, $width, $height, $size, $thumbnail, $thumbnail_w, $thumbnail_h) { // Java CC
    db_macrochan_images_add(DataExchange::getDBLink(), $name, $width, $height, $size, $thumbnail, $thumbnail_w, $thumbnail_h);
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
 * Get random macrochan image.
 * @param string $name Tag name.
 * @return array
 * macrochan image.
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
 * Check popdown handler id.
 * @param int $id Popdown handler id.
 * @return int
 * popdown handler id.
 */
function popdown_handlers_check_id($id) {
    return kotoba_intval($id);
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
 * Get popdown hanglers.
 * @return array
 * popdown hanglers.
 */
function popdown_handlers_get_all() {
    return db_popdown_handlers_get_all(DataExchange::getDBLink());
}

/* ********
 * Posts. *
 **********/

/**
 * Add post.
 * @param int $board_id Board id.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 * @param string|null $password Password.
 * @param string|null $name Name.
 * @param string|null $tripcode Tripcode.
 * @param int $ip IP-address.
 * @param string|null $subject Subject.
 * @param string $date_time Date.
 * @param string|null $text Text.
 * @param int|null $sage Sage flag.
 * @return array
 * added post.
 */
function posts_add($board_id,
                   $thread_id,
                   $user_id,
                   $password,
                   $name,
                   $tripcode,
                   $ip,
                   $subject,
                   $date_time,
                   $text,
                   $sage) {

    return db_posts_add(DataExchange::getDBLink(),
                        $board_id,
                        $thread_id,
                        $user_id,
                        $password,
                        $name,
                        $tripcode,
                        $ip,
                        $subject,
                        $date_time,
                        $text,
                        $sage);
}
/**
 * Добавляет текст в конец текста заданного сообщения.
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param text string <p>Текст.</p>
 */
function posts_add_text_by_id($id, $text) {
	db_posts_add_text_by_id(DataExchange::getDBLink(), $id, $text);
}
/**
 * Проверяет корректность идентификатора сообщения.
 * @param mixed $id Идентификатор сообщения.
 * @return int
 * Возвращает безопасный для использования идентификатор сообщения.
 */
function posts_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check name length.
 * @param string $name Name.
 */
function posts_check_name_size($name) {
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
 * Check password.
 * @param string $password Password.
 * @return string
 * safe password.
 */
function posts_check_password($password) {
    $password = kotoba_strval($password);
    $l = strlen($password);

    // Password must be at 1 to 12 symbols.
    if ($l <= 12 && $l >= 1) {

        // Valid symbold is digits and latin letters.
        for ($i = 0; $i < $l; $i++) {
            $code = ord($password[$i]);
            if ($code < 0x30 || $code > 0x39 && $code < 0x41 || $code > 0x5A && $code < 0x61 || $code > 0x7A) {
                throw new FormatException(FormatException::$messages['POST_PASSWORD']);
            }
        }
        return $password;
    }

    throw new FormatException(FormatException::$messages['POST_PASSWORD']);
}
/**
 * Check subject size.
 * @param string $subject Subject.
 */
function posts_check_subject_size($subject) {
    if (strlen($subject) > Config::MAX_THEME_LENGTH) {
        throw new LimitException(LimitException::$messages['MAX_SUBJECT_LENGTH']);
    }
}
/**
 * Validate text.
 * @param string $text Text.
 */
function posts_check_text($text) {
    if (!check_utf8($text)) {
        throw new CommonException(CommonException::$messages['TEXT_UNICODE']);
    }
}
/**
 * Check text size.
 * @param string $text Text.
 */
function posts_check_text_size($text) {
    if (mb_strlen($text) > Config::MAX_MESSAGE_LENGTH) {
        throw new LimitException(LimitException::$messages['MAX_TEXT_LENGTH']);
    }
}
/**
 * Crop text.
 * TODO: Урезание в длину.
 * @param string $text Text to crop.
 * @param int $lines_per_post Count of lines what will not be cropped.
 * @param boolean $is_cropped Text was cropped or not.
 * @return string
 * cropped text.
 */
function posts_corp_text(&$text, $lines_per_post, &$is_cropped) {
    $lines = explode('<br>', $text);
    if (count($lines) > $lines_per_post) {
        $is_cropped = true;
        return implode('<br>', array_slice($lines, 0, $lines_per_post));
    } else {
        $is_cropped = false;
        return $text;
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
function posts_delete_marked() { // Java CC
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
 * Получает номера всех сообщений с соотвествующими номерами нитей и именами досок.
 * @return array
 * Возвращает сообщения:<br>
 * 'post' - Номер сообщения.<br>
 * 'thread' - Номер нити.<br>
 * 'board' - Имя доски.
 */
function posts_get_all_numbers() { // Java CC
    return db_posts_get_all_numbers(DataExchange::getDBLink());
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
 * @param array $boards Доски.
 * @param Object $filter Фильтр (лямбда).
 * @return array
 * Возвращает сообщения с разверунтыми данными о доске и нити.
 */
function posts_get_filtred_by_boards($boards, $filter) { // Java CC
    $posts = db_posts_get_by_boards(DataExchange::getDBLink(), $boards);
    $filtred_posts = array();
    $filter_args = array();
    $filter_argn = 0;
    $n = func_num_args();
    for ($i = 2; $i < $n; $i++) {   // Пропустим первые два аргумента фукнции.
        $filter_args[$filter_argn++] = func_get_arg($i);
    }
    foreach ($posts as $post) {
        $filter_args[$filter_argn] = $post;
        if (call_user_func_array($filter, $filter_args)) {
            array_push($filtred_posts, $post);
        }
    }
    return $filtred_posts;
}
/**
 * Получает сообщения, на которые поступила жалоба, с заданных досок.
 * @param array $boards Доски.
 * @return array
 * Возвращает сообщения:<br>
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
 * 'sage' - Флаг поднятия нити.
 */
function posts_get_reported_by_boards($boards) {
    return db_posts_get_reported_by_boards(DataExchange::getDBLink(), $boards);
}
/**
 * Получает заданное сообщение, доступное для просмотра заданному пользователю.
 * @param int $post_id Идентификатор сообщения.
 * @param int $user_id Идентификатор пользователя.
 * @return array
 * Возвращает сообщение с развернутыми данными о доске и нити.
 */
function posts_get_visible_by_id($post_id, $user_id) { // Java CC
    return db_posts_get_visible_by_id(DataExchange::getDBLink(), $post_id, $user_id);
}
/**
 * Get visible post.
 * @param string $board_name Board name.
 * @param int $post_number Post number.
 * @param int $user_id User id.
 * @return array
 * post.
 */
function posts_get_visible_by_number($board_name, $post_number, $user_id) {
    return db_posts_get_visible_by_number(DataExchange::getDBLink(), $board_name, $post_number, $user_id);
}
/**
 * Get posts visible to user and filter it.
 * @param array $threads Threads.
 * @param int $user_id User id.
 * @param Object $filter Filter function. First two arguments must be thread and post.
 * @return array
 * posts.
 */
function posts_get_visible_filtred_by_threads($threads, $user_id, $filter) {
    return db_posts_get_visible_filtred_by_threads(DataExchange::getDBLink(),
            $threads,
            $user_id,
            $filter,
            array_slice(func_get_args(), 3, func_num_args()));
}
/**
 * Cleanup and markup text.
 * @param string $text Text.
 * @param array $board Board.
 */
function posts_prepare_text(&$text, $board) {
    purify_ascii($text);
    kotoba_mark($text, $board);
    $text = str_replace("</blockquote>\n", '</blockquote>', $text);
    $text = str_replace("\n<blockquote", '<blockquote', $text);
    $text = preg_replace('/\n{3,}/', '\n', $text);
    $text = preg_replace('/\n/', '<br>', $text);
}
/**
 * Ищет в сообщениях досок заданную фразу.
 * @param array $boards Доски.
 * @param string $keyword Искомая фраза.
 * @param int $user Идентификатор пользователя.
 * @return array
 * Возвращает сообщения, с развёрнутыми данными о нити и доске.
 */
function posts_search_visible_by_boards($boards, $keyword, $user) {
    return db_posts_search_visible_by_boards(DataExchange::getDBLink(), $boards, $keyword, $user);
}

/* ************************
 * Posts files relations. *
 **************************/

/**
 * Add post file relation.
 * @param int $post Post id.
 * @param int file File id.
 * @param int $deleted Mark to delete.
 */
function posts_files_add($post, $file, $deleted) {
    db_posts_files_add(DataExchange::getDBLink(), $post, $file, $deleted);
}

/* *************************
 * Posts images relations. *
 ***************************/

/**
 * Add post image relation.
 * @param int $post Post id.
 * @param int $image Image id.
 * @param int $deleted Mark to delete.
 */
function posts_images_add($post, $image, $deleted) {
    db_posts_images_add(DataExchange::getDBLink(), $post, $image, $deleted);
}

/* ************************
 * Posts links relations. *
 **************************/

/**
 * Add post link relation.
 * @param int $post Post id.
 * @param int $link Link id.
 * @param int $deleted Mark to delete.
 */
function posts_links_add($post, $link, $deleted) {
    db_posts_links_add(DataExchange::getDBLink(), $post, $link, $deleted);
}

/* *************************
 * Posts videos relations. *
 ***************************/

/**
 * Add post video relation.
 * @param int $post Post id.
 * @param int $video Video id.
 * @param int $deleted Mark to delete.
 */
function posts_videos_add($post, $video, $deleted) {
    db_posts_videos_add(DataExchange::getDBLink(), $post, $video, $deleted);
}

/* ********************
 * Работа с жалобами. *
 **********************/

/**
 *
 */
function reports_add($post_id) {
    db_reports_add(DataExchange::getDBLink(), $post_id);
}
/**
 *
 */
function reports_delete($post_id) {
    db_reports_delete(DataExchange::getDBLink(), $post_id);
}
/**
 *
 */
function reports_get_all() {
    return db_reports_get_all(DataExchange::getDBLink());
}

/* *************************
 * Работа со спамфильтром. *
 ***************************/

/**
 * Добавляет шаблон в спамфильтр.
 * @param string $pattern Шаблон.
 */
function spamfilter_add($pattern) { // Java CC
	db_spamfilter_add(DataExchange::getDBLink(), $pattern);
}
/**
 * Проверяет корректность шаблона спамфильтра.
 * @return string Возвращает безопасный для использования шаблон спамфильтра.
 */
function spamfilter_check_pattern($pattern) {
    if (strlen($pattern) > 256) {
        throw new FormatException(FormatException::$messages['SPAMFILTER_PATTERN']);
    }
    return $pattern;
}
/**
 * Удаляет шаблон из спамфильтра.
 * @param int $id Идентификатор шаблона.
 */
function spamfilter_delete($id) { // Java CC
	db_spamfilter_delete(DataExchange::getDBLink(), $id);
}
/**
 * Get spamfilter records.
 * @return array
 * spamfilter records.
 */
function spamfilter_get_all() {
    return db_spamfilter_get_all(DataExchange::getDBLink());
}

/* ********************
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
 * Check stylesheet id.
 * @param int $id Stylesheet id.
 * @return int
 * safe stylesheet id.
 */
function stylesheets_check_id($id) {
    return kotoba_intval($id);
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
 * Get stylesheets.
 * @return array
 * stylesheets.
 */
function stylesheets_get_all() {
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
 * Возвращает нить:<br>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * Или null, если что-то пошло не так.
 */
function threads_add($board_id, $original_post, $bump_limit, $sage, $with_attachments) { // Java CC
    return db_threads_add(DataExchange::getDBLink(), $board_id, $original_post, $bump_limit, $sage, $with_attachments);
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
 * @param mixed $id Идентификатор нити.
 * @return int
 * Возвращает безопасный для использования идентификатор нити.
 */
function threads_check_id($id) { // Java CC
	return kotoba_intval($id);
}
/**
 * Check original post number.
 * @param int $original_post original post number.
 * @return int
 * safe original post number.
 */
function threads_check_original_post($original_post) {
    return kotoba_intval($original_post);
}
/**
 * Удаляет нити, помеченные на удаление.
 */
function threads_delete_marked() { // Java CC
    db_threads_delete_marked(DataExchange::getDBLink());
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
 * Получает нить по идентификатору.
 * @param int $id Идентификатор нити.
 * @return array
 * Возвращает нить с развернутыми данными о доске.
 */
function threads_get_by_id($id) { // Java CC
    return db_threads_get_by_id(DataExchange::getDBLink(), $id);
}
/**
 * Получает нить по номеру нити и идентификатору доски.
 * @param string|int $board Идентификатор доски.
 * @param string|int $original_post Номер нити.
 * @return array
 * Возвращает нить:<br>
 * 'id' - Идентификатор.<br>
 * 'board' - Идентификатор доски.<br>
 * 'original_post' - Номер оригинального сообщения.<br>
 * 'bump_limit' - Специфичный для нити бамплимит.<br>
 * 'archived' - Флаг архивирования.<br>
 * 'sage' - Флаг поднятия нити.<br>
 * 'sticky' - Флаг закрепления.<br>
 * 'with_attachments' - Флаг вложений.<br>
 * Или null, если нить не найдена, помечена на удаленение или архивирование.
 */
function threads_get_by_original_post($board, $original_post) {
    return db_threads_get_by_original_post(DataExchange::getDBLink(), $board, $original_post);
}
/**
 * Get changeable thread.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 * @return array
 * thread.
 */
function threads_get_changeable_by_id($thread_id, $user_id) {
    return db_threads_get_changeable_by_id(DataExchange::getDBLink(),
                                           $thread_id,
                                           $user_id);
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
 * Get moderatable thread.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 * @return mixed
 * thread or NULL if this thread is not moderatable for this user.
 */
function threads_get_moderatable_by_id($thread_id, $user_id) {
    return db_threads_get_moderatable_by_id(DataExchange::getDBLink(),
                                            $thread_id,
                                            $user_id);
}
/**
 * Get threads visible to user on specified board and filter it.
 * @param int $board_id Board id.
 * @param int $user_id User id.
 * @param Object $filter Filter function.
 * @return array
 * threads.
 */
function threads_get_visible_filtred_by_board($board_id, $user_id, $filter) {
    $filtred_threads = array();
    $threads = db_threads_get_visible_by_board(DataExchange::getDBLink(), $board_id, $user_id);

    /*
     * Arguments for filter.
     * Skip first three arguments of this function.
     * Index 0 in array of filter arguments is reseved.
     */
    $fargs = array_slice(func_get_args(), 3 - 1, func_num_args());
    $fargs[0] = NULL;

    foreach ($threads as $thread) {
        $fargs[0] = $thread;
		if (call_user_func_array($filter, $fargs)) {
			array_push($filtred_threads, $thread);
        }
    }

    return $filtred_threads;
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
 * Get visible threads.
 * @param int $board Board id.
 * @param int $original_post Original post number.
 * @param int $user_id User id.
 * @return array
 * threads.
 */
function threads_get_visible_by_original_post($board, $original_post, $user_id) {
    return db_threads_get_visible_by_original_post(DataExchange::getDBLink(),
                                                   $board,
                                                   $original_post,
                                                   $user_id);
}
/**
 * Calculate count of visible threads.
 * @param int $user_id User id.
 * @param int $board_id Board id.
 * @return string
 * count of visible threads.
 */
function threads_get_visible_count($user_id, $board_id) {
    return db_threads_get_visible_count(DataExchange::getDBLink(), $user_id, $board_id);
}
/**
 * Перемещает нить.
 * @param string|int $thread_id Идентификатор нити, которую нужно переместить.
 * @param string|int $board_id Идентификатор доски, на которую нужно переместить нить.
 */
function threads_move_thread($thread_id, $board_id) {
    db_threads_move_thread(DataExchange::getDBLink(), $thread_id, $board_id);
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
 * Get upload types on board.
 * @param int $board_id Board id.
 * @return array
 * upload types.
 */
function upload_types_get_by_board($board_id) {
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
 * Check redirection.
 * @param string $goto Redirection.
 * @return string
 * safe redirection.
 */
function users_check_goto($goto) {
    if ($goto === 'b' || $goto === 't') {
        return $goto;
    } else {
        throw new FormatException(FormatException::$messages['USER_GOTO']);
    }
}
/**
 * Проверяет корректность идентификатора пользователя.
 * @param mixed $id Идентификатор пользователя.
 * @return int
 * Возвращает безопасный для использования идентификатор пользователя.
 */
function users_check_id($id) { // Java CC
    return kotoba_intval($id);
}
/**
 * Check keyword.
 * @param string $keyword Keyword.
 * @return string
 * safe keyword.
 */
function users_check_keyword($keyword) {
    $keyword = kotoba_strval($keyword);
    $length = strlen($keyword);
    if ($length <= 32 && $length >= 2) {
        $keyword = RawUrlEncode($keyword);
        $length = strlen($keyword);
        if ($length > 32 || (strpos($keyword, '%') !== false) || $length < 2) {
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
function users_check_lines_per_post($lines_per_post) {
    $lines_per_post = kotoba_intval($lines_per_post);
    $length = strlen($lines_per_post);
    if ($length <= 2 && $length >= 1) {
        $lines_per_post = RawUrlEncode($lines_per_post);
        $length = strlen($lines_per_post);
        if($length > 2 || (ctype_digit($lines_per_post) === false) || $length < 1) {
            throw new FormatException(FormatException::$messages['USER_LINES_PER_POST'],
                                      Config::MIN_LINESPERPOST,
                                      Config::MAX_LINESPERPOST);
        }
    } else {
        throw new FormatException(FormatException::$messages['USER_LINES_PER_POST'],
                                      Config::MIN_LINESPERPOST,
                                      Config::MAX_LINESPERPOST);
    }

    return kotoba_intval($lines_per_post);
}
/**
 * Check count of posts per thread.
 * @param int $posts_per_thread Count of posts per thread.
 * @return int
 * safe count of posts per thread.
 */
function users_check_posts_per_thread($posts_per_thread) {
    $posts_per_thread = kotoba_intval($posts_per_thread);
    $length = strlen($posts_per_thread);
    if ($length <= 2 && $length >= 1) {
        $posts_per_thread = RawUrlEncode($posts_per_thread);
        $length = strlen($posts_per_thread);
        if($length > 2 || (ctype_digit($posts_per_thread) === false) || $length < 1) {
            throw new FormatException(FormatException::$messages['USER_POSTS_PER_THREAD'],
                                      Config::MIN_POSTSPERTHREAD,
                                      Config::MAX_POSTSPERTHREAD);
        }
    } else {
        throw new FormatException(FormatException::$messages['USER_POSTS_PER_THREAD'],
                                  Config::MIN_POSTSPERTHREAD,
                                  Config::MAX_POSTSPERTHREAD);
    }

    return kotoba_intval($posts_per_thread);
}
/**
 * Check count of threads per page.
 * @param int $threads_per_page Count of threads per page.
 * @return int
 * safe count of threads per page.
 */
function users_check_threads_per_page($threads_per_page) {
    $threads_per_page = kotoba_intval($threads_per_page);
    $length = strlen($threads_per_page);
    if ($length <= 2 && $length >= 1) {
        $threads_per_page = RawUrlEncode($threads_per_page);
        $length = strlen($threads_per_page);
        if ($length > 2 || (ctype_digit($threads_per_page) === false) || $length < 1) {
            throw new FormatException(FormatException::$messages['USER_THREADS_PER_PAGE'],
                                      Config::MIN_THREADSPERPAGE,
                                      Config::MAX_THREADSPERPAGE);
        }
    } else {
        throw new FormatException(FormatException::$messages['USER_THREADS_PER_PAGE'],
                                  Config::MIN_THREADSPERPAGE,
                                  Config::MAX_THREADSPERPAGE);
    }
    return kotoba_intval($threads_per_page);
}
/**
 * Edit user settings by keyword or create new user if it not exist.
 * @param string $keyword Keyword hash.
 * @param int|null $posts_per_thread Count of posts per thread or NULL.
 * @param int|null $threads_per_page Count of threads per page or NULL.
 * @param int|null $lines_per_post Count of lines per post or NULL.
 * @param int $language Language id.
 * @param int $stylesheet Stylesheet id.
 * @param string|null $password Password or NULL.
 * @param string|null $goto Redirection or NULL.
 */
function users_edit_by_keyword($keyword,
                               $posts_per_thread,
                               $threads_per_page,
                               $lines_per_post,
                               $language,
                               $stylesheet,
                               $password,
                               $goto) {

    db_users_edit_by_keyword(DataExchange::getDBLink(),
                             $keyword,
                             $posts_per_thread,
                             $threads_per_page,
                             $lines_per_post,
                             $language,
                             $stylesheet,
                             $password,
                             $goto);
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
 * Get admins.
 * @return array
 * admin users.
 */
function users_get_admins() {
    return db_users_get_admins(DataExchange::getDBLink());
}
/**
 * Load user settings.
 * @param string $keyword Keyword hash.
 */
function users_get_by_keyword($keyword) {
    return db_users_get_by_keyword(DataExchange::getDBLink(), $keyword);
}
/**
 * Set redirection.
 * @param int $id User id.
 * @param string $goto Redirection.
 */
function users_set_goto($id, $goto) {
    db_users_set_goto(DataExchange::getDBLink(), $id, $goto);
}
/**
 * Set password.
 * @param int $id User id.
 * @param string $password New password.
 */
function users_set_password($id, $password) {
    db_users_set_password(DataExchange::getDBLink(), $id, $password);
}

/* *********
 * Videos. *
 ***********/

/**
 * Add video.
 * @param string $code Code.
 * @param int $widht Width.
 * @param int $height Height.
 * @return int
 * added video id.
 */
function videos_add($code, $widht, $height) {
    return db_videos_add(DataExchange::getDBLink(), $code, $widht, $height);
}
/**
 * Check youtube video code.
 * @param string $code Code of vide.
 * @return string
 * safe code of vide.
 */
function videos_check_code($code) {
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
