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
 * Интерфейс работы с БД.
 * @package api
 */

/* *********
 * Common. *
 ***********/

/**
 * Clean up link to database. Any received results over this link will be lost.
 * You MUST call this function after each stored procedure call.
 * @param MySQLi $link Link to database.
 */
function db_cleanup_link($link) {
	/*
	 * Если использовать mysqli_use_result вместо store, то
	 * не будет выведена ошибка, если таковая произошла в следующем запросе
	 * в mysqli_multi_query.
	 */
    if (($result = mysqli_store_result($link)) != false) {
        mysqli_free_result($result);
    }
    while (mysqli_more_results($link)) {
        mysqli_next_result($link);
		if (($result = mysqli_store_result($link)) != false) {
			mysqli_free_result($result);
        }
	}
	if (mysqli_errno($link)) {
		throw new CommonException(mysqli_error($link));
    }
}

/* *************************************
 * Работа со списком контроля доступа. *
 ***************************************/

/**
 * Add rule to ACL.
 * @param MySQLi $link Link to database.
 * @param int|null $group_id Group id.
 * @param int|null $board_id Board id.
 * @param int|null $thread_id Thread id.
 * @param int|null $post_id Post id.
 * @param boolean $view View permission.
 * @param boolean $change Change prmission.
 * @param boolean $moderate Moderate permission.
 */
function db_acl_add($link, $group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate) {
    $group_id = ($group_id == null ? 'null' : $group_id);
    $board_id = ($board_id == null ? 'null' : $board_id);
    $thread_id = ($thread_id == null ? 'null' : $thread_id);
    $post_id = ($post_id == null ? 'null' : $post_id);
    $view = $view ? '1' : '0';
    $change = $change ? '1' : '0';
    $moderate = $moderate ? '1' : '0';
    $query = "call sp_acl_add($group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate)";
    $result = mysqli_query($link, $query);
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete rule from ACL.
 * @param MySQLi $link Link to database.
 * @param int|null $group_id Group id.
 * @param int|null $board_id Board id.
 * @param int|null $thread_id Thread id.
 * @param int|null $post_id Post id.
 */
function db_acl_delete($link, $group_id, $board_id, $thread_id, $post_id) {
    $group_id = ($group_id === null ? 'null' : $group_id);
    $board_id = ($board_id === null ? 'null' : $board_id);
    $thread_id = ($thread_id === null ? 'null' : $thread_id);
    $post_id = ($post_id === null ? 'null' : $post_id);
    $query = "call sp_acl_delete($group_id, $board_id, $thread_id, $post_id)";
    $result = mysqli_query($link, $query);
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Edit rule in ACL.
 * @param MySQLi $link Link to database.
 * @param int|null $group_id Group id.
 * @param int|null $board_id Board id.
 * @param int|null $thread_id Thread id.
 * @param int|null $post_id Post id.
 * @param boolean $view View permission.
 * @param boolean $change Change prmission.
 * @param boolean $moderate Moderate permission.
 */
function db_acl_edit($link, $group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate) {
    $group_id = ($group_id == null ? 'null' : $group_id);
    $board_id = ($board_id == null ? 'null' : $board_id);
    $thread_id = ($thread_id == null ? 'null' : $thread_id);
    $post_id = ($post_id == null ? 'null' : $post_id);
    $view = $view ? '1' : '0';
    $change = $change ? '1' : '0';
    $moderate = $moderate ? '1' : '0';
    $query = "call sp_acl_edit($group_id, $board_id, $thread_id, $post_id, $view, $change, $moderate)";
    $result = mysqli_query($link, $query);
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get ACL.
 * @param MySQLi $link Link to database.
 * @return array
 * ACL.
 */
function db_acl_get_all($link) {
    if ( ($result = mysqli_query($link, 'call sp_acl_get_all()')) == FALSE) {
        throw new CommonException(mysqli_error($link));
    }

    $acl = array();
    if (mysqli_affected_rows($link) > 0)
        while ( ($row = mysqli_fetch_assoc($result)) != NULL)
            array_push($acl, array(	'group' => $row['group'],
                                    'board' => $row['board'],
                                    'thread' => $row['thread'],
                                    'post' => $row['post'],
                                    'view' => $row['view'],
                                    'change' => $row['change'],
                                    'moderate' => $row['moderate']));
    else {
        throw new NodataException(NodataException::$messages['ACL_NOT_EXIST']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $acl;
}

/* *******
 * Bans. *
 *********/

/**
 * Ban IP-address range.
 * @param MySQLi $link Link to database.
 * @param int $range_beg Begin of banned IP-address range.
 * @param int $range_end End of banned IP-address range.
 * @param string $reason Ban reason.
 * @param string $untill Expiration time.
 */
function db_bans_add($link, $range_beg, $range_end, $reason, $untill) {
    $reason = ($reason === null ? 'null' : $reason);

    if (!mysqli_query($link, "call sp_bans_add($range_beg, $range_end, '$reason', '$untill')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Checks if IP-address banned.
 * @param MySQLi $link Link to database.
 * @param int $ip IP-address.
 * @return boolean|array
 * Return FALSE if IP-address not banned. Otherwise return ban information.
 */
function db_bans_check($link, $ip) {
    $result = mysqli_query($link, "call sp_bans_check($ip)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $row = false;
    if (mysqli_affected_rows($link) > 0) {
        $row = mysqli_fetch_assoc($result);
        $row = array('range_beg' => $row['range_beg'],
                     'range_end' => $row['range_end'],
                     'untill' => $row['untill'],
                     'reason' => $row['reason']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $row;
}
/**
 * Delete ban.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_bans_delete_by_id($link, $id) {
    if (!mysqli_query($link, "call sp_bans_delete_by_id($id)")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Delete certain ip bans.
 * @param MySQLi $link Link to database.
 * @param int $ip IP-address.
 */
function db_bans_delete_by_ip($link, $ip) {
    if (!mysqli_query($link, "call sp_bans_delete_by_ip($ip)")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Get bans.
 * @param MySQLi $link Link to database.
 * @return array
 * bans.
 */
function db_bans_get_all($link) {
    $result = mysqli_query($link, 'call sp_bans_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $bans = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($bans,
                       array('id' => $row['id'],
                             'range_beg' => $row['range_beg'],
                             'range_end' => $row['range_end'],
                             'reason' => $row['reason'],
                             'untill' => $row['untill']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $bans;
}

/* *********************
 * Board upload types. *
 ***********************/

/**
 * Add board upload type relation.
 * @param MySQLi $link Link to database.
 * @param int $board Board id.
 * @param int $upload_type Upload type id.
 */
function db_board_upload_types_add($link, $board, $upload_type) {
    if (!mysqli_query($link, "call sp_board_upload_types_add($board, $upload_type)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete board upload type relation.
 * @param MySQLi $link Link to database.
 * @param int $board Board id.
 * @param int $upload_type Upload type id.
 */
function db_board_upload_types_delete($link, $board, $upload_type) {
    if (!mysqli_query($link, "call sp_board_upload_types_delete($board, $upload_type)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get board upload types relations.
 * @param MySQLi $link Link to database.
 * @return array
 * board upload types relations.
 */
function db_board_upload_types_get_all($link) {
    $result = mysqli_query($link, 'call sp_board_upload_types_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $board_upload_types = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($board_upload_types,
                       array('board' => $row['board'],
                             'upload_type' => $row['upload_type']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $board_upload_types;
}

/* ********
 * Words. *
 **********/

/**
 * Add word.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @param string $word Word.
 * @param string $replace Replacement.
 */
function db_words_add($link, $board_id, $word, $replace) {
    $word = $word == null ? 'null' : "'$word'";
    $replace = $replace == null ? 'null' : "'$replace'";
    if (!mysqli_query($link, "call sp_words_add($board_id, $word, $replace)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete word.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_words_delete($link, $id) {
    if (!mysqli_query($link, "call sp_words_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Edit word.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 * @param string $word Word.
 * @param string $replace Replacement.
 */
function db_words_edit($link, $id, $word, $replace) {
    $word = $word == null ? 'null' : "'$word'";
    $replace = $replace == null ? 'null' : "'$replace'";
    if (!mysqli_query($link, "call sp_words_edit($id, $word, $replace)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get words.
 * @param MySQLi $link Link to database.
 * @return array
 * words.
 */
function db_words_get_all($link) {
    $result = mysqli_query($link, 'call sp_words_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $words = array();
    if (mysqli_affected_rows($link) > 0) {
        while( ($row = mysqli_fetch_assoc($result)) != NULL){
            array_push($words,
                       array('id' => $row['id'],
                             'board_id' => $row['board_id'],
                             'word' => $row['word'],
                             'replace' => $row['replace']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $words;
}
/**
 * Get all words from wordfilter.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @return array
 * words.
 */
function db_words_get_all_by_board($link, $board_id) {
    $result = mysqli_query($link, "call sp_words_get_all_by_board($board_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $words = array();
    if(mysqli_affected_rows($link) > 0) {
        while( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($words,
                       array('id' => $row['id'],
                             'word' => $row['word'],
                             'replace' => $row['replace']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $words;
}

/* *********
 * Boards. *
 ***********/

/**
 * Add board.
 * @param MySQLi $link Link to database.
 * @param array $new_board Board.
 */
function db_boards_add($link, $new_board) {

    // Prepare data fro query.
    foreach (array('force_anonymous',
                   'with_attachments',
                   'enable_macro',
                   'enable_youtube',
                   'enable_captcha',
                   'enable_translation',
                   'enable_geoip',
                   'enable_shi',
                   'enable_postid') as $attr) {

        if ($new_board[$attr] === null) {
            $new_board[$attr] = 'null';
        }
        if ($new_board[$attr] === false) {
            $new_board[$attr] = '0';
        }
    }
    foreach (array('name',
                   'title',
                   'annotation',
                   'default_name',
                   'same_upload') as $attr) {

        if ($new_board[$attr] === null) {
            $new_board[$attr] = 'null';
        } else {
            $new_board[$attr] = "'{$new_board[$attr]}'";
        }
    }

    $query = "call sp_boards_add({$new_board['name']},
                                 {$new_board['title']},
                                 {$new_board['annotation']},
                                 {$new_board['bump_limit']},
                                 {$new_board['force_anonymous']},
                                 {$new_board['default_name']},
                                 {$new_board['with_attachments']},
                                 {$new_board['enable_macro']},
                                 {$new_board['enable_youtube']},
                                 {$new_board['enable_captcha']},
                                 {$new_board['enable_translation']},
                                 {$new_board['enable_geoip']},
                                 {$new_board['enable_shi']},
                                 {$new_board['enable_postid']},
                                 {$new_board['same_upload']},
                                 {$new_board['popdown_handler']},
                                 {$new_board['category']})";
    if (!mysqli_query($link, $query)) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Deletes board.
 * @param MySQLi $link Link to database.
 * @param int $id Board id.
 */
function db_boards_delete($link, $id) {
    if (!mysqli_query($link, "call sp_boards_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Edit board.
 * @param MySQLi $link Link to database.
 * @param array $board Board.
 */
function db_boards_edit($link, $board) {

    // Prepare data for query.
    foreach (array('force_anonymous',
                   'with_attachments',
                   'enable_macro',
                   'enable_youtube',
                   'enable_captcha',
                   'enable_translation',
                   'enable_geoip',
                   'enable_shi',
                   'enable_postid') as $attr) {

        if ($board[$attr] === null) {
            $board[$attr] = 'null';
            continue;
        }
        if ($board[$attr] === false) {
            $board[$attr] = '0';
        }
    }
    foreach (array('title',
                   'annotation',
                   'default_name',
                   'same_upload') as $attr) {

        if ($board[$attr] === null) {
            $board[$attr] = 'null';
        } else {
            $board[$attr] = "'{$board[$attr]}'";
        }
    }

    $query = "call sp_boards_edit({$board['id']},
                                  {$board['title']},
                                  {$board['annotation']},
                                  {$board['bump_limit']},
                                  {$board['force_anonymous']},
                                  {$board['default_name']},
                                  {$board['with_attachments']},
                                  {$board['enable_macro']},
                                  {$board['enable_youtube']},
                                  {$board['enable_captcha']},
                                  {$board['enable_translation']},
                                  {$board['enable_geoip']},
                                  {$board['enable_shi']},
                                  {$board['enable_postid']},
                                  {$board['same_upload']},
                                  {$board['popdown_handler']},
                                  {$board['category']})";
    if (!mysqli_query($link, $query)) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get boards.
 * @param MySQLi $link Link to database.
 * @return array
 * boards.
 */
function db_boards_get_all($link) {
    $result = mysqli_query($link, 'call sp_boards_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $boards = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($boards,
                array('id' => $row['id'],
                      'name' => $row['name'],
                      'title' => $row['title'],
                      'annotation' => $row['annotation'],
                      'bump_limit' => $row['bump_limit'],
                      'force_anonymous' => $row['force_anonymous'],
                      'default_name' => $row['default_name'],
                      'with_attachments' => $row['with_attachments'],
                      'enable_macro' => $row['enable_macro'],
                      'enable_youtube' => $row['enable_youtube'],
                      'enable_captcha' => $row['enable_captcha'],
                      'enable_translation' => $row['enable_translation'],
                      'enable_geoip' => $row['enable_geoip'],
                      'enable_shi' => $row['enable_shi'],
                      'enable_postid' => $row['enable_postid'],
                      'same_upload' => $row['same_upload'],
                      'popdown_handler' => $row['popdown_handler'],
                      'category' => $row['category'],
                      'category_name' => $row['category_name']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);    
    return $boards;
}
/**
 * Get board.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @return array
 * board.
 */
function db_boards_get_by_id($link, $board_id) {
    $result = mysqli_query($link, "call sp_boards_get_by_id($board_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $board = null;
    if (mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        $board = array('id' => $row['id'],
                       'name' => $row['name'],
                       'title' => $row['title'],
                       'annotation' => $row['annotation'],
                       'bump_limit' => $row['bump_limit'],
                       'force_anonymous' => $row['force_anonymous'],
                       'default_name' => $row['default_name'],
                       'with_attachments' => $row['with_attachments'],
                       'enable_macro' => $row['enable_macro'],
                       'enable_youtube' => $row['enable_youtube'],
                       'enable_captcha' => $row['enable_captcha'],
                       'same_upload' => $row['same_upload'],
                       'popdown_handler' => $row['popdown_handler'],
                       'category' => $row['category']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $board;
}
/**
 * Получает заданную доску.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_by_name($link, $board_name)
{
	$result = mysqli_query($link, 'call sp_boards_get_by_name(\''
		. $board_name . '\')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$board = null;
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) !== null)
	{
		$board['id'] = $row['id'];
		$board['name'] = $row['name'];
		$board['title'] = $row['title'];
		$board['annotation'] = $row['annotation'];
		$board['bump_limit'] = $row['bump_limit'];
		$board['force_anonymous'] = $row['force_anonymous'];
		$board['default_name'] = $row['default_name'];
		$board['with_attachments'] = $row['with_attachments'];
		$board['enable_macro'] = $row['enable_macro'];
		$board['enable_youtube'] = $row['enable_youtube'];
		$board['enable_captcha'] = $row['enable_captcha'];
		$board['same_upload'] = $row['same_upload'];
		$board['popdown_handler'] = $row['popdown_handler'];
		$board['category'] = $row['category'];
	}
	if($board === null)
		throw new NodataException(NodataException::$messages['BOARD_NOT_FOUND']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board;
}
/**
 * Получает доски, доступные для изменения заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_changeable($link, $user_id)
{
	$result = mysqli_query($link, 'call sp_boards_get_changeable(' . $user_id . ')');
	if($result == false)
		throw new CommonException(mysqli_error($link));
	$boards = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($boards, array('id' => $row['id'],
					'name' => $row['name'],
					'title' => $row['title'],
					'annotation' => $row['annotation'],
					'bump_limit' => $row['bump_limit'],
					'force_anonymous' => $row['force_anonymous'],
					'default_name' => $row['default_name'],
					'with_attachments' => $row['with_attachments'],
					'enable_macro' => $row['enable_macro'],
					'enable_youtube' => $row['enable_youtube'],
					'enable_captcha' => $row['enable_captcha'],
					'same_upload' => $row['same_upload'],
					'popdown_handler' => $row['popdown_handler'],
					'category' => $row['category'],
					'category_name' => $row['category_name']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $boards;
}
/**
 * Get changeable board.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @param int $user_id User id.
 * @return array
 * board.
 */
function db_boards_get_changeable_by_id($link, $board_id, $user_id) {
    $result = mysqli_query($link, "call sp_boards_get_changeable_by_id($board_id, $user_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    if (mysqli_affected_rows($link) <= 0) {
        mysqli_free_result($result);
        db_cleanup_link($link);
        throw new PermissionException(PermissionException::$messages['BOARD_NOT_ALLOWED']);
    }

    $row = mysqli_fetch_assoc($result);
    if (isset($row['error']) && $row['error'] == 'NOT_FOUND') {
        mysqli_free_result($result);
        db_cleanup_link($link);
        throw new NodataException(NodataException::$messages['BOARD_NOT_FOUND']);
    }

    $board = array('id' => $row['id'],
                   'name' => $row['name'],
                   'title' => $row['title'],
                   'annotation' => $row['annotation'],
                   'bump_limit' => $row['bump_limit'],
                   'force_anonymous' => $row['force_anonymous'],
                   'default_name' => $row['default_name'],
                   'with_attachments' => $row['with_attachments'],
                   'enable_macro' => $row['enable_macro'],
                   'enable_youtube' => $row['enable_youtube'],
                   'enable_captcha' => $row['enable_captcha'],
                   'same_upload' => $row['same_upload'],
                   'popdown_handler' => $row['popdown_handler'],
                   'category' => $row['category'],
                   'category_name' => $row['category_name']);

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $board;
}
/**
 * Получает заданную доску, доступную для редактирования заданному
 * пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_changeable_by_name($link, $board_name, $user_id)
{
	$result = mysqli_query($link, 'call sp_boards_get_changeable_by_name(\''
		. $board_name . '\', ' . $user_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) <= 0)
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new PermissionException(PermissionException::$messages['BOARD_NOT_ALLOWED']);
	}
	$row = mysqli_fetch_assoc($result);
	if(isset($row['error']) && $row['error'] == 'NOT_FOUND')
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new NodataException(NodataException::$messages['BOARD_NOT_FOUND']);
	}
	$board['id'] = $row['id'];
	$board['name'] = $row['name'];
	$board['title'] = $row['title'];
	$board['annotation'] = $row['annotation'];
	$board['bump_limit'] = $row['bump_limit'];
	$board['force_anonymous'] = $row['force_anonymous'];
	$board['default_name'] = $row['default_name'];
	$board['with_attachments'] = $row['with_attachments'];
	$board['enable_macro'] = $row['enable_macro'];
	$board['enable_youtube'] = $row['enable_youtube'];
	$board['enable_captcha'] = $row['enable_captcha'];
	$board['same_upload'] = $row['same_upload'];
	$board['popdown_handler'] = $row['popdown_handler'];
	$board['category'] = $row['category'];
	$board['category_name'] = $row['category_name'];
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board;
}
/**
 * Получает доски, доступные для модерирования заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_moderatable($link, $user_id)
{
	$result = mysqli_query($link, "call sp_boards_get_moderatable($user_id)");
	if(!$result)
	{
		throw new CommonException(mysqli_error($link));
	}
	$boards = array();
	if(mysqli_affected_rows($link) > 0)
	{
		while(($row = mysqli_fetch_assoc($result)) !== null)
		{
			array_push($boards, array('id' => $row['id'],
					'name' => $row['name'],
					'title' => $row['title'],
					'annotation' => $row['annotation'],
					'bump_limit' => $row['bump_limit'],
					'force_anonymous' => $row['force_anonymous'],
					'default_name' => $row['default_name'],
					'with_attachments' => $row['with_attachments'],
					'enable_macro' => $row['enable_macro'],
					'enable_youtube' => $row['enable_youtube'],
					'enable_captcha' => $row['enable_captcha'],
					'same_upload' => $row['same_upload'],
					'popdown_handler' => $row['popdown_handler'],
					'category' => $row['category']));
		}
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $boards;
}
/**
 * Returns boards visible to user.
 * @param MySQLi $link Link to database.
 * @param int $user_id User id.
 * @return array
 * boards visible to user.
 */
function db_boards_get_visible($link, $user_id) {
    $result = mysqli_query($link, "call sp_boards_get_visible($user_id)");
    if ($result == false) {
        throw new CommonException(mysqli_error($link));
    }

    $boards = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != null) {
            array_push($boards,
                array('id' => $row['id'],
                      'name' => $row['name'],
                      'title' => $row['title'],
                      'annotation' => $row['annotation'],
                      'bump_limit' => $row['bump_limit'],
                      'force_anonymous' => $row['force_anonymous'],
                      'default_name' => $row['default_name'],
                      'with_attachments' => $row['with_attachments'],
                      'enable_macro' => $row['enable_macro'],
                      'enable_youtube' => $row['enable_youtube'],
                      'enable_captcha' => $row['enable_captcha'],
                      'enable_translation' => $row['enable_translation'],
                      'enable_geoip' => $row['enable_geoip'],
                      'enable_shi' => $row['enable_shi'],
                      'enable_postid' => $row['enable_postid'],
                      'same_upload' => $row['same_upload'],
                      'popdown_handler' => $row['popdown_handler'],
                      'category' => $row['category'],
                      'category_name' => $row['category_name']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $boards;
}

/* *************
 * Categories. *
 ***************/

/**
 * Add category.
 * @param MySQLi $link Link to database.
 * @param string $name Name.
 */
function db_categories_add($link, $name) {
    if (!mysqli_query($link, "call sp_categories_add('$name')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete category.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_categories_delete($link, $id) {
    if (!mysqli_query($link, "call sp_categories_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get categories.
 * @param MySQLi $link Link to database.
 * @return array
 * category.
 */
function db_categories_get_all($link) {
    // Query.
    if ( ($result = mysqli_query($link, 'call sp_categories_get_all()')) == FALSE) {
        throw new CommonException(mysqli_error($link));
    }

    // Collect data from query result.
    $categories = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != null) {
            array_push($categories,
                       array('id' => $row['id'],
                             'name' => $row['name']));
        }
    }

    // Cleanup.
    mysqli_free_result($result);
    db_cleanup_link($link);

    return $categories;
}

/* ************
 * Favorites. *
 **************/

/**
 * Adds thread to user's favorites.
 * @param MySQLi $link Link to database.
 * @param int $user User id.
 * @param int $thread Thread id.
 */
function db_favorites_add($link, $user, $thread) {
    if (!mysqli_query($link, "call sp_favorites_add($user, $thread)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}

/**
 * Removes thread from user's favorites.
 * @param MySQLi $link Link to database.
 * @param int $user User id.
 * @param int $thread Thread id.
 */
function db_favorites_delete($link, $user, $thread) {
    if (!mysqli_query($link, "call sp_favorites_delete($user, $thread)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get favorite threads.
 * @param MySQLi $link Link to database.
 * @param int $user User id.
 * @return array
 * threads.
 */
function db_favorites_get_by_user($link, $user) {
    // Query.
    if ( ($result = mysqli_query($link, "call sp_favorites_get_by_user($user)")) == FALSE) {
        throw new CommonException(mysqli_error($link));
    }

    // Collect data from query result.
    $favorites = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            if (!isset($board_data[$row['board_id']])) {
                $board_data[$row['board_id']] = array('id' => $row['board_id'],
                                                      'name' => $row['board_name'],
                                                      'title' => $row['board_title'],
                                                      'annotation' => $row['board_annotation'],
                                                      'bump_limit' => $row['board_bump_limit'],
                                                      'force_anonymous' => $row['board_force_anonymous'],
                                                      'default_name' => $row['board_default_name'],
                                                      'with_attachments' => $row['board_with_attachments'],
                                                      'enable_macro' => $row['board_enable_macro'],
                                                      'enable_youtube' => $row['board_enable_youtube'],
                                                      'enable_captcha' => $row['board_enable_captcha'],
                                                      'same_upload' => $row['board_same_upload'],
                                                      'popdown_handler' => $row['board_popdown_handler'],
                                                      'category' => $row['board_category']);
            }
            if (!isset($thread_data[$row['thread_id']])) {
                $thread_data[$row['thread_id']] = array('id' => $row['thread_id'],
                                                        'board' => &$board_data[$row['board_id']],
                                                        'original_post' => $row['thread_original_post'],
                                                        'bump_limit' => $row['thread_bump_limit'],
                                                        'deleted' => $row['thread_deleted'],
                                                        'archived' => $row['thread_archived'],
                                                        'sage' => $row['thread_sage'],
                                                        'sticky' => $row['thread_sticky'],
                                                        'with_attachments' => $row['thread_with_attachments']);
            }
            if (!isset($user_data[$row['user_id']])) {
                $user_data[$row['user_id']] = array('id' => $row['user_id'],
                                                    'keyword' => $row['user_keyword'],
                                                    'posts_per_thread' => $row['user_posts_per_thread'],
                                                    'threads_per_page' => $row['user_threads_per_page'],
                                                    'lines_per_post' => $row['user_lines_per_post'],
                                                    'language' => $row['user_language'],
                                                    'stylesheet' => $row['user_stylesheet'],
                                                    'password' => $row['user_password'],
                                                    'goto' => $row['user_goto']);
            }
            array_push($favorites,
                       array('user' => &$user_data[$row['user_id']],
                             'thread' => &$thread_data[$row['thread_id']],
                             'post' => array('id' => $row['post_id'],
                                             'board' => &$board_data[$row['board_id']],
                                             'thread' => &$thread_data[$row['thread_id']],
                                             'number' => $row['post_number'],
                                             'user' => &$user_data[$row['user_id']],
                                             'password' => $row['post_password'],
                                             'name' => $row['post_name'],
                                             'tripcode' => $row['post_tripcode'],
                                             'ip' => $row['post_ip'],
                                             'subject' => $row['post_subject'],
                                             'date_time' => $row['post_date_time'],
                                             'text' => $row['post_text'],
                                             'sage' => $row['post_sage']),
                             'last_readed' => $row['last_readed']));
        }
    }

    // Cleanup.
    mysqli_free_result($result);
    db_cleanup_link($link);

    return $favorites;
}

/**
 * Mark thread as readed in user favorites. If thread is null then marks all
 * threads as readed.
 * @param MySQLi $link Link to database.
 * @param int $user User id.
 * @param int|null $thread Thread id or NULL.
 */
function db_favorites_mark_readed($link, $user, $thread) {
    if ($thread === null) {
        if (!mysqli_query($link, "call sp_favorites_mark_readed_all($user)")) {
            throw new CommonException(mysqli_error($link));
        }
    } else {
        if (!mysqli_query($link, "call sp_favorites_mark_readed($user, $thread)")) {
            throw new CommonException(mysqli_error($link));
        }
    }

    db_cleanup_link($link);
}

/* ********
 * Files. *
 **********/

/**
 * Add file.
 * @param MySQLi $link Link to database.
 * @param string $hash Hash.
 * @param string $name Name.
 * @param int $size Size in bytes..
 * @param string $thumbnail Thumbnail.
 * @param int $thumbnail_w Thumbnail width.
 * @param int $thumbnail_h Thumbnail height.
 * @return int
 * added file id.
 */
function db_files_add($link, $hash, $name, $size, $thumbnail, $thumbnail_w, $thumbnail_h) {
    $query = "call sp_files_add('$hash', '$name', $size, '$thumbnail', $thumbnail_w, $thumbnail_h)";
    $result = mysqli_query($link, $query);
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    db_cleanup_link($link);
    return is_int($row['id']) ? $row['id'] : kotoba_intval($row['id']);
}
/**
 * Get files.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * files.
 */
function db_files_get_by_post($link, $post_id) {
    $result = mysqli_query($link, "call sp_files_get_by_post($post_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $files = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($files,
                       array('id' => $row['id'],
                             'hash' => $row['hash'],
                             'name' => $row['name'],
                             'size' => $row['size'],
                             'thumbnail' => $row['thumbnail'],
                             'thumbnail_w' => $row['thumbnail_w'],
                             'thumbnail_h' => $row['thumbnail_h'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_FILE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $files;
}
/**
 * Get thread files.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @return array
 * files.
 */
function db_files_get_by_thread($link, $thread_id) {
    $result = mysqli_query($link, "call sp_files_get_by_thread($thread_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $files = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != null) {
            array_push($files,
                array('id' => $row['id'],
                      'hash' => $row['hash'],
                      'name' => $row['name'],
                      'size' => $row['size'],
                      'thumbnail' => $row['thumbnail'],
                      'thumbnail_w' => $row['thumbnail_w'],
                      'thumbnail_h' => $row['thumbnail_h'],
                      'attachment_type' => Config::ATTACHMENT_TYPE_FILE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $files;
}
/**
 * Get dangling files.
 * @param MySQLi $link Link to database.
 * @return array
 * files.
 */
function db_files_get_dangling($link) {
    $result = mysqli_query($link, 'call sp_files_get_dangling()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $files = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != null) {
            array_push( $files,
                        array('id' => $row['id'],
                              'hash' => $row['hash'],
                              'name' => $row['name'],
                              'size' => $row['size'],
                              'thumbnail' => $row['thumbnail'],
                              'thumbnail_w' => $row['thumbnail_w'],
                              'thumbnail_h' => $row['thumbnail_h'],
                              'attachment_type' => Config::ATTACHMENT_TYPE_FILE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $files;
}
/**
 * Get same files.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @param int $user_id User id.
 * @param string $file_hash File hash.
 * @return array
 * files.
 */
 function db_files_get_same($link, $board_id, $user_id, $file_hash) {
    $result = mysqli_query($link, "call sp_files_get_same($board_id, $user_id, '$file_hash')");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $files = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            if (!isset($thread_data[$row['thread_id']])) {
                $thread_data[$row['thread_id']] = array('id' => $row['thread_id'],
                                                        'board' => $row['thread_board'],
                                                        'original_post' => $row['thread_original_post'],
                                                        'bump_limit' => $row['thread_bump_limit'],
                                                        'sage' => $row['thread_sage'],
                                                        'sticky' => $row['thread_sticky'],
                                                        'with_attachments' => $row['thread_with_attachments']);
            }
            if (!isset($post_data[$row['post_id']])) {
                $post_data[$row['post_id']] = array('id' => $row['post_id'],
                                                    'board' => $row['post_board'],
                                                    'thread' => &$thread_data[$row['thread_id']],
                                                    'number' => $row['post_number'],
                                                    'user' => $row['post_user'],
                                                    'password' => $row['post_password'],
                                                    'name' => $row['post_name'],
                                                    'tripcode' => $row['post_tripcode'],
                                                    'ip' => $row['post_ip'],
                                                    'subject' => $row['post_subject'],
                                                    'date_time' => $row['post_date_time'],
                                                    'text`' => $row['post_text'],
                                                    'sage' => $row['post_sage']);
            }
            array_push($files,
                       array('id' => $row['file_id'],
                             'hash' => $row['file_hash'],
                             'name' => $row['file_name'],
                             'size' => $row['file_size'],
                             'thumbnail' => $row['file_thumbnail'],
                             'thumbnail_w' => $row['file_thumbnail_w'],
                             'thumbnail_h' => $row['file_thumbnail_h'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_FILE,
                             'post' => &$post_data[$row['post_id']],
                             'view' => $row['view']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $files;
 }

/* *********
 * Groups. *
 ***********/

/**
 * Add group.
 * @param MySQLi $link Link to database.
 * @param string $name Group name.
 * @return int
 * new group id.
 */
function db_groups_add($link, $name) {
    $result = mysqli_query($link, "call sp_groups_add('$name')");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    if(mysqli_affected_rows($link) <= 0
            || ($row = mysqli_fetch_assoc($result)) == NULL) {
        throw new CommonException(CommonException::$messages['GROUPS_ADD']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $row['id'];
}
/**
 * Delete groups.
 * @param MySQLi $link Link to database.
 * @param array $groups Groups.
 */
function db_groups_delete($link, $groups) {
    foreach ($groups as $id) {
        if (!mysqli_query($link, "call sp_groups_delete($id)")) {
            throw new CommonException(mysqli_error($link));
        }

        db_cleanup_link($link);
    }
}
/**
 * Get groups.
 * @param MySQLi $link Link to database.
 * @return array
 * groups.
 */
function db_groups_get_all($link) {
    if ( ($result = mysqli_query($link, 'call sp_groups_get_all()')) == false) {
        throw new CommonException(mysqli_error($link));
    }

    $groups = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($groups, array('id' => $row['id'], 'name' => $row['name']));
        }
    } else {
        throw new NodataException(NodataException::$messages['GROUPS_NOT_EXIST']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $groups;
}
/**
 * Получает группы, в которые входит пользователь.
 * @param MySQLi $link Связь с базой данных.
 * @param int $user_id Идентификатор пользователя.
 * @return array
 * Возвращает группы:<br>
 * id - идентификатор.<br>
 * name - имя.
 */
function db_groups_get_by_user($link, $user_id) {

    // Запрос.
    $result = mysqli_query($link, "call sp_groups_get_by_user($user_id)");
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

    // Выбор данных из результата выполнения запроса.
	$groups = array();
	if (mysqli_affected_rows($link) > 0) {
		while (($row = mysqli_fetch_assoc($result)) != null) {
			array_push($groups, array('id' => $row['id'], 'name' => $row['name']));
        }
    } else {
		throw new NodataException(NodataException::$messages['GROUPS_NOT_EXIST']);
    }

    // Освобождение ресурсов и очистка.
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $groups;
}

/* ************
 * Hard bans. *
 **************/

/**
 * Ban IP-address range in firewall.
 * @param MySQLi $link Link to database.
 * @param string $range_beg Begin of banned IP-address range.
 * @param string $range_end End of banned IP-address range.
 */
function db_hard_ban_add($link, $range_beg, $range_end) {
    if (!mysqli_query($link, "call sp_hard_ban_add('$range_beg', '$range_end')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}

/* *****************
 * Hidden threads. *
 *******************/

/**
 * Hide thread.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 */
function db_hidden_threads_add($link, $thread_id, $user_id) {
    if (!mysqli_query($link, "call sp_hidden_threads_add($thread_id, $user_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Unhide thread.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 */
function db_hidden_threads_delete($link, $thread_id, $user_id) {
    if(!mysqli_query($link, "call sp_hidden_threads_delete($thread_id, $user_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get hidden threads.
 * @param MySQLi $link Link to database.
 * @param array $boards Boards.
 * @param object $filter Filter functions.
 * @return array
 * hidden threads.
 */
function db_hidden_threads_get_by_boards($link, $boards) {
    $threads = array();

    foreach ($boards as $b) {
        $result = mysqli_query($link, "call sp_hidden_threads_get_by_board({$b['id']})");
        if (!$result) {
            throw new CommonException(mysqli_error($link));
        }

        if (mysqli_affected_rows($link) > 0) {
            while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
                array_push($threads,
                           array('thread' => $row['thread'],
                                 'thread_number' => $row['original_post'],
                                 'user' => $row['user'],
                                 'board_name' => $b['name']));
            }
        }

        mysqli_free_result($result);
        db_cleanup_link($link);
    }

    return $threads;
}
/**
 * Получает доступную для просмотра скрытую нить и количество сообщений в ней.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_hidden_threads_get_visible($link, $board_id, $thread_num, $user_id)
{
	$result = mysqli_query($link,
		'call sp_hidden_threads_get_visible(' . $board_id . ', ' . $thread_num
			. ', ' . $user_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) <= 0)
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new PermissionException(PermissionException::$messages['THREAD_NOT_ALLOWED']);
	}
	$row = mysqli_fetch_assoc($result);
	if(isset($row['error']) && $row['error'] == 'NOT_FOUND')
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new NodataException(NodataException::$messages['THREAD_NOT_FOUND']);
	}
	$thread = array('id' => $row['id'],
					'board' => $board_id,
					'original_post' => $row['original_post'],
					'bump_limit' => $row['bump_limit'],
					'archived' => $row['archived'],
					'sage' => $row['sage'],
					'sticky' => $row['sticky'],
					'with_attachments' => $row['with_attachments'],
					'posts_count' => $row['posts_count']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
}


/* *********
 * Images. *
 ***********/

/**
 * Add image.
 * @param MySQLi $link Link to database.
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
function db_images_add($link,
                       $hash,
                       $name,
                       $widht,
                       $height,
                       $size,
                       $thumbnail,
                       $thumbnail_w,
                       $thumbnail_h,
                       $spoiler) {

    $hash = ($hash == null ? 'null' : "'$hash'");
    $spoiler = ($spoiler ? '1' : '0');

    $query = "call sp_images_add($hash, '$name', $widht, $height, $size, '$thumbnail', $thumbnail_w, $thumbnail_h, $spoiler)";
    $result = mysqli_query($link, $query);
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return is_int($row['id']) ? $row['id'] : kotoba_intval($row['id']);
}
/**
 * Get images.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @return array
 * images.
 */
function db_images_get_by_board($link, $board_id) {
    $result = mysqli_query($link, "call sp_images_get_by_board($board_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $images = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != null) {
            array_push($images,
                       array('id' => $row['id'],
                             'hash' => $row['hash'],
                             'name' => $row['name'],
                             'widht' => $row['widht'],
                             'height' => $row['height'],
                             'size' => $row['size'],
                             'thumbnail' => $row['thumbnail'],
                             'thumbnail_w' => $row['thumbnail_w'],
                             'thumbnail_h' => $row['thumbnail_h'],
                             'spoiler' => $row['spoiler'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $images;
}
/**
 * Get images.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * images.
 */
function db_images_get_by_post($link, $post_id) {
	$result = mysqli_query($link, "call sp_images_get_by_post($post_id)");
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

	$images = array();
	if (mysqli_affected_rows($link) > 0) {
		while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
			array_push($images,
                       array('id' => $row['id'],
                             'hash' => $row['hash'],
                             'name' => $row['name'],
                             'widht' => $row['widht'],
                             'height' => $row['height'],
                             'size' => $row['size'],
                             'thumbnail' => $row['thumbnail'],
                             'thumbnail_w' => $row['thumbnail_w'],
                             'thumbnail_h' => $row['thumbnail_h'],
                             'spoiler' => $row['spoiler'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE));
        }
    }

	mysqli_free_result($result);
	db_cleanup_link($link);
	return $images;
}
/**
 * Get thread images.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @return array
 * images.
 */
function db_images_get_by_thread($link, $thread_id) {
    $result = mysqli_query($link, "call sp_images_get_by_thread($thread_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $images = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != null) {
            array_push($images,
                array('id' => $row['id'],
                      'hash' => $row['hash'],
                      'name' => $row['name'],
                      'widht' => $row['widht'],
                      'height' => $row['height'],
                      'size' => $row['size'],
                      'thumbnail' => $row['thumbnail'],
                      'thumbnail_w' => $row['thumbnail_w'],
                      'thumbnail_h' => $row['thumbnail_h'],
                      'spoiler' => $row['spoiler'],
                      'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $images;
}
/**
 * Get dangling images.
 * @param MySQLi $link Link to database.
 * @return array
 * images.
 */
function db_images_get_dangling($link) {
    $result = mysqli_query($link, 'call sp_images_get_dangling()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $images = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != null) {
            array_push( $images,
                        array('id' => $row['id'],
                              'hash' => $row['hash'],
                              'name' => $row['name'],
                              'widht' => $row['widht'],
                              'height' => $row['height'],
                              'size' => $row['size'],
                              'thumbnail' => $row['thumbnail'],
                              'thumbnail_w' => $row['thumbnail_w'],
                              'thumbnail_h' => $row['thumbnail_h'],
                              'spoiler' => $row['spoiler'],
                              'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $images;
}
/**
 * Get same images.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @param int $user_id User id.
 * @param string $image_hash Image file hash.
 * @return array
 * images.
 */
function db_images_get_same($link, $board_id, $user_id, $image_hash) {
    $result = mysqli_query($link, "call sp_images_get_same($board_id, $user_id, '$image_hash')");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $images = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            if (!isset($thread_data[$row['thread_id']])) {
                $thread_data[$row['thread_id']] = array('id' => $row['thread_id'],
                                                        'board' => $row['thread_board'],
                                                        'original_post' => $row['thread_original_post'],
                                                        'bump_limit' => $row['thread_bump_limit'],
                                                        'sage' => $row['thread_sage'],
                                                        'sticky' => $row['thread_sticky'],
                                                        'with_attachments' => $row['thread_with_attachments']);
            }
            if (!isset($post_data[$row['post_id']])) {
                $post_data[$row['post_id']] = array('id' => $row['post_id'],
                                                    'board' => $row['post_board'],
                                                    'thread' => &$thread_data[$row['thread_id']],
                                                    'number' => $row['post_number'],
                                                    'user' => $row['post_user'],
                                                    'password' => $row['post_password'],
                                                    'name' => $row['post_name'],
                                                    'tripcode' => $row['post_tripcode'],
                                                    'ip' => $row['post_ip'],
                                                    'subject' => $row['post_subject'],
                                                    'date_time' => $row['post_date_time'],
                                                    'text`' => $row['post_text'],
                                                    'sage' => $row['post_sage']);
            }
            array_push($images,
                       array('id' => $row['image_id'],
                             'hash' => $row['image_hash'],
                             'name' => $row['image_name'],
                             'widht' => $row['image_widht'],
                             'height' => $row['image_height'],
                             'size' => $row['image_size'],
                             'thumbnail' => $row['image_thumbnail'],
                             'thumbnail_w' => $row['image_thumbnail_w'],
                             'thumbnail_h' => $row['image_thumbnail_h'],
                             'spoiler' => $row['image_spoiler'],
                             'post' => &$post_data[$row['post_id']],
                             'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE,
                             'view' => $row['view']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $images;
}

/* ************
 * Languages. *
 **************/

/**
 * Add language.
 * @param MySQLi $link Link to database.
 * @param string $code ISO_639-2 code.
 */
function db_languages_add($link, $code) {
    $result = mysqli_query($link, "call sp_languages_add('$code')");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete language.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_languages_delete($link, $id) {
    $result = mysqli_query($link, "call sp_languages_delete($id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get languages.
 * @param MySQLi $link Link to database.
 * @return array
 * languages.
 */
function db_languages_get_all($link) {
    $result = mysqli_query($link, 'call sp_languages_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $languages = array();
    if (mysqli_affected_rows($link) > 0) {
        while( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($languages, array('id' => $row['id'],
                                         'code' => $row['code']));
        }
    }
    else {
        throw new NodataException(NodataException::$messages['LANGUAGES_NOT_EXIST']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $languages;
}

/* ********
 * Links. *
 **********/

/**
 * Add link.
 * @param MySQLi $link Link to database.
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
function db_links_add($link,
                      $url,
                      $widht,
                      $height,
                      $size,
                      $thumbnail,
                      $thumbnail_w,
                      $thumbnail_h) {

    $query = "call sp_links_add('$url', $widht, $height, $size, '$thumbnail', $thumbnail_w, $thumbnail_h)";
    $result = mysqli_query($link, $query);
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return is_int($row['id']) ? $row['id'] : kotoba_intval($row['id']);
}
/**
 * Get links.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * links.
 */
function db_links_get_by_post($link, $post_id) {
    $result = mysqli_query($link, "call sp_links_get_by_post($post_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $links = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($links,
                       array('id' => $row['id'],
                             'url' => $row['url'],
                             'widht' => $row['widht'],
                             'height' => $row['height'],
                             'size' => $row['size'],
                             'thumbnail' => $row['thumbnail'],
                             'thumbnail_w' => $row['thumbnail_w'],
                             'thumbnail_h' => $row['thumbnail_h'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_LINK));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $links;
}
/**
 * Get thread links.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @return array
 * links.
 */
function db_links_get_by_thread($link, $thread_id) {
    $result = mysqli_query($link, "call sp_links_get_by_thread($thread_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $links = array();
    if(mysqli_affected_rows($link) > 0) {
        while(($row = mysqli_fetch_assoc($result)) != null) {
            array_push($links,
                array('id' => $row['id'],
                      'url' => $row['url'],
                      'widht' => $row['widht'],
                      'height' => $row['height'],
                      'size' => $row['size'],
                      'thumbnail' => $row['thumbnail'],
                      'thumbnail_w' => $row['thumbnail_w'],
                      'thumbnail_h' => $row['thumbnail_h'],
                      'attachment_type' => Config::ATTACHMENT_TYPE_LINK));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $links;
}
/**
 * Get dangling links.
 * @param MySQLi $link Link to database.
 * @return array
 * links.
 */
function db_links_get_dangling($link) {
    $result = mysqli_query($link, 'call sp_links_get_dangling()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $links = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push( $links,
                        array('id' => $row['id'],
                              'url' => $row['url'],
                              'widht' => $row['widht'],
                              'height' => $row['height'],
                              'size' => $row['size'],
                              'thumbnail' => $row['thumbnail'],
                              'thumbnail_w' => $row['thumbnail_w'],
                              'thumbnail_h' => $row['thumbnail_h'],
                              'attachment_type' => Config::ATTACHMENT_TYPE_LINK));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $links;
}

/* *****************
 * Macrochan tags. *
 *******************/

/**
 * Добавляет тег макрочана.
 * @param MySQLi $link Связь с базой данных.
 * @param string $name Имя.
 */
function db_macrochan_tags_add($link, $name) { // Java CC
    if ($name == null) { // Пустая строка тоже null.
        $name = 'null';
    } else {
        $name = '\'' . $name . '\'';
    }

    $result = mysqli_query($link,
            'call sp_macrochan_tags_add(' . $name . ')');
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
/**
 * Delete tag.
 * @param MySQLi $link Link to database.
 * @param string $name Tag name.
 */
function db_macrochan_tags_delete_by_name($link, $name) {
    $name = $name == null ? 'null' : "'$name'";
    $result = mysqli_query($link, "call sp_macrochan_tags_delete_by_name($name)");
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
/**
 * Get macrochan tags.
 * @param MySQLi $link Link to database.
 * @return array
 * macrochan tags.
 */
function db_macrochan_tags_get_all($link) {
    $result = mysqli_query($link, 'call sp_macrochan_tags_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $tags = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != null) {
            array_push($tags, array('id' => $row['id'],
                                    'name' => $row['name']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $tags;
}

/* *******************
 * Macrochan images. *
 *********************/

/**
 * Add macrochan image.
 * @param MySQLi $link Link to database.
 * @param string $name Name.
 * @param int $width Width.
 * @param int $height Height.
 * @param int $size Size in bytes.
 * @param string $thumbnail Thumbnail.
 * @param int $thumbnail_w Thumbnail width.
 * @param int $thumbnail_h Thumbnail height.
 */
function db_macrochan_images_add($link, $name, $width, $height, $size, $thumbnail, $thumbnail_w, $thumbnail_h) {
    $name = $name == null ? 'null' : "'$name'";
    $thumbnail = $thumbnail == null ? 'null' : "'$thumbnail'";
    $result = mysqli_query($link, "call sp_macrochan_images_add($name,
                                                                $width,
                                                                $height,
                                                                $size,
                                                                $thumbnail,
                                                                $thumbnail_w,
                                                                $thumbnail_h)");
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
/**
 * Delete macrochan image.
 * @param MySQLi $link Link to database.
 * @param string $name Image name.
 */
function db_macrochan_images_delete_by_name($link, $name) {
    $name = $name == null ? 'null' : "'$name'";
    $result = mysqli_query($link, "call sp_macrochan_images_delete_by_name($name)");
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
/**
 * Get macrochan images.
 * @param MySQLi $link Link to database.
 * @return array
 * macrochan images.
 */
function db_macrochan_images_get_all($link) {
    $result = mysqli_query($link, 'call sp_macrochan_images_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }
    $images = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) !== null) {
            array_push($images,
                    array('id' => $row['id'],
                          'name' => $row['name'],
                          'width' => $row['width'],
                          'height' => $row['height'],
                          'size' => $row['size'],
                          'thumbnail' => $row['thumbnail'],
                          'thumbnail_w' => $row['thumbnail_w'],
                          'thumbnail_h' => $row['thumbnail_h']));
        }
    }
    mysqli_free_result($result);
    db_cleanup_link($link);
    return $images;
}
/**
 * Get random macrochan image.
 * @param MySQLi $link Link to database.
 * @param string $name Tag name.
 * @return array
 * macrochan image.
 */
function db_macrochan_images_get_random($link, $name) {
    $result = mysqli_query($link, "call sp_macrochan_images_get_random('$name')");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $images = null;
    if (mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        $images['id'] = $row['id'];
        $images['name'] = $row['name'];
        $images['width'] = $row['width'];
        $images['height'] = $row['height'];
        $images['size'] = $row['size'];
        $images['thumbnail'] = $row['thumbnail'];
        $images['thumbnail_w'] = $row['thumbnail_w'];
        $images['thumbnail_h'] = $row['thumbnail_h'];
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $images;
}

/* **********************************
 * Macrochan tags images relations. *
 ************************************/

/**
 * Add tag image relation.
 * @param string $tag_name Macrochan tag name.
 * @param string $image_name Macrochan image name.
 */
function db_macrochan_tags_images_add($link, $tag_name, $image_name) {
    $tag_name = $tag_name == null ? 'null' : "'$tag_name'";
    $image_name = $image_name == null ? 'null' : "'$image_name'";
    $result = mysqli_query($link, "call sp_macrochan_tags_images_add($tag_name, $image_name)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get tag image relation.
 * @param MySQLi $link Link to database.
 * @param string $tag_name Macrochan tag name.
 * @param string $image_name Macrochan image name.
 * @return array|null
 * tag image relation or NULL if it not exist.
 */
function db_macrochan_tags_images_get($link, $tag_name, $image_name) {
    $tag_name = $tag_name == null ? 'null' : "'$tag_name'";
    $image_name = $image_name == null ? 'null' : "'$image_name'";
    $result = mysqli_query($link, "call sp_macrochan_tags_images_get($tag_name, $image_name)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $tags_images = null;
    if (mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        $tags_images['tag'] = $row['tag'];
        $tags_images['image'] = $row['image'];
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $tags_images;
}
/**
 * Получает все связи тегов и изображениями макрочана.
 * @param MySQLi $link Связь с базой данных.
 * @return array
 * Возвращает связи тегов и изображениями макрочана:<p>
 * 'tag' - Идентификатор тега макрочана.<br>
 * 'image' - Идентификатор изображения макрочана.</p>
 */
function db_macrochan_tags_images_get_all($link) { // Java CC
    $result = mysqli_query($link, 'call sp_macrochan_tags_images_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }
    $tags_images = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) !== null) {
            array_push($tags_images,
                    array('tag' => $row['tag'],
                          'image' => $row['image']));
        }
    }
    mysqli_free_result($result);
    db_cleanup_link($link);
    return $tags_images;
}

/* *******************
 * Popdown handlers. *
 *********************/

/**
 * Add popdown handeler.
 * @param MySQLi $link Link to database.
 * @param string $name Popdown handeler name.
 */
function db_popdown_handlers_add($link, $name) {
    if (!mysqli_query($link, "call sp_popdown_handlers_add('$name')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete popdown handeler.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_popdown_handlers_delete($link, $id) {
    if (!mysqli_query($link, "call sp_popdown_handlers_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get popdown hanglers.
 * @param MySQLi $link Link to database.
 * @return array
 * popdown hanglers.
 */
function db_popdown_handlers_get_all($link) {
    $result = mysqli_query($link, 'call sp_popdown_handlers_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $popdown_handlers = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($popdown_handlers,
                       array('id' => $row['id'], 'name' => $row['name']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $popdown_handlers;
}

/* ********
 * Posts. *
 **********/

/**
 * Add post.
 * @param MySQLi $link Link to database.
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
function db_posts_add($link,
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
                      $sage) {
    
    if ($sage === null) {
        $sage = 'null';
    }
    $text = ($text == null ? 'null' : "'$text'");
    $subject = ($subject == null ? 'null' : "'$subject'");
    $tripcode = ($tripcode == null ? 'null' : "'$tripcode'");
    $name = ($name == null ? 'null' : "'$name'");
    $password = ($password == null ? 'null' : "'$password'");

    $query = "call sp_posts_add($board_id, $thread_id, $user_id, $password, $name, $tripcode, $ip, $subject, '$date_time', $text, $sage)";
    $result = mysqli_query($link, $query);
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $post = null;
    if(mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        $post['id'] = $row['id'];
        $post['board'] = $row['board'];
        $post['thread'] = $row['thread'];
        $post['number'] = $row['number'];
        $post['password'] = $row['password'];
        $post['name'] = $row['name'];
        $post['tripcode'] = $row['tripcode'];
        $post['ip'] = $row['ip'];
        $post['subject'] = $row['subject'];
        $post['date_time'] = $row['date_time'];
        $post['text'] = $row['text'];
        $post['sage'] = $row['sage'];
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $post;
}
/**
 * Add text to the end of message.
 * @param MySQLi $link Link to database.
 * @param int $id Post id.
 * @param string $text Text.
 */
function db_posts_add_text_by_id($link, $id, $text) {
    if (!mysqli_query($link, "call sp_posts_edit_text_by_id($id, '$text')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Remove post.
 * @param MySQLi $link Link to database.
 * @param int $id Post id.
 */
function db_posts_delete($link, $id) {
    if (!mysqli_query($link, "call sp_posts_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete last posts.
 * @param MySQLi $link Link to database.
 * @param int $id Post id.
 * @param string $date_time Date.
 */
function db_posts_delete_last($link, $id, $date_time) {
    if(!mysqli_query($link, "call sp_posts_delete_last($id, '$date_time')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete marked posts.
 * @param MySQLi $link Link to database.
 */
function db_posts_delete_marked($link) {
    if (!mysqli_query($link, 'call sp_posts_delete_marked()')) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Получает все сообщения.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_posts_get_all($link)
{
	$result = mysqli_query($link, 'call sp_posts_get_all()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$posts = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($posts,
				array('id' => $row['id'],
						'board' => $row['board'],
						'board_name' => $row['board_name'],
						'thread' => $row['thread'],
						'thread_number' => $row['thread_number'],
						'number' => $row['number'],
						'password' => $row['password'],
						'name' => $row['name'],
						'tripcode' => $row['tripcode'],
						'ip' => $row['ip'],
						'subject' => $row['subject'],
						'date_time' => $row['date_time'],
						'text' => $row['text'],
						'sage' => $row['sage']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $posts;
}
/**
 * Получает номера всех сообщений с соотвествующими номерами нитей и именами досок.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает сообщения:<br>
 * 'post' - Номер сообщения.<br>
 * 'thread' - Номер нити.<br>
 * 'board' - Имя доски.
 */
function db_posts_get_all_numbers($link) { // Java CC
    $result = mysqli_query($link, 'call sp_posts_get_all_numbers()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $posts = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) !== null) {
            array_push($posts,
                array('post' => $row['post'],
                      'thread' => $row['thread'],
                      'board' => $row['board']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $posts;
}
/**
 * Get posts.
 * @param MySQLi $link Link to database.
 * @param array $boards Boards.
 * @return array
 * posts.
 */
function db_posts_get_by_boards($link, $boards) {
    $posts = array();
    foreach ($boards as $b) {
        $result = mysqli_query($link, "call sp_posts_get_by_board({$b['id']})");
        if (!$result) {
            throw new CommonException(mysqli_error($link));
        }

        if (mysqli_affected_rows($link) > 0) {
            while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
                if (!isset ($tmp_boards["{$row['post_board']}"])) {
                    $tmp_boards["{$row['post_board']}"] =
                        array('id' => $row['board_id'],
                              'name' => $row['board_name'],
                              'title' => $row['board_title'],
                              'annotation' => $row['board_annotation'],
                              'bump_limit' => $row['board_bump_limit'],
                              'force_anonymous' => $row['board_force_anonymous'],
                              'default_name' => $row['board_default_name'],
                              'with_attachments' => $row['board_with_attachments'],
                              'enable_macro' => $row['board_enable_macro'],
                              'enable_youtube' => $row['board_enable_youtube'],
                              'enable_captcha' => $row['board_enable_captcha'],
                              'enable_translation' => $row['board_enable_translation'],
                              'enable_geoip' => $row['board_enable_geoip'],
                              'enable_shi' => $row['board_enable_shi'],
                              'enable_postid' => $row['board_enable_postid'],
                              'same_upload' => $row['board_same_upload'],
                              'popdown_handler' => $row['board_popdown_handler'],
                              'category' => $row['board_category']);
                }
                if (!isset ($tmp_threads["{$row['post_thread']}"])) {
                    $tmp_threads["{$row['post_thread']}"] =
                        array('id' => $row['thread_id'],
                              'board' => $row['thread_board'],
                              'original_post' => $row['thread_original_post'],
                              'bump_limit' => $row['thread_bump_limit'],
                              'sage' => $row['thread_sage'],
                              'sticky' => $row['thread_sticky'],
                              'with_attachments' => $row['thread_with_attachments']);
                }
                array_push($posts, array('id' => $row['post_id'],
                                         'board' => &$tmp_boards["{$row['post_board']}"],
                                         'thread' => &$tmp_threads["{$row['post_thread']}"],
                                         'number' => $row['post_number'],
                                         'user' => $row['post_user'],
                                         'password' => $row['post_password'],
                                         'name' => $row['post_name'],
                                         'tripcode' => $row['post_tripcode'],
                                         'ip' => $row['post_ip'],
                                         'subject' => $row['post_subject'],
                                         'date_time' => $row['post_date_time'],
                                         'text' => $row['post_text'],
                                         'sage' => $row['post_sage'],
                                         'attachments_count' => $row['attachments_count']));
            }
        }

        // Cleanup.
        mysqli_free_result($result);
        db_cleanup_link($link);
    }
    return $posts;
}
/**
 * Get posts.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @return array
 * posts.
 */
function db_posts_get_by_thread($link, $thread_id) {
    $result = mysqli_query($link, "call sp_posts_get_by_thread($thread_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $posts = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            if (!isset($board_data[$row['board_id']])) {
                $board_data[$row['board_id']] = array('id' => $row['board_id'],
                                                        'name' => $row['board_name'],
                                                        'title' => $row['board_title'],
                                                        'annotation' => $row['board_annotation'],
                                                        'bump_limit' => $row['board_bump_limit'],
                                                        'force_anonymous' => $row['board_force_anonymous'],
                                                        'default_name' => $row['board_default_name'],
                                                        'with_attachments' => $row['board_with_attachments'],
                                                        'enable_macro' => $row['board_enable_macro'],
                                                        'enable_youtube' => $row['board_enable_youtube'],
                                                        'enable_captcha' => $row['board_enable_captcha'],
                                                        'enable_translation' => $row['board_enable_translation'],
                                                        'enable_geoip' => $row['board_enable_geoip'],
                                                        'enable_shi' => $row['board_enable_shi'],
                                                        'enable_postid' => $row['board_enable_postid'],
                                                        'same_upload' => $row['board_same_upload'],
                                                        'popdown_handler' => $row['board_popdown_handler'],
                                                        'category' => $row['board_category']);
            }
            if (!isset($thread_data[$row['thread_id']])) {
                $thread_data[$row['thread_id']] = array('id' => $row['thread_id'],
                                                         'board' => $row['thread_board'],
                                                         'original_post' => $row['thread_original_post'],
                                                         'bump_limit' => $row['thread_bump_limit'],
                                                         'sage' => $row['thread_sage'],
                                                         'sticky' => $row['thread_sticky'],
                                                         'with_attachments' => $row['thread_with_attachments']);
            }
            array_push($posts,
                       array('id' => $row['post_id'],
                             'board' => &$board_data[$row['board_id']],
                             'thread' => &$thread_data[$row['thread_id']],
                             'number' => $row['post_number'],
                             'password' => $row['post_password'],
                             'name' => $row['post_name'],
                             'tripcode' => $row['post_tripcode'],
                             'ip' => $row['post_ip'],
                             'subject' => $row['post_subject'],
                             'date_time' => $row['post_date_time'],
                             'text' => $row['post_text'],
                             'sage' => $row['post_sage'],
                             'user' => $row['post_user']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $posts;
}
/**
 * Get reported posts.
 * @param MySQLi $link Link to database.
 * @param array $boards Boards.
 * @return array
 * posts.
 */
function db_posts_get_reported_by_boards($link, $boards) {
    $posts = array();
    foreach ($boards as $b) {
        $result = mysqli_query($link, "call sp_posts_get_reported_by_board({$b['id']})");
        if (!$result) {
            throw new CommonException(mysqli_error($link));
        }

        if (mysqli_affected_rows($link) > 0) {
            while( ($row = mysqli_fetch_assoc($result)) != NULL) {
                if (!isset($board_data[$row['board_id']])) {
                    $board_data[$row['board_id']] = array('id' => $row['board_id'],
                                                            'name' => $row['board_name'],
                                                            'title' => $row['board_title'],
                                                            'annotation' => $row['board_annotation'],
                                                            'bump_limit' => $row['board_bump_limit'],
                                                            'force_anonymous' => $row['board_force_anonymous'],
                                                            'default_name' => $row['board_default_name'],
                                                            'with_attachments' => $row['board_with_attachments'],
                                                            'enable_macro' => $row['board_enable_macro'],
                                                            'enable_youtube' => $row['board_enable_youtube'],
                                                            'enable_captcha' => $row['board_enable_captcha'],
                                                            'enable_translation' => $row['board_enable_translation'],
                                                            'enable_geoip' => $row['board_enable_geoip'],
                                                            'enable_shi' => $row['board_enable_shi'],
                                                            'enable_postid' => $row['board_enable_postid'],
                                                            'same_upload' => $row['board_same_upload'],
                                                            'popdown_handler' => $row['board_popdown_handler'],
                                                            'category' => $row['board_category']);
                }
                if (!isset($thread_data[$row['thread_id']])) {
                    $thread_data[$row['thread_id']] = array('id' => $row['thread_id'],
                                                             'board' => $row['thread_board'],
                                                             'original_post' => $row['thread_original_post'],
                                                             'bump_limit' => $row['thread_bump_limit'],
                                                             'sage' => $row['thread_sage'],
                                                             'sticky' => $row['thread_sticky'],
                                                             'with_attachments' => $row['thread_with_attachments']);
                }
                array_push($posts,
                           array('id' => $row['post_id'],
                                 'board' => &$board_data[$row['board_id']],
                                 'thread' => &$thread_data[$row['thread_id']],
                                 'number' => $row['post_number'],
                                 'password' => $row['post_password'],
                                 'name' => $row['post_name'],
                                 'tripcode' => $row['post_tripcode'],
                                 'ip' => $row['post_ip'],
                                 'subject' => $row['post_subject'],
                                 'date_time' => $row['post_date_time'],
                                 'text' => $row['post_text'],
                                 'sage' => $row['post_sage'],
                                 'user' => $row['post_user']));
            }
        }

        mysqli_free_result($result);
        db_cleanup_link($link);
    }

    return $posts;
}
/**
 * Get visible post.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @param int $user_id User id.
 * @return array
 * post.
 */
function db_posts_get_visible_by_id($link, $post_id, $user_id) {
    $result = mysqli_query($link, "call sp_posts_get_visible_by_id($post_id, $user_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $post = null;
    if (mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != null) {
        $post['id'] = $row['post_id'];
        $post['number'] = $row['post_number'];
        $post['user'] = $row['post_user'];
        $post['password'] = $row['post_password'];
        $post['name'] = $row['post_name'];
        $post['tripcode'] = $row['post_tripcode'];
        $post['ip'] = $row['post_ip'];
        $post['subject'] = $row['post_subject'];
        $post['date_time'] = $row['post_date_time'];
        $post['text'] = $row['post_text'];
        $post['sage'] = $row['post_sage'];

        $post['board']['id'] = $row['board_id'];
        $post['board']['name'] = $row['board_name'];
        $post['board']['title'] = $row['board_title'];
        $post['board']['annotation'] = $row['board_annotation'];
        $post['board']['bump_limit'] = $row['board_bump_limit'];
        $post['board']['force_anonymous'] = $row['board_force_anonymous'];
        $post['board']['default_name'] = $row['board_default_name'];
        $post['board']['with_attachments'] = $row['board_with_attachments'];
        $post['board']['enable_macro'] = $row['board_enable_macro'];
        $post['board']['enable_youtube'] = $row['board_enable_youtube'];
        $post['board']['enable_captcha'] = $row['board_enable_captcha'];
        $post['board']['enable_translation'] = $row['board_enable_translation'];
        $post['board']['enable_geoip'] = $row['board_enable_geoip'];
        $post['board']['enable_shi'] = $row['board_enable_shi'];
        $post['board']['enable_postid'] = $row['board_enable_postid'];
        $post['board']['same_upload'] = $row['board_same_upload'];
        $post['board']['popdown_handler'] = $row['board_popdown_handler'];
        $post['board']['category'] = $row['board_category'];

        $post['thread']['id'] = $row['thread_id'];
        $post['thread']['board'] = $row['thread_board'];
        $post['thread']['original_post'] = $row['thread_original_post'];
        $post['thread']['bump_limit'] = $row['thread_bump_limit'];
        $post['thread']['sage'] = $row['thread_sage'];
        $post['thread']['sticky'] = $row['thread_sticky'];
        $post['thread']['with_attachments'] = $row['thread_with_attachments'];
    }

    if($post === null) {
        throw new NodataException(NodataException::$messages['POST_NOT_FOUND'], $post_id, $user_id);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $post;
}
/**
 * Get visible post.
 * @param MySQLi $link Link to database.
 * @param string $board_name Board name.
 * @param int $post_number Post number.
 * @param int $user_id User id.
 * @return array
 * post.
 */
function db_posts_get_visible_by_number($link, $board_name, $post_number, $user_id) {
    // Query.
    $result = mysqli_query($link, "call sp_posts_get_visible_by_number('$board_name', $post_number, $user_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    // Collect data from query result.
    $post = NULL;
    if(mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        $board_data = array('id' => $row['board_id'],
                            'name' => $row['board_name'],
                            'title' => $row['board_title'],
                            'annotation' => $row['board_annotation'],
                            'bump_limit' => $row['board_bump_limit'],
                            'force_anonymous' => $row['board_force_anonymous'],
                            'default_name' => $row['board_default_name'],
                            'with_attachments' => $row['board_with_attachments'],
                            'enable_macro' => $row['board_enable_macro'],
                            'enable_youtube' => $row['board_enable_youtube'],
                            'enable_captcha' => $row['board_enable_captcha'],
                            'enable_translation' => $row['board_enable_translation'],
                            'enable_geoip' => $row['board_enable_geoip'],
                            'enable_shi' => $row['board_enable_shi'],
                            'enable_postid' => $row['board_enable_postid'],
                            'same_upload' => $row['board_same_upload'],
                            'popdown_handler' => $row['board_popdown_handler'],
                            'category' => $row['board_category']);

        $thread_data = array('id' => $row['thread_id'],
                             'board' => $row['thread_board'],
                             'original_post' => $row['thread_original_post'],
                             'bump_limit' => $row['thread_bump_limit'],
                             'sage' => $row['thread_sage'],
                             'sticky' => $row['thread_sticky'],
                             'with_attachments' => $row['thread_with_attachments']);

        $post['id'] = $row['post_id'];
        $post['board'] = &$board_data;
        $post['thread'] = &$thread_data;
        $post['number'] = $row['post_number'];
        $post['user'] = $row['post_user'];
        $post['password'] = $row['post_password'];
        $post['name'] = $row['post_name'];
        $post['tripcode'] = $row['post_tripcode'];
        $post['ip'] = $row['post_ip'];
        $post['subject'] = $row['post_subject'];
        $post['date_time'] = $row['post_date_time'];
        $post['text'] = $row['post_text'];
        $post['sage'] = $row['post_sage'];
    }
    if ($post === NULL) {
        throw new NodataException(NodataException::$messages['POST_NOT_FOUND']);
    }

    // Cleanup.
    mysqli_free_result($result);
    db_cleanup_link($link);
    return $post;
}
/**
 * Get posts visible to user and filter it.
 * @param MySQLi $link Link to database.
 * @param array $threads Threads.
 * @param int $user_id User id.
 * @param Object $filter Filter function. First two arguments must be thread and post.
 * @param array $args Filter arguments.
 * @return array
 * posts.
 */
function db_posts_get_visible_filtred_by_threads($link, $threads, $user_id, $filter, $args) {
    $posts = array();
    $arg = count($args);

    foreach ($threads as $thread) {
        $result = mysqli_query($link, "call sp_posts_get_visible_by_thread({$thread['id']}, $user_id)");
        if (!$result) {
            throw new CommonException(mysqli_error($link));
        }

        if (mysqli_affected_rows($link) > 0) {
            $args[$arg] = $thread;
            while ( ($row = mysqli_fetch_assoc($result)) != null) {
                $args[$arg + 1] = $row;
                if (call_user_func_array($filter, $args)) {
                    if (!isset ($tmp_boards[$row['board_id']])) {
                        $tmp_boards[$row['board_id']] =
                            array('id' => $row['board_id'],
                                  'name' => $row['board_name'],
                                  'title' => $row['board_title'],
                                  'annotation' => $row['board_annotation'],
                                  'bump_limit' => $row['board_bump_limit'],
                                  'force_anonymous' => $row['board_force_anonymous'],
                                  'default_name' => $row['board_default_name'],
                                  'with_attachments' => $row['board_with_attachments'],
                                  'enable_macro' => $row['board_enable_macro'],
                                  'enable_youtube' => $row['board_enable_youtube'],
                                  'enable_captcha' => $row['board_enable_captcha'],
                                  'enable_translation' => $row['board_enable_translation'],
                                  'enable_geoip' => $row['board_enable_geoip'],
                                  'enable_shi' => $row['board_enable_shi'],
                                  'enable_postid' => $row['board_enable_postid'],
                                  'same_upload' => $row['board_same_upload'],
                                  'popdown_handler' => $row['board_popdown_handler'],
                                  'category' => $row['board_category']);
                    }
                    if (!isset ($tmp_threads[$row['thread_id']])) {
                        $tmp_threads[$row['thread_id']] =
                            array('id' => $row['thread_id'],
                                  'board' => $row['thread_board'],
                                  'original_post' => $row['thread_original_post'],
                                  'bump_limit' => $row['thread_bump_limit'],
                                  'sage' => $row['thread_sage'],
                                  'sticky' => $row['thread_sticky'],
                                  'with_attachments' => $row['thread_with_attachments']);
                    }
                    array_push($posts,
                               array('id' => $row['post_id'],
                                     'board' => &$tmp_boards[$row['board_id']],
                                     'thread' => &$tmp_threads[$row['thread_id']],
                                     'number' => $row['post_number'],
                                     'user' => $row['post_user'],
                                     'password' => $row['post_password'],
                                     'name' => $row['post_name'],
                                     'tripcode' => $row['post_tripcode'],
                                     'ip' => $row['post_ip'],
                                     'subject' => $row['post_subject'],
                                     'date_time' => $row['post_date_time'],
                                     'text' => $row['post_text'],
                                     'sage' => $row['post_sage']));
                }
            }
        }

        mysqli_free_result($result);
        db_cleanup_link($link);
    }

    return $posts;
}
/**
 * Search posts by keyword.
 * @param MySQLi $link Link to database.
 * @param array $boards Boards.
 * @param string $keyword Keyword.
 * @param int $user User id.
 * @return array
 * posts.
 */
function db_posts_search_visible_by_boards($link, $boards, $keyword, $user) {
    $posts = array();
    foreach ($boards as $b) {
        $query = "call sp_posts_search_visible_by_board({$b['id']}, '$keyword', $user)";
        $result = mysqli_query($link, $query);
        if (!$result) {
            throw new CommonException(mysqli_error($link));
        }

        if (mysqli_affected_rows($link) > 0) {
            while (($row = mysqli_fetch_assoc($result)) != null) {
                if (!isset ($tmp_boards["{$row['post_board']}"])) {
                    $tmp_boards["{$row['post_board']}"] =
                        array('id' => $row['board_id'],
                              'name' => $row['board_name'],
                              'title' => $row['board_title'],
                              'annotation' => $row['board_annotation'],
                              'bump_limit' => $row['board_bump_limit'],
                              'force_anonymous' => $row['board_force_anonymous'],
                              'default_name' => $row['board_default_name'],
                              'with_attachments' => $row['board_with_attachments'],
                              'enable_macro' => $row['board_enable_macro'],
                              'enable_youtube' => $row['board_enable_youtube'],
                              'enable_captcha' => $row['board_enable_captcha'],
                              'enable_translation' => $row['board_enable_translation'],
                              'enable_geoip' => $row['board_enable_geoip'],
                              'enable_shi' => $row['board_enable_shi'],
                              'enable_postid' => $row['board_enable_postid'],
                              'same_upload' => $row['board_same_upload'],
                              'popdown_handler' => $row['board_popdown_handler'],
                              'category' => $row['board_category']);
                }
                if (!isset ($tmp_threads["{$row['post_thread']}"])) {
                    $tmp_threads["{$row['post_thread']}"] =
                        array('id' => $row['thread_id'],
                              'board' => $row['thread_board'],
                              'original_post' => $row['thread_original_post'],
                              'bump_limit' => $row['thread_bump_limit'],
                              'sage' => $row['thread_sage'],
                              'sticky' => $row['thread_sticky'],
                              'with_attachments' => $row['thread_with_attachments']);
                }
                array_push($posts, array('id' => $row['post_id'],
                                         'board' => &$tmp_boards["{$row['post_board']}"],
                                         'thread' => &$tmp_threads["{$row['post_thread']}"],
                                         'number' => $row['post_number'],
                                         'user' => $row['post_user'],
                                         'password' => $row['post_password'],
                                         'name' => $row['post_name'],
                                         'tripcode' => $row['post_tripcode'],
                                         'ip' => $row['post_ip'],
                                         'subject' => $row['post_subject'],
                                         'date_time' => $row['post_date_time'],
                                         'text' => $row['post_text'],
                                         'sage' => $row['post_sage']));
            }
        }

        mysqli_free_result($result);
        db_cleanup_link($link);
    }

    return $posts;
}

/* ************************
 * Posts files relations. *
 **************************/

/**
 * Add post file relation.
 * @param MySQLi $link Link to database.
 * @param int $post Post id.
 * @param int file File id.
 * @param int $deleted Mark to delete.
 */
function db_posts_files_add($link, $post, $file, $deleted) {
    if (!mysqli_query($link, "call sp_posts_files_add($post, $file, $deleted)")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Remove post files relations.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 */
function db_posts_files_delete_by_post($link, $post_id) {
    if (!mysqli_query($link, "call sp_posts_files_delete_by_post($post_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete marked posts files relations.
 */
function db_posts_files_delete_marked($link) {
    if (!mysqli_query($link, 'call sp_posts_files_delete_marked()')) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get posts files relations.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * posts files relations.
 */
function db_posts_files_get_by_post($link, $post_id) {
    $result = mysqli_query($link, "call sp_posts_files_get_by_post($post_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $posts_files = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($posts_files,
                       array('post' => $row['post'],
                             'file' => $row['file'],
                             'deleted' => $row['deleted'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_FILE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $posts_files;
}

/* *************************
 * Posts images relations. *
 ***************************/

/**
 * Add post image relation.
 * @param MySQLi $link Link to database.
 * @param int $post Post id.
 * @param int $image Image id.
 * @param int $deleted Mark to delete.
 */
function db_posts_images_add($link, $post, $image, $deleted) {
    if(!mysqli_query($link, "call sp_posts_images_add($post, $image, $deleted)")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Delete post images relations.
 * @param MySQLi $link Link to database.
 * @param int $post Post id.
 */
function db_posts_images_delete_by_post($link, $post_id) {
    if (!mysqli_query($link, "call sp_posts_images_delete_by_post($post_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete marked posts images relations.
 */
function db_posts_images_delete_marked($link) {
    if (!mysqli_query($link, 'call sp_posts_images_delete_marked()')) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get posts images relations.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * posts images relations.
 */
function db_posts_images_get_by_post($link, $post_id) {
    $result = mysqli_query($link, "call sp_posts_images_get_by_post($post_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $posts_images = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) !== null) {
            array_push($posts_images,
                       array('post' => $row['post'],
                             'image' => $row['image'],
                             'deleted' => $row['deleted'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $posts_images;
}

/* ************************
 * Posts links relations. *
 **************************/

/**
 * Add post link relation.
 * @param MySQLi $link Link to database.
 * @param int $post Post id.
 * @param int $link Link id.
 * @param int $deleted Mark to delete.
 */
function db_posts_links_add($link, $post, $posts_links_link, $deleted) {
    if(!mysqli_query($link, "call sp_posts_links_add($post, $posts_links_link, $deleted)")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Delete post links relations.
 * @param MySQLi $link Link to database.
 * @param int $post Post id.
 */
function db_posts_links_delete_by_post($link, $post_id) {
    if (!mysqli_query($link, "call sp_posts_links_delete_by_post($post_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete marked posts links relations.
 */
function db_posts_links_delete_marked($link) {
    if (!mysqli_query($link, 'call sp_posts_links_delete_marked()')) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get posts links relations.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * posts links relations.
 */
function db_posts_links_get_by_post($link, $post_id) {
    $result = mysqli_query($link, "call sp_posts_links_get_by_post($post_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $posts_links = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($posts_links,
                       array('post' => $row['post'],
                             'link' => $row['link'],
                             'deleted' => $row['deleted'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_LINK));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $posts_links;
}

/* *************************
 * Posts videos relations. *
 ***************************/

/**
 * Add post video relation.
 * @param MySQLi $link Link to database.
 * @param int $post Post id.
 * @param int $video Video id.
 * @param int $deleted Mark to delete.
 */
function db_posts_videos_add($link, $post, $video, $deleted) {
    if(!mysqli_query($link, "call sp_posts_videos_add($post, $video, $deleted)")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Delete post videos relations.
 * @param MySQLi $link Link to database.
 * @param int $post Post id.
 */
function db_posts_videos_delete_by_post($link, $post_id) {
    if (!mysqli_query($link, "call sp_posts_videos_delete_by_post($post_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete marked posts videos relations.
 */
function db_posts_videos_delete_marked($link) {
    if (!mysqli_query($link, 'call sp_posts_videos_delete_marked()')) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get posts videos relations.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * posts videos relations.
 */
function db_posts_videos_get_by_post($link, $post_id) {
    $result = mysqli_query($link, "call sp_posts_videos_get_by_post($post_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $posts_videos = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($posts_videos,
                       array('post' => $row['post'],
                             'video' => $row['video'],
                             'deleted' => $row['deleted'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_VIDEO));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $posts_videos;
}

/* **********
 * Reports. *
 ************/

/**
 * Add report.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 */
function db_reports_add($link, $post_id) {
	if (!mysqli_query($link, "call sp_reports_add($post_id)")) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
/**
 * Delete report.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 */
function db_reports_delete($link, $post_id) {
	if (!mysqli_query($link, "call sp_reports_delete($post_id)")) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
/**
 * 
 */
function db_reports_get_all($link) {
    $result = mysqli_query($link, 'call sp_reports_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $reports = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != null) {
            array_push($reports, array('post' => $row['post']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $reports;
}

/* *************
 * Spamfilter. *
 ***************/

/**
 * Add pattern to spamfilter.
 * @param MySQLi $link Link to database.
 * @param string $pattern Pattern.
 */
function db_spamfilter_add($link, $pattern) {
    if (!mysqli_query($link, "call sp_spamfilter_add('$pattern')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete pattern from spamfilter.
 * @param MySQLi $link Link to database.
 * @param int $id id.
 */
function db_spamfilter_delete($link, $id) {
	if (!mysqli_query($link, "call sp_spamfilter_delete($id)")) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
/**
 * Get spamfilter records.
 * @param MySQLi $link Link to database.
 * @return array
 * spamfilter records.
 */
function db_spamfilter_get_all($link) {
    $result = mysqli_query($link, 'call sp_spamfilter_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $patterns = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($patterns,
                       array('id' => $row['id'],
                             'pattern' => $row['pattern']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $patterns;
}

/* **************
 * Stylesheets. *
 ****************/

/**
 * Add stylesheet.
 * @param MySQLi $link Link to database.
 * @param string $name Stylesheet name.
 */
function db_stylesheets_add($link, $name) {
    if (!mysqli_query($link, "call sp_stylesheets_add('$name')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete stylesheet.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_stylesheets_delete($link, $id) {
    if (!mysqli_query($link, "call sp_stylesheets_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get stylesheets.
 * @param MySQLi $link Link to database.
 * @return array
 * stylesheets.
 */
function db_stylesheets_get_all($link) {
    $result = mysqli_query($link, 'call sp_stylesheets_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $stylesheets = array();
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($stylesheets,
                       array('id' => $row['id'],
                             'name' => $row['name']));
        }
    } else {
        throw new NodataException(NodataException::$messages['STYLESHEETS_NOT_EXIST']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $stylesheets;
}

/* ******************
 * Работа с нитями. *
 ********************/

/**
 * Добавляет нить. Если номер оригинального сообщения null, то будет создана
 * пустая нить.
 * @param MySQLi $link Связь с базой данных.
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
function db_threads_add($link, $board_id, $original_post, $bump_limit, $sage, $with_attachments) { // Java CC
    if ($original_post === null) {
        $original_post = 'null';
    }
    if ($bump_limit === null) {
        $bump_limit = 'null';
    }
    if ($with_attachments === null) {
        $with_attachments = 'null';
    }

    $result = mysqli_query($link, "call sp_threads_add($board_id, $original_post, $bump_limit, $sage, $with_attachments)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $thread = null;
    if (mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != null) {
        $thread = array('id' => $row['id'],
                        'board' => $row['board'],
                        'original_post' => $row['original_post'],
                        'bump_limit' => $row['bump_limit'],
                        'sage' => $row['sage'],
                        'sticky' => $row['sticky'],
                        'with_attachments' => $row['with_attachments']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $thread;
}
/**
 * Delete marked threads.
 */
function db_threads_delete_marked($link) {
    if (!mysqli_query($link, 'call sp_threads_delete_marked()')) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Edit thread.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @param int $bump_limit Thread specific bumplimit.
 * @param boolean $sage Sage flag.
 * @param boolean $sticky Sticky flag.
 * @param boolean $with_attachments Attachments flag.
 * @param boolean $closed Thread closed flag.
 */
function db_threads_edit($link, $thread_id, $bump_limit, $sticky, $sage, $with_attachments, $closed) {
    $bump_limit = $bump_limit == null ? 'null' : $bump_limit;
    $sticky = $sticky ? '1' : '0';
    $sage = $sage ? '1' : '0';
    $with_attachments = $with_attachments === null ? 'null' : ($with_attachments ? '1' : '0');
    $closed = $closed ? '1' : '0';
    $query = "call sp_threads_edit($thread_id, $bump_limit, $sticky, $sage, $with_attachments, $closed)";
    if (!mysqli_query($link, $query)) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Редактирует номер оригинального сообщения нити.
 * @param MySQLi $link Связь с базой данных.
 * @param int $id Идентификатор нити.
 * @param int $original_post Номер оригинального сообщения нити.
 */
function db_threads_edit_original_post($link, $id, $original_post) {
    if(!mysqli_query($link,
            "call sp_threads_edit_original_post($id, $original_post)")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Get threads.
 * @return array
 * threads.
 */
function db_threads_get_all($link) {
    $result = mysqli_query($link, 'call sp_threads_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $threads = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            if (!isset($board_data[$row['board_id']])) {
                $board_data[$row['board_id']] = array(  'id' => $row['board_id'],
                                                        'name' => $row['board_name'],
                                                        'title' => $row['board_title'],
                                                        'annotation' => $row['board_annotation'],
                                                        'bump_limit' => $row['board_bump_limit'],
                                                        'force_anonymous' => $row['board_force_anonymous'],
                                                        'default_name' => $row['board_default_name'],
                                                        'with_attachments' => $row['board_with_attachments'],
                                                        'enable_macro' => $row['board_enable_macro'],
                                                        'enable_youtube' => $row['board_enable_youtube'],
                                                        'enable_captcha' => $row['board_enable_captcha'],
                                                        'enable_translation' => $row['board_enable_translation'],
                                                        'enable_geoip' => $row['board_enable_geoip'],
                                                        'enable_shi' => $row['board_enable_shi'],
                                                        'enable_postid' => $row['board_enable_postid'],
                                                        'same_upload' => $row['board_same_upload'],
                                                        'popdown_handler' => $row['board_popdown_handler'],
                                                        'category' => $row['board_category']);
            }
            array_push($threads,
                       array('id' => $row['thread_id'],
                             'board' => &$board_data[$row['board_id']],
                             'original_post' => $row['thread_original_post'],
                             'bump_limit' => $row['thread_bump_limit'],
                             'sage' => $row['thread_sage'],
                             'sticky' => $row['thread_sticky'],
                             'with_attachments' => $row['thread_with_attachments'],
                             'closed' => $row['thread_closed']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $threads;
}
/**
 * Получает нити, помеченные для архивирования.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_archived($link)
{
	$result = mysqli_query($link, 'call sp_threads_get_archived()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$threads = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($threads,
				array('id' => $row['id'],
						'board' => $row['board'],
						'original_post' => $row['original_post'],
						'bump_limit' => $row['bump_limit'],
						'sage' => $row['sage'],
						'sticky' => $row['sticky'],
						'with_attachments' => $row['with_attachments']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Get thread.
 * @param MySQLi $link Link to database.
 * @param int $id Thread id.
 * @return array
 * thread.
 */
function db_threads_get_by_id($link, $id) {
    $result = mysqli_query($link, "call sp_threads_get_by_id($id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $thread = null;
    if(mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        $thread['id'] = $row['thread_id'];
        $thread['original_post'] = $row['thread_original_post'];
        $thread['bump_limit'] = $row['thread_bump_limit'];
        $thread['sage'] = $row['thread_sage'];
        $thread['sticky'] = $row['thread_sticky'];
        $thread['with_attachments'] = $row['thread_with_attachments'];
        $thread['board']['id'] = $row['board_id'];
        $thread['board']['name'] = $row['board_name'];
        $thread['board']['title'] = $row['board_title'];
        $thread['board']['annotation'] = $row['board_annotation'];
        $thread['board']['bump_limit'] = $row['board_bump_limit'];
        $thread['board']['force_anonymous'] = $row['board_force_anonymous'];
        $thread['board']['default_name'] = $row['board_default_name'];
        $thread['board']['with_attachments'] = $row['board_with_attachments'];
        $thread['board']['enable_macro'] = $row['board_enable_macro'];
        $thread['board']['enable_youtube'] = $row['board_enable_youtube'];
        $thread['board']['enable_captcha'] = $row['board_enable_captcha'];
        $thread['board']['same_upload'] = $row['board_same_upload'];
        $thread['board']['popdown_handler'] = $row['board_popdown_handler'];
        $thread['board']['category'] = $row['board_category'];
    }

    mysqli_free_result($result);
    db_cleanup_link($link);

    return $thread;
}
/**
 * Get thread.
 * @param MySQLi $link Link to database.
 * @param int $board Board id.
 * @param int $original_post Thread number.
 * @return array
 * thread.
 */
function db_threads_get_by_original_post($link, $board, $original_post) {
    $result = mysqli_query($link, "call sp_threads_get_by_original_post($board, $original_post)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $thread = null;
    if(mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        $thread = array('id' => $row['id'],
                        'board' => $board,
                        'original_post' => $row['original_post'],
                        'bump_limit' => $row['bump_limit'],
                        'sage' => $row['sage'],
                        'sticky' => $row['sticky'],
                        'with_attachments' => $row['with_attachments'],
                        'archived' => $row['archived']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $thread;
}
/**
 * Get changeable thread.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 * @return array
 * thread.
 */
function db_threads_get_changeable_by_id($link, $thread_id, $user_id) {
	$result = mysqli_query($link,
            "call sp_threads_get_changeable_by_id($thread_id, $user_id)");
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

	$thread = NULL;
	if(mysqli_affected_rows($link) > 0) {
		if( ($row = mysqli_fetch_assoc($result)) != NULL) {
            $board_data = array('id' => $row['board_id'],
                                'name' => $row['board_name'],
                                'title' => $row['board_title'],
                                'annotation' => $row['board_annotation'],
                                'bump_limit' => $row['board_bump_limit'],
                                'force_anonymous' => $row['board_force_anonymous'],
                                'default_name' => $row['board_default_name'],
                                'with_attachments' => $row['board_with_attachments'],
                                'enable_macro' => $row['board_enable_macro'],
                                'enable_youtube' => $row['board_enable_youtube'],
                                'enable_captcha' => $row['board_enable_captcha'],
                                'enable_translation' => $row['board_enable_translation'],
                                'enable_geoip' => $row['board_enable_geoip'],
                                'enable_shi' => $row['board_enable_shi'],
                                'enable_postid' => $row['board_enable_postid'],
                                'same_upload' => $row['board_same_upload'],
                                'popdown_handler' => $row['board_popdown_handler'],
                                'category' => $row['board_category']);

			$thread['id'] = $row['thread_id'];
			$thread['board'] = &$board_data;
			$thread['original_post'] = $row['thread_original_post'];
			$thread['bump_limit'] = $row['thread_bump_limit'];
			$thread['archived'] = $row['thread_archived'];
			$thread['sage'] = $row['thread_sage'];
			$thread['with_attachments'] = $row['thread_with_attachments'];
			$thread['closed'] = $row['thread_closed'];
		}
	} else {
        // TODO It happens if thread not exist also.
		throw new PermissionException(PermissionException::$messages['THREAD_NOT_ALLOWED']);
	}

	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
}
/**
 * Get moderatable threads.
 * @param MySQLi $link Link to database.
 * @param int $user_id User id.
 * @return array
 * threads.
 */
function db_threads_get_moderatable($link, $user_id) {
	$result = mysqli_query($link, "call sp_threads_get_moderatable($user_id)");
	if (!$result) {
		throw new CommonException(mysqli_error($link));
    }

	$threads = array();
	if (mysqli_affected_rows($link) > 0) {
		while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            if (!isset($board_data[$row['board_id']])) {
                $board_data[$row['board_id']] = array(  'id' => $row['board_id'],
                                                        'name' => $row['board_name'],
                                                        'title' => $row['board_title'],
                                                        'annotation' => $row['board_annotation'],
                                                        'bump_limit' => $row['board_bump_limit'],
                                                        'force_anonymous' => $row['board_force_anonymous'],
                                                        'default_name' => $row['board_default_name'],
                                                        'with_attachments' => $row['board_with_attachments'],
                                                        'enable_macro' => $row['board_enable_macro'],
                                                        'enable_youtube' => $row['board_enable_youtube'],
                                                        'enable_captcha' => $row['board_enable_captcha'],
                                                        'enable_translation' => $row['board_enable_translation'],
                                                        'enable_geoip' => $row['board_enable_geoip'],
                                                        'enable_shi' => $row['board_enable_shi'],
                                                        'enable_postid' => $row['board_enable_postid'],
                                                        'same_upload' => $row['board_same_upload'],
                                                        'popdown_handler' => $row['board_popdown_handler'],
                                                        'category' => $row['board_category']);
            }
			array_push($threads,
                       array('id' => $row['thread_id'],
                             'board' => &$board_data[$row['board_id']],
                             'original_post' => $row['thread_original_post'],
                             'bump_limit' => $row['thread_bump_limit'],
                             'sage' => $row['thread_sage'],
                             'sticky' => $row['thread_sticky'],
                             'with_attachments' => $row['thread_with_attachments'],
                             'closed' => $row['thread_closed']));
        }
    }

	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Get moderatable thread.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @param int $user_id User id.
 * @return mixed
 * thread or NULL if this thread is not moderatable for this user.
 */
function db_threads_get_moderatable_by_id($link, $thread_id, $user_id) {
    $result = mysqli_query($link,
        "call sp_threads_get_moderatable_by_id($thread_id, $user_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $thread = null;
    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            $thread['id'] = $row['id'];
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $thread;
}
/**
 * Get threads visible to user on specified board.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @param int $user_id User id.
 * @return array
 * threads.
 */
function db_threads_get_visible_by_board($link, $board_id, $user_id) {
    $threads = array();

    $result = mysqli_query($link, "call sp_threads_get_visible_by_board($board_id, $user_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    if (mysqli_affected_rows($link) > 0) {
        while (($row = mysqli_fetch_assoc($result)) != NULL) {
            if (!isset ($tmp_boards["{$row['board_id']}"])) {
                $tmp_boards["{$row['board_id']}"] =
                    array('id' => $row['board_id'],
                          'name' => $row['board_name'],
                          'title' => $row['board_title'],
                          'annotation' => $row['board_annotation'],
                          'bump_limit' => $row['board_bump_limit'],
                          'force_anonymous' => $row['board_force_anonymous'],
                          'default_name' => $row['board_default_name'],
                          'with_attachments' => $row['board_with_attachments'],
                          'enable_macro' => $row['board_enable_macro'],
                          'enable_youtube' => $row['board_enable_youtube'],
                          'enable_captcha' => $row['board_enable_captcha'],
                          'enable_translation' => $row['board_enable_translation'],
                          'enable_geoip' => $row['board_enable_geoip'],
                          'enable_shi' => $row['board_enable_shi'],
                          'enable_postid' => $row['board_enable_postid'],
                          'same_upload' => $row['board_same_upload'],
                          'popdown_handler' => $row['board_popdown_handler'],
                          'category' => $row['board_category']);
            }
            array_push($threads,
                       array('id' => $row['thread_id'],
                             'board' => &$tmp_boards["{$row['board_id']}"],
                             'original_post' => $row['thread_original_post'],
                             'bump_limit' => $row['thread_bump_limit'],
                             'sticky' => $row['thread_sticky'],
                             'sage' => $row['thread_sage'],
                             'with_attachments' => $row['thread_with_attachments'],
                             'closed' => $row['thread_closed'],
                             'posts_count' => $row['thread_posts_count']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $threads;
}
/**
 * Ищет с заданной страницы доски доступные для просмотра пользователю нити
 * и количество сообщений в них.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_search_visible_by_board($link, $board_id, $page, $user_id,
	$threads_per_page, $string)
{
	$threads = array();
	$threads_ids = array(); //Для проверки уже добавленых нитей
	$sticky_threads = array();
	$sticky_threads_ids = array(); //Для проверки уже добавленых нитей
	/*
	 * Количество нитей, которое нужно пропустить, чтобы выбирать нити только
	 * для нужной страницы.
	 */
	$skip = $threads_per_page * ($page - 1);
	$number = 0;	// Номер записи с не закреплённой нитью. Начинается с 1.
	$received = 0;	// Число выбранных не закреплённых нитей.
	$words = preg_split("/\s+/", $string);
	reset($words);
	foreach($words as $word)
	{
		if(strlen($word) > 60)
			throw new SearchException(SearchException::$messages['LONG_WORD']);
		$result = mysqli_query($link, 'call sp_threads_search_visible_by_board('
			. $board_id . ', ' . $user_id . ', "' . $word . '")');
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
			while(($row = mysqli_fetch_assoc($result)) != null)
			{
				if($row['sticky'] && !in_array($row['id'], $sticky_threads_ids))
				{
					if($page == 1)
					{
						// Закреплённые нити будут показаны только на 1 странице.
						array_push($sticky_threads,
							array('id' => $row['id'],
									'original_post' => $row['original_post'],
									'bump_limit' => $row['bump_limit'],
									'sticky' => $row['sticky'],
									'sage' => $row['sage'],
									'with_attachments' => $row['with_attachments'],
									'posts_count' => $row['posts_count']));
						array_push($sticky_threads_ids, $row['id']);
					}
					continue;
				}
				$number++;
				if($number > $skip && $received < $threads_per_page && !in_array($row['id'], $threads_ids))
				{
					array_push($threads,
						array('id' => $row['id'],
								'original_post' => $row['original_post'],
								'bump_limit' => $row['bump_limit'],
								'sticky' => $row['sticky'],
								'sage' => $row['sage'],
								'with_attachments' => $row['with_attachments'],
								'posts_count' => $row['posts_count']));
					array_push($threads_ids, $row['id']);
					$received++;
				}
			}
		mysqli_free_result($result);
        db_cleanup_link($link);
	}
	return $threads;
}
/**
 * Get visible threads.
 * @param MySQLi $link Link to database.
 * @param int $board Board id.
 * @param int $original_post Original post number.
 * @param int $user_id User id.
 * @return array
 * threads.
 */
function db_threads_get_visible_by_original_post($link, $board, $original_post, $user_id) {
    $result = mysqli_query($link,
            "call sp_threads_get_visible_by_original_post($board, $original_post, $user_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    if (mysqli_affected_rows($link) <= 0) {
        mysqli_free_result($result);
        db_cleanup_link($link);
        throw new PermissionException(PermissionException::$messages['THREAD_NOT_ALLOWED']);
    }

    $row = mysqli_fetch_assoc($result);
    if (isset($row['error']) && $row['error'] == 'NOT_FOUND') {
        mysqli_free_result($result);
        db_cleanup_link($link);
        throw new NodataException(NodataException::$messages['THREAD_NOT_FOUND']);
    }

    $board_data = array('id' => $row['board_id'],
                        'name' => $row['board_name'],
                        'title' => $row['board_title'],
                        'annotation' => $row['board_annotation'],
                        'bump_limit' => $row['board_bump_limit'],
                        'force_anonymous' => $row['board_force_anonymous'],
                        'default_name' => $row['board_default_name'],
                        'with_attachments' => $row['board_with_attachments'],
                        'enable_macro' => $row['board_enable_macro'],
                        'enable_youtube' => $row['board_enable_youtube'],
                        'enable_captcha' => $row['board_enable_captcha'],
                        'enable_translation' => $row['board_enable_translation'],
                        'enable_geoip' => $row['board_enable_geoip'],
                        'enable_shi' => $row['board_enable_shi'],
                        'enable_postid' => $row['board_enable_postid'],
                        'same_upload' => $row['board_same_upload'],
                        'popdown_handler' => $row['board_popdown_handler'],
                        'category' => $row['board_category']);

    $thread = array('id' => $row['thread_id'],
                    'board' => &$board_data,
                    'original_post' => $row['thread_original_post'],
                    'bump_limit' => $row['thread_bump_limit'],
                    'sage' => $row['thread_sage'],
                    'sticky' => $row['thread_sticky'],
                    'with_attachments' => $row['thread_with_attachments'],
                    'closed' => $row['thread_closed'],
                    'archived' => $row['thread_archived'],
                    'posts_count' => $row['visible_posts_count']);

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $thread;
}
/**
 * Calculate count of visible threads.
 * @param MySQLi $link Link to database.
 * @param int $user_id User id.
 * @param int $board_id Board id.
 * @return string
 * count of visible threads.
 */
function db_threads_get_visible_count($link, $user_id, $board_id) {
    $result = mysqli_query($link, "call sp_threads_get_visible_count($user_id, $board_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    if (mysqli_affected_rows($link) > 0 && ($row = mysqli_fetch_assoc($result)) != NULL) {
        mysqli_free_result($result);
        db_cleanup_link($link);
        return $row['threads_count'];
    } else {
        mysqli_free_result($result);
        db_cleanup_link($link);
        return 0;
    }
}
/**
 * Move thread.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @param int $board_id Board id.
 */
function db_threads_move_thread($link, $thread_id, $board_id) {
    if (!mysqli_query($link, "call sp_threads_move_thread($thread_id, $board_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}

/* ******************
 * Upload handlers. *
 ********************/

/**
 * Add upload handler.
 * @param MySQLi $link Link to database.
 * @param string $name Function name.
 */
function db_upload_handlers_add($link, $name) {
    if (!mysqli_query($link, "call sp_upload_handlers_add('$name')")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete upload handlers.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_upload_handlers_delete($link, $id) {
    if (!mysqli_query($link, "call sp_upload_handlers_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get upload handlers.
 * @param MySQLi $link Link to database.
 * @return array
 * upload handlers.
 */
function db_upload_handlers_get_all($link) {
    $result = mysqli_query($link, 'call sp_upload_handlers_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $upload_handlers = array();
    if (mysqli_affected_rows($link) > 0) {
        while( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($upload_handlers, array('id' => $row['id'],
                                               'name' => $row['name']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $upload_handlers;
}

/* *************************************
 * Работа с типами загружаемых файлов. *
 ***************************************/

/**
 * Add upload type.
 * @param MySQLi $link Link to database.
 * @param etring $extension Extension.
 * @param string $store_extension Stored extension.
 * @param boolean $is_image Image flag.
 * @param int $upload_handler_id Upload handler id.
 * @param string $thumbnail_image Thumbnail.
 */
function db_upload_types_add($link, $extension, $store_extension, $is_image, $upload_handler_id, $thumbnail_image) {
	$thumbnail_image = $thumbnail_image == null ? 'null' : "'$thumbnail_image'";
    $is_image = $is_image ? '1' : '0';
    if(!mysqli_query($link, "call sp_upload_types_add('$extension', '$store_extension', $is_image, $upload_handler_id, $thumbnail_image)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Delete upload type.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 */
function db_upload_types_delete($link, $id) {
    if (!mysqli_query($link, "call sp_upload_types_delete($id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Edit upload type.
 * @param MySQLi $link Link to database.
 * @param int $id Id.
 * @param string $store_extension Stored extension.
 * @param boolean $is_image Image flag.
 * @param int $upload_handler_id Upload handler id.
 * @param string $thumbnail_image Thumbnail.
 */
function db_upload_types_edit($link, $id, $store_extension, $is_image, $upload_handler_id, $thumbnail_image) {
    $thumbnail_image = $thumbnail_image == null ? 'null' : "'$thumbnail_image'";
    $is_image = $is_image ? '1' : '0';
    if(!mysqli_query($link, "call sp_upload_types_edit($id, '$store_extension', $is_image, $upload_handler_id, $thumbnail_image)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get upload types.
 * @param MySQLi $link Link to database.
 * @return array
 * upload types.
 */
function db_upload_types_get_all($link) {
    $result = mysqli_query($link, 'call sp_upload_types_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $upload_types = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($upload_types,
                       array('id' => $row['id'],
                             'extension' => $row['extension'],
                             'store_extension' => $row['store_extension'],
                             'is_image' => $row['is_image'],
                             'upload_handler' => $row['upload_handler'],
                             'thumbnail_image' => $row['thumbnail_image']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $upload_types;
}
/**
 * Get upload types on board.
 * @param MySQLi $link Link to database.
 * @param int $board_id Board id.
 * @return array
 * upload types.
 */
function db_upload_types_get_by_board($link, $board_id) {
    $result = mysqli_query($link, "call sp_upload_types_get_by_board($board_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $upload_types = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != null) {
            array_push($upload_types,
                       array('id' => $row['id'],
                             'extension' => $row['extension'],
                             'store_extension' => $row['store_extension'],
                             'is_image' => $row['is_image'],
                             'upload_handler' => $row['upload_handler'],
                             'upload_handler_name' => $row['upload_handler_name'],
                             'thumbnail_image' => $row['thumbnail_image']));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $upload_types;
}

/* ************************
 * User groups relations. *
 **************************/

/**
 * Add user to group.
 * @param MySQLi $link Link to database.
 * @param int $user_id User id.
 * @param int $group_id Group id.
 */
function db_user_groups_add($link, $user_id, $group_id) {
    if (!mysqli_query($link, "call sp_user_groups_add($user_id, $group_id)")) {
        throw new CommonException(mysqli_error($link));
    }
    
    db_cleanup_link($link);
}
/**
 * Delete user from group.
 * @param MySQLi $link Link to database.
 * @param int $user_id User id.
 * @param int $group_id Group id.
 */
function db_user_groups_delete($link, $user_id, $group_id) {
    if (!mysqli_query($link, "call sp_user_groups_delete($user_id, $group_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Move user to new group.
 * @param MySQLi $link Link to database.
 * @param int $user_id User id.
 * @param int $old_group_id Id of old group.
 * @param int $new_group_id Id of new group.
 */
function db_user_groups_edit($link, $user_id, $old_group_id, $new_group_id) {
    if (!mysqli_query($link, "call sp_user_groups_edit($user_id, $old_group_id, $new_group_id)")) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get user groups relations.
 * @param MySQLi $link Link to database.
 * @return array
 * user groups relations.
 */
function db_user_groups_get_all($link) {
    $result = mysqli_query($link, 'call sp_user_groups_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $user_groups = array();
    if (mysqli_affected_rows($link) > 0) {
        while( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($user_groups, array('user' => $row['user'],
                                           'group' => $row['group']));
        }
    } else {
        throw new NodataException(NodataException::$messages['USER_GROUPS_NOT_EXIST']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $user_groups;
}

/* ********
 * Users. *
 **********/

/**
 * Edit user settings by keyword or create new user if it not exist.
 * @param MySQLi $link Link to database.
 * @param string $keyword Keyword hash.
 * @param int|null $posts_per_thread Count of posts per thread or NULL.
 * @param int|null $threads_per_page Count of threads per page or NULL.
 * @param int|null $lines_per_post Count of lines per post or NULL.
 * @param int $language Language id.
 * @param int $stylesheet Stylesheet id.
 * @param string|null $password Password or NULL.
 * @param string|null $goto Redirection or NULL.
 */
function db_users_edit_by_keyword($link,
                                  $keyword,
                                  $posts_per_thread,
                                  $threads_per_page,
                                  $lines_per_post,
                                  $language,
                                  $stylesheet,
                                  $password,
                                  $goto) {

    if ($posts_per_thread === NULL) {
        $posts_per_thread = 'null';
    }
    if ($threads_per_page === NULL) {
        $threads_per_page = 'null';
    }
    if ($lines_per_post === NULL) {
        $lines_per_post = 'null';
    }
    $password = ($password === NULL? 'null' : "'$password'");
    $goto = ($goto === NULL? 'null' : "'$goto'");
    $query = "call sp_users_edit_by_keyword('$keyword', $posts_per_thread,
              $threads_per_page, $lines_per_post, $language, $stylesheet,
              $password, $goto)";
    if (!mysqli_query($link, $query)) {
        throw new CommonException(mysqli_error($link));
    }

    db_cleanup_link($link);
}
/**
 * Get users.
 * @param MySQLi $link Link to database.
 * @return array
 * users.
 */
function db_users_get_all($link) {
    $result = mysqli_query($link, 'call sp_users_get_all()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $users = array();
    if (mysqli_affected_rows($link) > 0) {
        while( ($row = mysqli_fetch_assoc($result)) != NULL)
            array_push($users, array('id' => $row['id']));
    } else {
        throw new NodataException(NodataException::$messages['USERS_NOT_EXIST']);
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $users;
}
/**
 * Get admins.
 * @param MySQLi $link Link to database.
 * @return array
 * admin users.
 */
function db_users_get_admins($link) {
    // Query.
    $result = mysqli_query($link, 'call sp_users_get_admins()');
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    // Collect data from query result.
    $admins = array();
    if (mysqli_affected_rows($link) > 0) {
        while(($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($admins, array('id' => $row['id']));
        }
    }

    // Cleanup.
    mysqli_free_result($result);
    db_cleanup_link($link);
    return $admins;
}
/**
 * Load user settings.
 * @param MySQLi $link Link to database.
 * @param string $keyword Keyword hash.
 */
function db_users_get_by_keyword($link, $keyword) {
    if (mysqli_multi_query($link, "call sp_users_get_by_keyword('$keyword')") == false) {
        throw new CommonException(mysqli_error($link));
    }

    // User settings.
    if ( ($result = mysqli_store_result($link)) == false) {
        throw new CommonException(mysqli_error($link));
    }
    if ( ($row = mysqli_fetch_assoc($result)) !== null) {
        $user_settings['id'] = $row['id'];
        $user_settings['posts_per_thread'] = $row['posts_per_thread'];
        $user_settings['threads_per_page'] = $row['threads_per_page'];
        $user_settings['lines_per_post'] = $row['lines_per_post'];
        $user_settings['language'] = $row['language'];
        $user_settings['stylesheet'] = $row['stylesheet'];
        $user_settings['password'] = $row['password'];
        $user_settings['goto'] = $row['goto'];
    } else {
        throw new PermissionException(PermissionException::$messages['USER_NOT_EXIST']);
    }
    mysqli_free_result($result);
    if (!mysqli_next_result($link)) {
        throw new CommonException(mysqli_error($link));
    }

    // Groups.
    if ( ($result = mysqli_store_result($link)) == false) {
        throw new CommonException(mysqli_error($link));
    }
    $user_settings['groups'] = array();
    while ( ($row = mysqli_fetch_assoc($result)) !== null) {
        array_push($user_settings['groups'], $row['name']);
    }
    if (count($user_settings['groups']) <= 0) {
        throw new NodataException(NodataException::$messages['USER_WITHOUT_GROUP']);
    }

    // Cleanup.
    mysqli_free_result($result);
    db_cleanup_link($link);
    return $user_settings;
}
/**
 * Set redirection.
 * @param MySQLi $link Link to database.
 * @param int $id User id.
 * @param string $goto Redirection.
 */
function db_users_set_goto($link, $id, $goto) {
    if(!mysqli_query($link, "call sp_users_set_goto($id, '$goto')")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Set password.
 * @param MySQLi $link Link to database.
 * @param int $id User id.
 * @param string $password New password.
 */
function db_users_set_password($link, $id, $password) {
    if (!mysqli_query($link, "call sp_users_set_password($id, '$password')")) {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}

/* *********
 * Videos. *
 ***********/

/**
 * Add video.
 * @param MySQLi $link Link to database.
 * @param string $code Code.
 * @param int $widht Width.
 * @param int $height Height.
 * @return int
 * added video id.
 */
function db_videos_add($link, $code, $widht, $height) {
    $result = mysqli_query($link, "call sp_videos_add('$code', $widht, $height)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return is_int($row['id']) ? $row['id'] : kotoba_intval($row['id']);
}
/**
 * Get videos.
 * @param MySQLi $link Link to database.
 * @param int $post_id Post id.
 * @return array
 * videos.
 */
function db_videos_get_by_post($link, $post_id) {
    $result = mysqli_query($link, "call sp_videos_get_by_post($post_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $videos = array();
    if (mysqli_affected_rows($link) > 0) {
        while ( ($row = mysqli_fetch_assoc($result)) != NULL) {
            array_push($videos,
                       array('id' => $row['id'],
                             'code' => $row['code'],
                             'widht' => $row['widht'],
                             'height' => $row['height'],
                             'attachment_type' => Config::ATTACHMENT_TYPE_VIDEO));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $videos;
}
/**
 * Get thread vodeos.
 * @param MySQLi $link Link to database.
 * @param int $thread_id Thread id.
 * @return array
 * vodeos.
 */
function db_videos_get_by_thread($link, $thread_id) {
    $result = mysqli_query($link, "call sp_videos_get_by_thread($thread_id)");
    if (!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $videos = array();
    if(mysqli_affected_rows($link) > 0) {
        while(($row = mysqli_fetch_assoc($result)) != null) {
            array_push($videos,
                array('id' => $row['id'],
                      'code' => $row['code'],
                      'widht' => $row['widht'],
                      'height' => $row['height'],
                      'attachment_type' => Config::ATTACHMENT_TYPE_VIDEO));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $videos;
}
/**
 * Get dangling videos.
 * @param MySQLi $link Link to database.
 * @return array
 * videos.
 */
function db_videos_get_dangling($link) {
    $result = mysqli_query($link, 'call sp_videos_get_dangling()');
    if(!$result) {
        throw new CommonException(mysqli_error($link));
    }

    $videos = array();
    if(mysqli_affected_rows($link) > 0) {
        while(($row = mysqli_fetch_assoc($result)) != null) {
            array_push( $videos,
                        array('id' => $row['id'],
                              'code' => $row['code'],
                              'widht' => $row['widht'],
                              'height' => $row['height'],
                              'attachment_type' => Config::ATTACHMENT_TYPE_VIDEO));
        }
    }

    mysqli_free_result($result);
    db_cleanup_link($link);
    return $videos;
}
?>