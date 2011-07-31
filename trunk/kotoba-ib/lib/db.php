<?php
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

/**
 * Скрипт, предоставляющий прослойку из фукнций для фукнций работы с БД.
 * @package api
 */

/**
 *
 */
require_once Config::ABS_PATH . '/lib/errors.php';
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
            self::$link = mysqli_connect(Config::DB_HOST, Config::DB_USER,
                                         Config::DB_PASS, Config::DB_BASENAME);
            if (!self::$link) {
                throw new DBException(mysqli_connect_error());
            }
            if (!mysqli_set_charset(self::$link, Config::SQL_ENCODING)) {
                throw new DBException(mysqli_error(self::$link));
            }
        }

        return self::$link;
    }

    /**
     * Release connection to database.
     */
    static function releaseResources() {
        if (self::$link != NULL && self::$link instanceof MySQLi) {
            mysqli_close(self::$link);
            self::$link = NULL;
        }
    }

    /**
     * Escapes string to use in SQL statement.
     * @param string $s String to escape.
     * @return string Returns escaped string.
     */
    static function escapeString($s) {
        return addcslashes(
            mysqli_real_escape_string(DataExchange::getDBLink(), $s),
            '%_'
        );
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
 * Create necessary directories when new language adds.
 * @param string $code ISO_639-2 code.
 */
function create_language_directories($code) {
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

/* ************
 * Favorites. *
 **************/

/**
 * Adds thread to user's favorites.
 * @param int $user User id.
 * @param int $thread Thread id.
 */
function favorites_add($user, $thread) {
    db_favorites_add(DataExchange::getDBLink(), $user, $thread);
}

/**
 * Removes thread from user's favorites.
 * @param int $user User id.
 * @param int $thread Thread id.
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
function favorites_mark_readed($user, $thread = NULL) {
    db_favorites_mark_readed(DataExchange::getDBLink(), $user, $thread);
}

/* ******
 * ACL. *
 ********/

/**
 * Add rule to ACL.
 * @param int|null $group_id Group id.
 * @param int|null $board_id Board id.
 * @param int|null $thread_id Thread id.
 * @param int|null $post_id Post id.
 * @param boolean $view View permission.
 * @param boolean $change Change prmission.
 * @param boolean $moderate Moderate permission.
 */
function acl_add($group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate) {
    db_acl_add(DataExchange::getDBLink(), $group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate);
}
/**
 * Delete rule from ACL.
 * @param int|null $group_id Group id.
 * @param int|null $board_id Board id.
 * @param int|null $thread_id Thread id.
 * @param int|null $post_id Post id.
 */
function acl_delete($group_id, $board_id, $thread_id, $post_id) {
    db_acl_delete(DataExchange::getDBLink(), $group_id, $board_id, $thread_id, $post_id);
}
/**
 * Edit rule in ACL.
 * @param int|null $group_id Group id.
 * @param int|null $board_id Board id.
 * @param int|null $thread_id Thread id.
 * @param int|null $post_id Post id.
 * @param boolean $view View permission.
 * @param boolean $change Change prmission.
 * @param boolean $moderate Moderate permission.
 */
function acl_edit($group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate) {
    db_acl_edit(DataExchange::getDBLink(),
                $group_id,
                $board_id,
                $thread_id,
                $post_id,
                $view,
                $change,
                $moderate);
}
/**
 * Get ACL.
 * @return array
 * ACL.
 */
function acl_get_all() {
    return db_acl_get_all(DataExchange::getDBLink());
}

/* ****************************************************
 * Attachments (abtract of certain attachment types). *
 ******************************************************/

/**
 * Delete post attachments relations.
 * @param int $post_id Post id.
 */
function posts_attachments_delete_by_post($post_id) {
    db_posts_files_delete_by_post(DataExchange::getDBLink(), $post_id);
    db_posts_images_delete_by_post(DataExchange::getDBLink(), $post_id);
    db_posts_links_delete_by_post(DataExchange::getDBLink(), $post_id);
    db_posts_videos_delete_by_post(DataExchange::getDBLink(), $post_id);
}
/**
 * Delete marked posts attachemtns relations.
 */
function posts_attachments_delete_marked() {
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
        $_ = db_posts_files_get_by_post(DataExchange::getDBLink(), $post['id']);
        foreach ($_ as $post_file) {
            array_push($posts_attachments, $post_file);
        }
        $_ = db_posts_images_get_by_post(DataExchange::getDBLink(),
                                         $post['id']);
        foreach ($_ as $post_image) {
            array_push($posts_attachments, $post_image);
        }
        $_ = db_posts_links_get_by_post(DataExchange::getDBLink(), $post['id']);
        foreach ($_ as $post_link) {
            array_push($posts_attachments, $post_link);
        }
        $_ = db_posts_videos_get_by_post(DataExchange::getDBLink(),
                                         $post['id']);
        foreach ($_ as $post_video) {
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
        $_ = db_files_get_by_post(DataExchange::getDBLink(), $post['id']);
        foreach ($_ as $file) {
            array_push($attachments, $file);
        }
        $_ = db_images_get_by_post(DataExchange::getDBLink(), $post['id']);
        foreach ($_ as $image) {
            array_push($attachments, $image);
        }
        $_ = db_links_get_by_post(DataExchange::getDBLink(), $post['id']);
        foreach ($_ as $link) {
            array_push($attachments, $link);
        }
        $_ = db_videos_get_by_post(DataExchange::getDBLink(), $post['id']);
        foreach ($_ as $video) {
            array_push($attachments, $video);
        }
    }

    return $attachments;
}
/**
 * Get thread attachments.
 * @param int $thread_id Thread id.
 * @return array
 * attachments.
 */
function attachments_get_by_thread($thread_id) {
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
 * Get dangling attachments.
 * @return array
 * attachments.
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

/* *******
 * Bans. *
 *********/

/**
 * Ban IP-address range.
 * @param int $range_beg Begin of banned IP-address range.
 * @param int $range_end End of banned IP-address range.
 * @param string $reason Ban reason.
 * @param string $untill Expiration time.
 */
function bans_add($range_beg, $range_end, $reason, $untill) {
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
 * Check begining of IP-address range.
 * @param string $range_beg Begining of IP-address range.
 * @return string
 * safe begining of IP-address range.
 */
function bans_check_range_beg($range_beg) {
    if ( ($range_beg = ip2long($range_beg)) == false) {
        throw new FormatException($EXCEPTIONS['BANS_RANGE_BEG']());
    }
    return $range_beg;
}
/**
 * Check ending of IP-address range.
 * @param string $range_beg Ending of IP-address range.
 * @return string|boolean Returns safe ending of IP-address range or boolean
 * FALSE if any error occurred and set last error to appropriate error object.
 */
function bans_check_range_end($range_end) {
    if ( ($range_end = ip2long($range_end)) == false) {
        kotoba_set_last_error(new RangeEndError());
        return FALSE;
    }
    return $range_end;
}
/**
 * Check ban reason.
 * @param string $reason Ban reason.
 * @return string|boolean Returs safe reason or boolean FALSE if any error
 * occured and set last error to appropriate error object.
 */
function bans_check_reason($reason) {
    $length = strlen($reason);
    if ($length <= 10000 && $length >= 1) {
        $reason = htmlentities($reason, ENT_QUOTES, Config::MB_ENCODING);
        $length = strlen($reason);
        if ($length > 10000 || $length < 1) {
            kotoba_set_last_error(new BansReasonError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new BansReasonError());
        return FALSE;
    }
    return $reason;
}
/**
 * Check ban expiration time.
 * @param mixed $untill Ban expiration.
 * @return string
 * safe ban expiration time.
 */
function bans_check_untill($untill) {
    return kotoba_intval($untill);
}
/**
 * Delete ban.
 * @param int $id Id.
 */
function bans_delete_by_id($id) {
    db_bans_delete_by_id(DataExchange::getDBLink(), $id);
}
/**
 * Delete certain ip bans.
 * @param int $ip IP-address.
 */
function bans_delete_by_ip($ip) {
    db_bans_delete_by_ip(DataExchange::getDBLink(), $ip);
}
/**
 * Get bans.
 * @return array
 * bans.
 */
function bans_get_all() {
    return db_bans_get_all(DataExchange::getDBLink());
}

/* *********************
 * Board upload types. *
 ***********************/

/**
 * Add board upload type relation.
 * @param int $board Board id.
 * @param int $upload_type Upload type id.
 */
function board_upload_types_add($board, $upload_type) {
    db_board_upload_types_add(DataExchange::getDBLink(), $board, $upload_type);
}
/**
 * Delete board upload type relation.
 * @param int $board Board id.
 * @param int $upload_type Upload type id.
 */
function board_upload_types_delete($board, $upload_type) {
    db_board_upload_types_delete(DataExchange::getDBLink(), $board, $upload_type);
}
/**
 * Get board upload types relations.
 * @return array
 * board upload types relations.
 */
function board_upload_types_get_all() {
    return db_board_upload_types_get_all(DataExchange::getDBLink());
}

/* ********
 * Words. *
 **********/

/**
 * Add word.
 * @param int $board_id Board id.
 * @param string $word Word.
 * @param string $replace Replacement.
 */
function words_add($board_id, $word, $replace) {
    db_words_add(DataExchange::getDBLink(), $board_id, $word, $replace);
}
/**
 * Check word.
 * @param string $word Word.
 * @return string|int Returns safe word or integer error code. Error codes: 1 -
 * word too long.
 */
function words_check_word($word) {
    $word = DataExchange::escapeString($word);
    if (strlen($word) > 100) {
        return 1;
    }
    return $word;
}
/**
 * Delete word.
 * @param int $id Id.
 */
function words_delete($id) {
    db_words_delete(DataExchange::getDBLink(), $id);
}
/**
 * Edit word.
 * @param int $id Id.
 * @param string $word Word.
 * @param string $replace Replacement.
 */
function words_edit($id, $word, $replace) {
    db_words_edit(DataExchange::getDBLink(), $id, $word, $replace);
}
/**
 * Get words.
 * @return array
 * words.
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
 * @return string|null|int Returns safe annotation or NULL if annotation is
 * empty. If any error occurred returns integer error value. Error values: 1 -
 * annotation too long.
 */
function boards_check_annotation($annotation) {
    $annotation = htmlentities(kotoba_strval($annotation), ENT_QUOTES, Config::MB_ENCODING);
    $len = strlen($annotation);

    if ($len == 0) {
        return null;
    }
    if ($len > Config::MAX_ANNOTATION_LENGTH) {
        return 1;
    }

    return $annotation;
}
/**
 * Check bump limit.
 * @param int $bump_limit Bump limit.
 * @return int|boolean Returns safe bump limit or boolean FALSE if any error
 * occuread and set last kotoba error to apropriate error object.
 */
function boards_check_bump_limit($bump_limit) {
    if ( ($intval = kotoba_intval($bump_limit)) > 0) {
        return $intval;
    } else {
        kotoba_set_last_error(new BumpLimitError());
        return FALSE;
    }
}
/**
 * Check default name.
 * @param string $name Default name.
 * @return string|null|int Returns safe default name or NULL if default name is
 * empty. If any error occurred returns integer error value. Error values: 1 -
 * name too long.
 */
function boards_check_default_name($name) {
    $name = htmlentities(kotoba_strval($name), ENT_QUOTES, Config::MB_ENCODING);
    $l = strlen($name);

    if ($l == 0) {
        return NULL;
    }
    if ($l > Config::MAX_NAME_LENGTH) {
        return 1;
    }

	return $name;
}
/**
 * Check board id.
 * @param mixed $id Board id.
 * @return int
 * safe board id.
 */
function boards_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check board name.
 * @param string $name Board name.
 * @return string|boolean Returns safe board name or boolean FALSE if any error
 * occurred and set last error to appropriate error object.
 */
function boards_check_name($name) {
    $name = kotoba_strval($name);
    $l = strlen($name);
    if ($l > 16 || $l < 1 || preg_match('/^[0-9a-zA-Z]+$/', $name) !== 1) {
        kotoba_set_last_error(new BoardNameError());
        $name = FALSE;
    }

    return $name;
}
/**
 * Check upload policy for same files.
 * @param mixed $same_upload Upload policy for same files.
 * @return string|boolean Returns safe upload policy for same files or boolean
 * FALSE if any error occurred and set last error to appropriate error object.
 */
function boards_check_same_upload($same_upload) {
    $same_upload = kotoba_strval($same_upload);
    $l = strlen($same_upload);

    if ($l <= 32 && $l >= 1) {

        // Symbols must be latin letters a-z or A-Z.
        for ($i = 0; $i < $l; $i++) {
            $code = ord($same_upload[$i]);
            if ($code < 0x41 || $code > 0x5A && $code < 0x61 || $code > 0x7A) {
                kotoba_set_last_error(new SameUploadsError());
                return FALSE;
            }
        }
        return $same_upload;
    }

    kotoba_set_last_error(new SameUploadsError());
    return FALSE;
}
/**
 * Check board title.
 * @param mixed $title Board title.
 * @return string|null|int Returns safe board title or NULL if title is empty
 * string. If any error occurred returs integer error value. Error values: 1 -
 * board title too long.
 */
function boards_check_title($title) {
    $title = htmlentities(kotoba_strval($title), ENT_QUOTES, Config::MB_ENCODING);
    $l = strlen($title);

    if ($l == 0) {
        return null;
    }
    if ($l > 50) {
        return 1;
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
 * @return int|array Returns array of board data or integer error value. Error
 * values is: 1 if user have no permissions to change board, 2 if board not
 * found.
 */
function boards_get_changeable_by_id($board_id, $user_id) {
    return db_boards_get_changeable_by_id(DataExchange::getDBLink(), $board_id, $user_id);
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
function boards_get_moderatable($user_id) {
    return db_boards_get_moderatable(DataExchange::getDBLink(), $user_id);
}
/**
 * Returns boards visible to user.
 * @param int $user_id User id.
 * @return array Boards visible to user.
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

/* *************
 * Categories. *
 ***************/

/**
 * Add category.
 * @param string $name Name.
 */
function categories_add($name) {
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
 * Check category name.
 * @param mixed $name Name.
 * @return string|boolean Returns safe name or boolean FALSE if any error
 * occurred and set last error to appropriate error object.
 */
function categories_check_name($name) {
    $length = strlen($name);
    if ($length <= 50 && $length >= 1) {
        $name = RawUrlEncode($name);
        $length = strlen($name);
        if ($length > 50 || (strpos($name, '%') !== false) || $length < 1) {
            kotoba_set_last_error(new CategoryNameError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new CategoryNameError());
        return FALSE;
    }

    return $name;
}
/**
 * Delete category.
 * @param int $id Id.
 */
function categories_delete($id) {
    db_categories_delete(DataExchange::getDBLink(), $id);
}
/**
 * Get categories.
 * @return array Category.
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

/* *********
 * Groups. *
 ***********/

/**
 * Add group.
 * @param string $name Group name.
 * @return int
 * new group id.
 */
function groups_add($name) {
    db_groups_add(DataExchange::getDBLink(), $name);
}
/**
 * Check group id.
 * @param mixed $id Group id.
 * @return int
 * safe group id.
 */
function groups_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check group name.
 * @param mixed $name Group name.
 * @return string|boolean Returns safe group name or boolean FALSE if any error
 * occurred and set last error to appropriate error object.
 */
function groups_check_name($name) {
    $length = strlen($name);
    if ($length <= 50 && $length >= 1) {
        $name = RawUrlEncode($name);
        $length = strlen($name);
        if ($length > 50 || (strpos($name, '%') !== false) || $length < 1) {
            kotoba_set_last_error(new GroupNameError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new GroupNameError());
        return FALSE;
    }

    return $name;
}
/**
 * Delete groups.
 * @param array $groups Groups.
 */
function groups_delete($groups) {
    db_groups_delete(DataExchange::getDBLink(), $groups);
}
/**
 * Get groups.
 * @return array
 * groups.
 */
function groups_get_all() {
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

/* ************
 * Hard bans. *
 **************/

/**
 * Ban IP-address range in firewall.
 * @param string $range_beg Begin of banned IP-address range.
 * @param string $range_end End of banned IP-address range.
 */
function hard_ban_add($range_beg, $range_end) {
    db_hard_ban_add(DataExchange::getDBLink(), $range_beg, $range_end);
}

/* *****************
 * Hidden threads. *
 *******************/

/**
 * Hide thread.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 */
function hidden_threads_add($thread_id, $user_id) {
    return db_hidden_threads_add(DataExchange::getDBLink(), $thread_id, $user_id);
}
/**
 * Unhide thread.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 */
function hidden_threads_delete($thread_id, $user_id) {
    return db_hidden_threads_delete(DataExchange::getDBLink(), $thread_id, $user_id);
}
/**
 * Get hidden threads and filter it.
 * @param array $boards Boards.
 * @param object $filter Filter functions.
 * @return array
 * hidden threads.
 */
function hidden_threads_get_filtred_by_boards($boards, $filter) {
    $threads = db_hidden_threads_get_by_boards(DataExchange::getDBLink(),
                                               $boards);

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
 * @return int Return 0 on success or error code. Error codes: 1 - image too
 * small.
 */
function images_check_size($size) {
    if ($size < Config::MIN_IMGSIZE) {
        return 1;
    }

    return 0;
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

/* ************
 * Languages. *
 **************/

/**
 * Add language.
 * @param string $code ISO_639-2 code.
 */
function languages_add($code) {
    db_languages_add(DataExchange::getDBLink(), $code);
}
/**
 * Check language ISO_639-2 code.
 * @param mixed $code ISO_639-2 code.
 * @return string|boolean Returns safe ISO_639-2 code or boolean FALSE if any
 * error occurred and set last error to appropriate object.
 */
function languages_check_code($code) {
    $length = strlen($code);
    if ($length == 3) {
        $code = RawUrlEncode($code);
        $length = strlen($code);
        if ($length != 3 || (strpos($code, '%') !== FALSE)) {
            kotoba_set_last_error(new LanguageCodeError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new LanguageCodeError());
        return FALSE;
    }

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
 * Delete language.
 * @param int $id Id.
 */
function languages_delete($id) {
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

/* *****************
 * Macrochan tags. *
 *******************/

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
 * @return string|boolean Returns safe macrochan tag name or boolean FALSE if
 * any error occurred and set last error to appropriate error object.
 */
function macrochan_tags_check($name) {
    $macrochan_tags = macrochan_tags_get_all();
    foreach ($macrochan_tags as $tag) {
        if ($tag['name'] === $name) {
            return $tag['name'];
        }
    }

    kotoba_set_last_error(new MacrochanTagNameError());
    return FALSE;
}
/**
 * Delete tag.
 * @param string $name Tag name.
 */
function macrochan_tags_delete_by_name($name) {
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

/* *******************
 * Macrochan images. *
 *********************/

/**
 * Add macrochan image.
 * @param string $name Name.
 * @param int $width Width.
 * @param int $height Height.
 * @param int $size Size in bytes.
 * @param string $thumbnail Thumbnail.
 * @param int $thumbnail_w Thumbnail width.
 * @param int $thumbnail_h Thumbnail height.
 */
function macrochan_images_add($name, $width, $height, $size, $thumbnail, $thumbnail_w, $thumbnail_h) {
    db_macrochan_images_add(DataExchange::getDBLink(),
                            $name,
                            $width,
                            $height,
                            $size,
                            $thumbnail,
                            $thumbnail_w,
                            $thumbnail_h);
}
/**
 * Delete macrochan image.
 * @param string $name Image name.
 */
function macrochan_images_delete_by_name($name) {
    db_macrochan_images_delete_by_name(DataExchange::getDBLink(), $name);
}
/**
 * Get macrochan images.
 * @return array
 * macrochan images.
 */
function macrochan_images_get_all() {
    return db_macrochan_images_get_all(DataExchange::getDBLink());
}
/**
 * 
 */
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

/* **********************************
 * Macrochan tags images relations. *
 ************************************/

/**
 * Add tag image relation.
 * @param string $tag_name Macrochan tag name.
 * @param string $image_name Macrochan image name.
 */
function macrochan_tags_images_add($tag_name, $image_name) {
    db_macrochan_tags_images_add(DataExchange::getDBLink(), $tag_name, $image_name);
}
/**
 * Get tag image relation.
 * @param string $tag_name Macrochan tag name.
 * @param string $image_name Macrochan image name.
 * @return array|null
 * tag image relation or NULL if it not exist.
 */
function macrochan_tags_images_get($tag_name, $image_name) {
    return db_macrochan_tags_images_get(DataExchange::getDBLink(), $tag_name, $image_name);
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

/* *******************
 * Popdown handlers. *
 *********************/

/**
 * Add popdown handeler.
 * @param string $name Popdown handeler name.
 */
function popdown_handlers_add($name) {
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
 * Check popdown handler name.
 * @param mixed $name Popdown handler name.
 * @return string|boolean Returns safe popdown handler name or boolean FALSE if
 * any error occurred and set last error to appropriate error object.
 */
function popdown_handlers_check_name($name) {
    $length = strlen($name);
    if ($length <= 50 && $length >= 1) {
        $name = RawUrlEncode($name);
        $length = strlen($name);
        if ($length > 50 || (strpos($name, '%') !== false) || $length < 1 || ctype_digit($name[0])) {
            kotoba_set_last_error(new PopdownHandlerNameError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new PopdownHandlerNameError());
        return FALSE;
    }

    return $name;
}
/**
 * Delete popdown handeler.
 * @param int $id Id.
 */
function popdown_handlers_delete($id) {
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
 * Add text to the end of message.
 * @param int $id Post id.
 * @param string $text Text.
 */
function posts_add_text_by_id($id, $text) {
    db_posts_add_text_by_id(DataExchange::getDBLink(), $id, $text);
}
/**
 * Check post id.
 * @param mixed $id Post id.
 * @return int
 * safe post id.
 */
function posts_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check name length.
 * @param string $name Name.
 * @return int Returns 0 on success or error value. Error values: 1 - name too
 * long.
 */
function posts_check_name_size($name) {
    if (strlen($name) > Config::MAX_THEME_LENGTH) {
        return 1;
    }

    return 0;
}
/**
 * Check post number.
 * @param mixed $number Post number.
 * @return string
 * safe post number.
 */
function posts_check_number($number) {
    return kotoba_intval($number);
}
/**
 * Check password.
 * @param string $password Password.
 * @return string|boolean Returns safe password or
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
                kotoba_set_last_error(new PostPasswordError());
                return FALSE;
            }
        }
        return $password;
    }

    kotoba_set_last_error(new PostPasswordError());
    return FALSE;
}
/**
 * Check subject size.
 * @param string $subject Subject.
 * @return int Reutrn 0 on success or error value. Error values: 1 - subject too
 * long.
 */
function posts_check_subject_size($subject) {
    if (strlen($subject) > Config::MAX_THEME_LENGTH) {
        return 1;
    }

    return 0;
}
/**
 * Validate text.
 * @param string $text Text.
 */
function posts_check_text($text) {
    if (!check_utf8($text)) {
        return FALSE;
    } else {
        return TRUE;
    }
}
/**
 * Check text size.
 * @param string $text Text.
 * @return int Reutrn 0 on success or error value. Error values: 1 - text too
 * long.
 */
function posts_check_text_size($text) {
    if (mb_strlen($text) > Config::MAX_MESSAGE_LENGTH) {
        return 1;
    }

    return 0;
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
 * Remove post.
 * @param int $id Post id.
 */
function posts_delete($id) {
    db_posts_delete(DataExchange::getDBLink(), $id);
}
/**
 * Delete last posts.
 * @param int $id Post id.
 * @param string $date_time Date.
 */
function posts_delete_last($id, $date_time) {
    db_posts_delete_last(DataExchange::getDBLink(), $id, $date_time);
}
/**
 * Delete marked posts.
 */
function posts_delete_marked() {
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
 * Get posts.
 * @param array $boards Boards.
 * @return array
 * posts.
 */
function posts_get_by_boards($boards) {
    return db_posts_get_by_boards(DataExchange::getDBLink(), $boards);
}
/**
 * Get posts by boards posted after date.
 * @param array $boards Boards.
 * @param int $date_time Date time.
 * @param int $page Page number.
 * @param int $posts_per_page Count of posts per page.
 * @return array
 * int count - total count of posts posted after date.
 * array posts - posts.
 */
function posts_get_by_boards_datetime($boards, $date_time, $page, $posts_per_page) {
    return db_posts_get_by_boards_datetime(DataExchange::getDBLink(),
                                           $boards,
                                           $date_time,
                                           $page,
                                           $posts_per_page);
}
/**
 * Get posts by boards limited by ip.
 * @param array $boards Boards.
 * @param int $ip IP-address.
 * @param int $page Page number.
 * @param int $posts_per_page Count of posts per page.
 * @return array
 * int count - total count of posts limited by number.
 * array posts - posts.
 */
function posts_get_by_boards_ip($boards, $ip, $page, $posts_per_page) {
    return db_posts_get_by_boards_ip(DataExchange::getDBLink(),
                                     $boards,
                                     $ip,
                                     $page,
                                     $posts_per_page);
}
/**
 * Get posts by boards limited by number.
 * @param array $boards Boards.
 * @param int $number Post number.
 * @param int $page Page number.
 * @param int $posts_per_page Count of posts per page.
 * @return array
 * int count - total count of posts limited by number.
 * array posts - posts.
 */
function posts_get_by_boards_number($boards, $number, $page, $posts_per_page) {
    return db_posts_get_by_boards_number(DataExchange::getDBLink(),
                                         $boards,
                                         $number,
                                         $page,
                                         $posts_per_page);
}
/**
 * Get posts by its ids.
 * @param array $ids Ids of posts.
 * @return array posts.
 */
function posts_get_by_ids($ids) {
    return db_posts_get_by_ids(DataExchange::getDBLink(), $ids);
}
/**
 * Get post by number and board name.
 * @param string $board_name Board name.
 * @param int $post_number Post number.
 * @return array post.
 */
function posts_get_by_number($board_name, $post_number) {
    return db_posts_get_by_number(DataExchange::getDBLink(), $board_name,
                                  $post_number);
}
/**
 * Get posts.
 * @param int $thread_id Thread id.
 * @return array
 * posts.
 */
function posts_get_by_thread($thread_id) {
    return db_posts_get_by_thread(DataExchange::getDBLink(), $thread_id);
}
/**
 * Get visible posts and filter it.
 * @param array $boards Boards.
 * @param Object $filter Filter function. First argument is post.
 * @return array
 * posts.
 */
function posts_get_filtred_by_boards($boards, $filter) {
    $posts = db_posts_get_by_boards(DataExchange::getDBLink(), $boards);
    $filtred_posts = array();
    $filter_args = array_slice(func_get_args(), 2 - 1, func_num_args());
    $filter_args[0] = NULL;
    foreach ($posts as $post) {
        $filter_args[0] = $post;
        if (call_user_func_array($filter, $filter_args)) {
            array_push($filtred_posts, $post);
        }
    }
    return $filtred_posts;
}
/**
 * Get original posts of threads.
 * @param array $threads Threads.
 * @return array Posts.
 */
function posts_get_original_by_threads($threads) {
    return db_posts_get_original_by_threads(DataExchange::getDBLink(),
                                            $threads);
}
/**
 * Get reported posts.
 * @param array $boards Boards.
 * @param int $page Page number.
 * @param int $posts_per_page Count of posts per page.
 * @return array
 * int count - total count of reported posts.
 * array posts - posts.
 */
function posts_get_reported_by_boards($boards, $page, $posts_per_page) {
    return db_posts_get_reported_by_boards(DataExchange::getDBLink(),
                                           $boards,
                                           $page,
                                           $posts_per_page);
}
/**
 * Get visible post.
 * @param int $post_id Post id.
 * @param int $user_id User id.
 * @return array
 * post.
 */
function posts_get_visible_by_id($post_id, $user_id) {
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
 * Get visible posts.
 * @param int $board_id Boards.
 * @param array $threads Threads.
 * @param int $user_id User id.
 * @param int $posts_per_thread Count of posts per thread.
 * @return array
 * posts.
 */
function posts_get_visible_by_threads_preview($board_id, &$threads, $user_id,
                                              $posts_per_thread) {

    $posts = array();

    foreach ($threads as &$thread) {
        $_ = db_posts_get_visible_by_thread_preview(DataExchange::getDBLink(),
                                                    $board_id,
                                                    $thread['id'],
                                                    $user_id,
                                                    $posts_per_thread);
        if (isset($_[0])) {
            $thread['posts_count'] = $_[0]['thread']['posts_count'];
        } else {
            echo "Warning. Thread {$thread['id']} without any posts?<br>\n";
        }

        foreach ($_ as $post) {
            array_push($posts, $post);
        }
    }

    return $posts;
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
    return db_posts_get_visible_filtred_by_threads(
        DataExchange::getDBLink(),
        $threads,
        $user_id,
        $filter,
        array_slice(func_get_args(), 3, func_num_args())
    );
}
/**
 * Check if author of post is admin.
 * @param int $id Post author id.
 * @return boolean Returns TRUE if author of post is admin and FALSE otherwise.
 */
function posts_is_author_admin($id) {
    static $admins = NULL;

    if ($admins == NULL) {
        $admins = users_get_admins();
    }

    foreach ($admins as $admin) {
        if ($admin['id'] == $id) {
            return TRUE;
        }
    }

    return FALSE;
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
 * Search posts by keyword.
 * @param array $boards Boards.
 * @param string $keyword Keyword.
 * @param int $user User id.
 * @return array
 * posts.
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
 * @param int $board Board id.
 * @param int file File id.
 * @param int $deleted Mark to delete.
 */
function posts_files_add($post, $board, $file, $deleted) {
    db_posts_files_add(DataExchange::getDBLink(), $post, $board, $file,
                       $deleted);
}

/* *************************
 * Posts images relations. *
 ***************************/

/**
 * Add post image relation.
 * @param int $post Post id.
 * @param int $board Board id.
 * @param int $image Image id.
 * @param int $deleted Mark to delete.
 */
function posts_images_add($post, $board, $image, $deleted) {
    db_posts_images_add(DataExchange::getDBLink(), $post, $board, $image,
                        $deleted);
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

/* **********
 * Reports. *
 ************/

/**
 * Add report.
 * @param int $post_id Post id.
 */
function reports_add($post_id) {
    db_reports_add(DataExchange::getDBLink(), $post_id);
}
/**
 * Delete report.
 * @param int $post_id Post id.
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

/* *************
 * Spamfilter. *
 ***************/

/**
 * Add pattern to spamfilter.
 * @param string $pattern Pattern.
 */
function spamfilter_add($pattern) {
    db_spamfilter_add(DataExchange::getDBLink(), $pattern);
}
/**
 * Check spamfilter pattern.
 * @param mixed $pattern Pattern.
 * @return string|boolean Returns safe pattern or boolean FALSE if any error
 * occurred and set last error to appropriate error object.
 */
function spamfilter_check_pattern($pattern) {
    $pattern = DataExchange::escapeString($pattern);
    if (strlen($pattern) > 256) {
        kotoba_set_last_error(new SpamfilterPatternError());
        return FALSE;
    }

    return $pattern;
}
/**
 * Delete pattern from spamfilter.
 * @param int $id id.
 */
function spamfilter_delete($id) {
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

/* **************
 * Stylesheets. *
 ****************/

/**
 * Add stylesheet.
 * @param string $name Stylesheet name.
 */
function stylesheets_add($name) {
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
 * Check stylesheet name.
 * @param mixed $name Stylesheet name.
 * @return string|boolean Return safe stylesheet name or boolean FALSE if any
 * error occuerred and set last error to appropriate error object.
 */
function stylesheets_check_name($name) {
    $length = strlen($name);
    if ($length <= 50 && $length >= 1) {
        $name = RawUrlEncode($name);
        $length = strlen($name);
        if ($length > 50 || (strpos($name, '%') !== false) || $length < 1) {
            kotoba_set_last_error(new StylesheetNameError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new StylesheetNameError());
        return FALSE;
    }

    return $name;
}
/**
 * Delete stylesheet.
 * @param int $id Id.
 */
function stylesheets_delete($id) {
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
 * Check thread specific bumplimit.
 * @param mixed $bump_limit Thread specific bumplimit.
 * @return int
 * safe thread specific bumplimit.
 */
function threads_check_bump_limit($bump_limit) {
    return kotoba_intval($bump_limit);
}
/**
 * Check thread id.
 * @param mixed $id Thread id.
 * @return int
 * thread id.
 */
function threads_check_id($id) {
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
 * Delete marked threads.
 */
function threads_delete_marked() {
    db_threads_delete_marked(DataExchange::getDBLink());
}
/**
 * Edit thread.
 * @param int $thread_id Thread id.
 * @param int $bump_limit Thread specific bumplimit.
 * @param boolean $sage Sage flag.
 * @param boolean $sticky Sticky flag.
 * @param boolean $with_attachments Attachments flag.
 * @param boolean $closed Thread closed flag.
 */
function threads_edit($thread_id, $bump_limit, $sticky, $sage, $with_attachments, $closed) {
    db_threads_edit(DataExchange::getDBLink(), $thread_id, $bump_limit, $sticky, $sage, $with_attachments, $closed);
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
 * Get threads.
 * @param int $page Page number. Default value is 1.
 * @param int $threads_per_page Count of thread per page. Default value is 100.
 * @return array
 * 0 int - total count of threads.
 * 1 array - threads.
 */
function threads_get_all($page = 1, $threads_per_page = 100) {
    return db_threads_get_all(DataExchange::getDBLink(),
                              $page,
                              $threads_per_page);
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
 * Get thread.
 * @param int $id Thread id.
 * @return array
 * thread.
 */
function threads_get_by_id($id) {
    return db_threads_get_by_id(DataExchange::getDBLink(), $id);
}
/**
 * Get thread.
 * @param int $board Board id.
 * @param int $original_post Thread number.
 * @return array
 * thread.
 */
function threads_get_by_original_post($board, $original_post) {
    return db_threads_get_by_original_post(DataExchange::getDBLink(), $board, $original_post);
}
/**
 * Get changeable thread.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 * @return int|array Returns array of thread data or integer error value. Error
 * values is: 1 if user have no permissions to change thread, 2 if thread not
 * found.
 */
function threads_get_changeable_by_id($thread_id, $user_id) {
    return db_threads_get_changeable_by_id(DataExchange::getDBLink(),
                                           $thread_id,
                                           $user_id);
}
/**
 * Get moderatable threads.
 * @param int $user_id User id.
 * @param int $page Page number. Default value is 1.
 * @param int $threads_per_page Count of thread per page. Default value is 100.
 * @return array
 * 0 int - total count of threads.
 * 1 array - threads.
 */
function threads_get_moderatable($user_id, $page = 1, $threads_per_page = 100) {
    return db_threads_get_moderatable(DataExchange::getDBLink(),
                                      $user_id,
                                      $page,
                                      $threads_per_page);
}
/**
 * Get moderatable thread.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 * @return array|null
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
 * @return array|boolean
 * thread or boolean FALSE if any error occurred and set last error to
 * appropriate error object.
 */
function threads_get_visible_by_original_post($board, $original_post,
                                              $user_id) {

    return db_threads_get_visible_by_original_post(DataExchange::getDBLink(),
                                                   $board,
                                                   $original_post,
                                                   $user_id);
}
/**
 * Get visible threads.
 * @param int $user_id User id.
 * @param int $board_id Board id.
 * @param int $page Page number.
 * @param int $threads_per_page Count of threads per page.
 * @return array
 * threads.
 */
function threads_get_visible_by_page($user_id, $board_id, $page,
                                     $threads_per_page) {

    return db_threads_get_visible_by_page(DataExchange::getDBLink(),
                                          $user_id,
                                          $board_id,
                                          $page,
                                          $threads_per_page);
}
/**
 * Calculate count of visible threads.
 * @param int $user_id User id.
 * @param int $board_id Board id.
 * @return int
 * count of visible threads.
 */
function threads_get_visible_count($user_id, $board_id) {
    return db_threads_get_visible_count(DataExchange::getDBLink(), $user_id,
                                        $board_id);
}
/**
 * Move thread.
 * @param int $thread_id Thread id.
 * @param int $board_id Board id.
 */
function threads_move_thread($thread_id, $board_id) {
    db_threads_move_thread(DataExchange::getDBLink(), $thread_id, $board_id);
}

/* ******************
 * Upload handlers. *
 ********************/

/**
 * Add upload handler.
 * @param string $name Function name.
 */
function upload_handlers_add($name) {
    db_upload_handlers_add(DataExchange::getDBLink(), $name);
}
/**
 * Check upload handler id.
 * @param mixed $id Id.
 * @return string
 * safe upload handler id.
 */
function upload_handlers_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check upload handler function name.
 * @param string $name Function name.
 * @return string
 * safe function name.
 */
function upload_handlers_check_name($name) {
    $length = strlen($name);
    if ($length <= 50 && $length >= 1) {
        $name = RawUrlEncode($name);
        $length = strlen($name);
        if ($length > 50 || (strpos($name, '%') !== false) || $length < 1 || ctype_digit($name[0])) {
            kotoba_set_last_error(new UploadHandlerNameError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new UploadHandlerNameError());
        return FALSE;
    }

    return $name;
}
/**
 * Delete upload handlers.
 * @param int $id Id.
 */
function upload_handlers_delete($id) {
    db_upload_handlers_delete(DataExchange::getDBLink(), $id);
}
/**
 * Get upload handlers.
 * @return array
 * upload handlers.
 */
function upload_handlers_get_all() {
    return db_upload_handlers_get_all(DataExchange::getDBLink());
}

/* ***************
 * Upload types. *
 *****************/

/**
 * Add upload type.
 * @param etring $extension Extension.
 * @param string $store_extension Stored extension.
 * @param boolean $is_image Image flag.
 * @param int $upload_handler_id Upload handler id.
 * @param string $thumbnail_image Thumbnail.
 */
function upload_types_add($extension, $store_extension, $is_image, $upload_handler_id, $thumbnail_image) {
    db_upload_types_add(DataExchange::getDBLink(), $extension, $store_extension, $is_image, $upload_handler_id, $thumbnail_image);
}
/**
 * Check extension.
 * @param string $ext Extension.
 * @return string|boolean Returns safe extension or boolean FALSE if any error
 * occurred and set last error to appropriate error object.
 */
function upload_types_check_extension($ext) {
    $length = strlen($ext);
    if ($length <= 10 && $length >= 1) {
        $ext = RawUrlEncode($ext);
        $length = strlen($ext);
        if ($length > 10 || (strpos($ext, '%') !== false) || $length < 1) {
            kotoba_set_last_error(new UploadTypeExtensionError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new UploadTypeExtensionError());
        return FALSE;
    }

    return $ext;
}
/**
 * Check upload type id.
 * @param mixed $id Id.
 * @return int
 * safe id.
 */
function upload_types_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check stored extension.
 * @param string $store_ext Stored extension.
 * @return string
 * stored extension.
 */
function upload_types_check_store_extension($store_ext) {
    $length = strlen($store_ext);
    if ($length <= 10 && $length >= 1) {
        $store_ext = RawUrlEncode($store_ext);
        $length = strlen($store_ext);
        if ($length > 10 || (strpos($store_ext, '%') !== false) || $length < 1) {
            kotoba_set_last_error(new UploadTypeStoreExtensionError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new UploadTypeStoreExtensionError());
        return FALSE;
    }

    return $store_ext;
}
/**
 * Check thumbnail.
 * @param string $thumbnail_image thumbnail.
 * @return string|boolean Returns safe thumbnail or boolean FALSE if any error
 * occurred and set last error to appropriate error object.
 */
function upload_types_check_thumbnail_image($thumbnail_image) {
    $length = strlen($thumbnail_image);
    if ($length <= 256 && $length >= 1) {
        $thumbnail_image = RawUrlEncode($thumbnail_image);
        $length = strlen($thumbnail_image);
        if ($length > 256 || (strpos($thumbnail_image, '%') !== false)
                || $length < 1) {

            kotoba_set_last_error(new UploadTypeThumbnailError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new UploadTypeThumbnailError());
        return FALSE;
    }

    return $thumbnail_image;
}
/**
 * Delete upload type.
 * @param int $id Id.
 */
function upload_types_delete($id) {
    db_upload_types_delete(DataExchange::getDBLink(), $id);
}
/**
 * Edit upload type.
 * @param int $id Id.
 * @param string $store_extension Stored extension.
 * @param boolean $is_image Image flag.
 * @param int $upload_handler_id Upload handler id.
 * @param string $thumbnail_image Thumbnail.
 */
function upload_types_edit($id, $store_extension, $is_image, $upload_handler_id, $thumbnail_image) {
    db_upload_types_edit(DataExchange::getDBLink(), $id, $store_extension, $is_image, $upload_handler_id, $thumbnail_image);
}
/**
 * Get upload types.
 * @return array
 * upload types.
 */
function upload_types_get_all() {
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

/* ************************
 * User groups relations. *
 **************************/

/**
 * Add user to group.
 * @param int $user_id User id.
 * @param int $group_id Group id.
 */
function user_groups_add($user_id, $group_id) {
    db_user_groups_add(DataExchange::getDBLink(), $user_id, $group_id);
}
/**
 * Delete user from group.
 * @param int $user_id User id.
 * @param int $group_id Group id.
 */
function user_groups_delete($user_id, $group_id) {
    db_user_groups_delete(DataExchange::getDBLink(), $user_id, $group_id);
}
/**
 * Move user to new group.
 * @param int $user_id User id.
 * @param int $old_group_id Id of old group.
 * @param int $new_group_id Id of new group.
 */
function user_groups_edit($user_id, $old_group_id, $new_group_id) {
    db_user_groups_edit(DataExchange::getDBLink(), $user_id, $old_group_id, $new_group_id);
}
/**
 * Get user groups relations.
 * @return array
 * user groups relations.
 */
function user_groups_get_all() {
    return db_user_groups_get_all(DataExchange::getDBLink());
}

/****************************
 * Работа с пользователями. *
 ****************************/

/**
 * Check redirection.
 * @param string $goto Redirection.
 * @return string|boolean Returns safe redirection or boolean FALSE if any error
 * occured and set last error to appropriate error object.
 */
function users_check_goto($goto) {
    if ($goto === 'b' || $goto === 't') {
        return $goto;
    } else {
        kotoba_set_last_error(new UserGotoError());
        return FALSE;
    }
}
/**
 * Check user id.
 * @param mixed $id User id.
 * @return int
 * safe user id.
 */
function users_check_id($id) {
    return kotoba_intval($id);
}
/**
 * Check keyword.
 * @param string $keyword Keyword.
 * @return string|boolean Returns safe keyword or boolean FALSE if any error
 * occurred and set last error to appropriate error object.
 */
function users_check_keyword($keyword) {
    $keyword = kotoba_strval($keyword);
    $length = strlen($keyword);
    if ($length <= 32 && $length >= 2) {
        $keyword = RawUrlEncode($keyword);
        $length = strlen($keyword);
        if ($length > 32 || (strpos($keyword, '%') !== false) || $length < 2) {
            kotoba_set_last_error(new UserKeywordError());
            return FALSE;
        }
    } else {
        kotoba_set_last_error(new UserKeywordError());
        return FALSE;
    }

    return $keyword;
}
/**
 * Проверяет корректность количества строк в предпросмотре сообщения.
 * @param string|int $lines_per_post Количество строк в предпросмотре сообщения.
 * @return string|boolean Returns safe count of lines per post or boolean FALSE
 * if any error occurred and set last error to appropriate error object.
 */
function users_check_lines_per_post($lines_per_post) {
    $lines_per_post = kotoba_intval($lines_per_post);
    $length = strlen($lines_per_post);
    if ($length <= 2 && $length >= 1) {
        $lines_per_post = RawUrlEncode($lines_per_post);
        $length = strlen($lines_per_post);
        if($length > 2 || (ctype_digit($lines_per_post) === false) || $length < 1) {
            $_ = new UserLinesPerPostError(Config::MIN_LINESPERPOST,
                                           Config::MAX_LINESPERPOST);
            kotoba_set_last_error($_);
            return FALSE;
        }
    } else {
        $_ = new UserLinesPerPostError(Config::MIN_LINESPERPOST,
                                       Config::MAX_LINESPERPOST);
        kotoba_set_last_error($_);
        return FALSE;
    }

    return kotoba_intval($lines_per_post);
}
/**
 * Check count of posts per thread.
 * @param int $posts_per_thread Count of posts per thread.
 * @return int|boolean Returns safe count of posts per thread or boolean FALSE
 * if any error occurred and set last error to appropriate error object.
 */
function users_check_posts_per_thread($posts_per_thread) {
    $posts_per_thread = kotoba_intval($posts_per_thread);
    $length = strlen($posts_per_thread);
    if ($length <= 2 && $length >= 1) {
        $posts_per_thread = RawUrlEncode($posts_per_thread);
        $length = strlen($posts_per_thread);
        if($length > 2 || (ctype_digit($posts_per_thread) === false) || $length < 1) {
            $_ = new UserPostsPerThreadError(Config::MIN_POSTSPERTHREAD,
                                             Config::MAX_POSTSPERTHREAD);
            kotoba_set_last_error($_);
            return FALSE;
        }
    } else {
        $_ = new UserPostsPerThreadError(Config::MIN_POSTSPERTHREAD,
                                         Config::MAX_POSTSPERTHREAD);
        kotoba_set_last_error($_);
        return FALSE;
    }

    return kotoba_intval($posts_per_thread);
}
/**
 * Check count of threads per page.
 * @param int $threads_per_page Count of threads per page.
 * @return int|boolean Returns safe count of threads per page or boolean FALSE
 * if any error occurred and set last error to appropriate error object.
 */
function users_check_threads_per_page($threads_per_page) {
    $threads_per_page = kotoba_intval($threads_per_page);
    $length = strlen($threads_per_page);
    if ($length <= 2 && $length >= 1) {
        $threads_per_page = RawUrlEncode($threads_per_page);
        $length = strlen($threads_per_page);
        if ($length > 2 || (ctype_digit($threads_per_page) === false) || $length < 1) {
            $_ = new UserThreadsPerPageError(Config::MIN_THREADSPERPAGE,
                                             Config::MAX_THREADSPERPAGE);
            kotoba_set_last_error($_);
            return FALSE;
        }
    } else {
        $_ = new UserThreadsPerPageError(Config::MIN_THREADSPERPAGE,
                                         Config::MAX_THREADSPERPAGE);
        kotoba_set_last_error($_);
        return FALSE;
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
 * Get users.
 * @return array
 * users.
 */
function users_get_all() {
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
 * @return array|boolean Returns array of user settings or boolean FALSE if any
 * error occurred and set last error to appropriate error object.
 */
function users_get_by_keyword($keyword) {
    return db_users_get_by_keyword(DataExchange::getDBLink(), $keyword);
}
/**
 * Check if use is admin.
 * @param int $id User id.
 * @return boolean
 * TRUE if user is admin, FALSE otherwise.
 */
function users_is_admin($id) {
    static $admins = NULL;

    if ($admins === NULL) {
        $admins = users_get_admins();
    }

    foreach ($admins as $admin) {
        if ($id == $admin['id']) {
            return TRUE;
        }
    }

    return FALSE;
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
 * @return string|int Returns safe code of video or integer error code. Error
 * codes: 1 - link too long.
 */
function videos_check_code($code) {
    $code = RawURLEncode($code);
    if (strlen($code) > Config::MAX_FILE_LINK) {
        return 1;
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
