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
// Интерфейс работы с БД.

/***********
 * Разное. *
 ***********/

/**
 * Устанавливает соединение с сервером баз данных.
 * @return MySQLi
 * Возвращает соединение.
 */
function db_connect()
{
	$link = mysqli_connect(Config::DB_HOST, Config::DB_USER, Config::DB_PASS,
		Config::DB_BASENAME);
	if(!$link)
		throw new CommonException(mysqli_connect_error());
	if(!mysqli_set_charset($link, Config::SQL_ENCODING))
		throw new CommonException(mysqli_error($link));
	return $link;
}
/**
 * Очищяет связь с базой данных от всех полученных результатов. Обязательна к
 * вызову после вызова хранимой процедуры.
 *
 * Аргументы:
 * $link - связь с базой данных.
 */
function db_cleanup_link($link)
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
		throw new CommonException(mysqli_error($link));
}

/******************************
 * Блокировки адресов (баны). *
 ******************************/

/**
 * Проверяет, заблокирован ли адрес $ip.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $ip - адрес.
 *
 * Возвращает false, если адрес не заблокирован и массив, если заблокирован:
 * 'range_beg' - начало диапазона заблокированных адресов.
 * 'range_end' - конец диапазона заблокированных адресов.
 * 'untill' - время истечения бана.
 * 'reason' - причина бана.
 */
function db_bans_check($link, $ip)
{
	$result = mysqli_query($link, "call sp_bans_check($ip)");
	if(!$result)
	{
		throw new CommonException(mysqli_error($link));
	}
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
	db_cleanup_link($link);
	return $row;
}
/**
 * Блокирует диапазон адресов.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $range_beg - начало диапазона адресов.
 * $range_end - конец диапазона адресов.
 * $reason - причина.
 * $untill - время истечения бана.
 */
function db_bans_add($link, $range_beg, $range_end, $reason, $untill)
{
	$reason = ($reason === null ? 'null' : "'$reason'");
	if(!mysqli_query($link, "call sp_bans_add($range_beg, $range_end, $reason,
			'$untill')"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет бан с заданным идентификатором.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $id - идентификатор бана.
 */
function db_bans_delete_byid($link, $id)
{
	if(!mysqli_query($link, "call sp_bans_delete_byid($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет баны с заданным IP адресом.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $ip - ip адрес.
 */
function db_bans_delete_byip($link, $ip)
{
	if(!mysqli_query($link, "call sp_bans_delete_byip($ip)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все баны.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает баны:
 * 'id' - идентификатор бана.
 * 'range_beg' - начало диапазона блокированных IP адресов.
 * 'range_end' - конец диапазона блокированных IP адресов.
 * 'reason' - причина бана.
 * 'untill' - время истечения бана.
 */
function db_bans_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_bans_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$bans = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($bans, array('id' => $row['id'],
									'range_beg' => $row['range_beg'],
									'range_end' => $row['range_end'],
									'reason' => $row['reason'],
									'untill' => $row['untill']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $bans;
}

/*********************
 * Работа с досками. *
 *********************/

/**
 * Получает доски, доступные для чтения пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_all_view($link, $user_id)
{
	$result = mysqli_query($link, "call sp_boards_get_all_view($user_id)");
	if($result == false)
		throw new CommonException(mysqli_error($link));
	$boards = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($boards, array('id' => $row['id'],
					'name' => $row['name'],
					'title' => $row['title'],
					'annotation' => $row['annotation'],
					'bump_limit' => $row['bump_limit'],
					'force_anonymous' => $row['force_anonymous'],
					'default_name' => $row['default_name'],
					'with_files' => $row['with_files'],
					'same_upload' => $row['same_upload'],
					'popdown_handler' => $row['popdown_handler'],
					'category' => $row['category']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $boards;
}
/**
 * Получает доски, доступные для редактирования пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
 * 'same_upload' - политика загрузки одинаковых изображений.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория.</p>
 */
function db_boards_get_all_change($link, $user_id)
{
	$result = mysqli_query($link, "call sp_boards_get_all_change($user_id)");
	if($result == false)
		throw new CommonException(mysqli_error($link));
	$boards = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($boards, array('id' => $row['id'],
					'name' => $row['name'],
					'title' => $row['title'],
					'bump_limit' => $row['bump_limit'],
					'force_anonymous' => $row['force_anonymous'],
					'default_name' => $row['default_name'],
					'with_files' => $row['with_files'],
					'same_upload' => $row['same_upload'],
					'popdown_handler' => $row['popdown_handler'],
					'category' => $row['category']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $boards;
}
/**
 * Получает доски, доступные для модерирования заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
					'bump_limit' => $row['bump_limit'],
					'force_anonymous' => $row['force_anonymous'],
					'default_name' => $row['default_name'],
					'with_files' => $row['with_files'],
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
 * Получает доску по заданному имени, доступную для редактирования пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_specifed_change_byname($link, $board_name, $user_id)
{
	$result = mysqli_query($link,
		"call sp_boards_get_specifed_change_byname('$board_name', $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) <= 0)
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new PremissionException(PremissionException::$messages['BOARD_NOT_ALLOWED']);
	}
	$row = mysqli_fetch_assoc($result);
	if(isset($row['error']) && $row['error'] == 'NOT_FOUND')
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new NodataException(sprintf(NodataException::$messages['BOARD_NOT_FOUND'],
				$board_name));
	}
	$board['id'] = $row['id'];
	$board['name'] = $row['name'];
	$board['title'] = $row['title'];
	$board['bump_limit'] = $row['bump_limit'];
	$board['force_anonymous'] = $row['force_anonymous'];
	$board['default_name'] = $row['default_name'];
	$board['with_files'] = $row['with_files'];
	$board['same_upload'] = $row['same_upload'];
	$board['popdown_handler'] = $row['popdown_handler'];
	$board['category'] = $row['category'];
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board;
}
/**
 * Получает доску, доступную для редактирования пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_specifed_change($link, $board_id, $user_id)
{
	$result = mysqli_query($link,
		"call sp_boards_get_specifed_change($board_id, $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) <= 0)
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new PremissionException(PremissionException::$messages['BOARD_NOT_ALLOWED']);
	}
	$row = mysqli_fetch_assoc($result);
	if(isset($row['error']) && $row['error'] == 'NOT_FOUND')
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new NodataException(sprintf(NodataException::$messages['BOARD_NOT_FOUND'],
				$board_name));
	}
	$board['id'] = $row['id'];
	$board['name'] = $row['name'];
	$board['title'] = $row['title'];
	$board['bump_limit'] = $row['bump_limit'];
	$board['force_anonymous'] = $row['force_anonymous'];
	$board['default_name'] = $row['default_name'];
	$board['with_files'] = $row['with_files'];
	$board['same_upload'] = $row['same_upload'];
	$board['popdown_handler'] = $row['popdown_handler'];
	$board['category'] = $row['category'];
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board;
}
/**
 * Получает все доски.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
 * 'same_upload' - политика загрузки одинаковых изображений.<br>
 * 'popdown_handler' - обработчик автоматического удаления нитей.<br>
 * 'category' - категория доски.</p>
 */
function db_boards_get_all($link)
{
	if(($result = mysqli_query($link, "call sp_boards_get_all()")) == false)
		throw new CommonException(mysqli_error($link));
	$boards = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($boards,
				array('id' => $row['id'], 'name' => $row['name'],
						'title' => $row['title'],
						'annotation' => $row['annotation'],
						'bump_limit' => $row['bump_limit'],
						'force_anonymous' => $row['force_anonymous'],
						'default_name' => $row['default_name'],
						'with_files' => $row['with_files'],
						'same_upload' => $row['same_upload'],
						'popdown_handler' => $row['popdown_handler'],
						'category' => $row['category']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $boards;
}
/**
 * Получает доску по заданному идентификатору.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_specifed($link, $board_id)
{
	$result = mysqli_query($link, "call sp_boards_get_specifed($board_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$board = array();
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) !== null)
	{
		$board['id'] = $row['id'];
		$board['name'] = $row['name'];
		$board['title'] = $row['title'];
		$board['bump_limit'] = $row['bump_limit'];
		$board['force_anonymous'] = $row['force_anonymous'];
		$board['default_name'] = $row['default_name'];
		$board['with_files'] = $row['with_files'];
		$board['same_upload'] = $row['same_upload'];
		$board['popdown_handler'] = $row['popdown_handler'];
		$board['category'] = $row['category'];
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board;
}
/**
 * Получает доску по заданному имени.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_name string <p>Имя доски.</p>
 * @return array
 * Возвращает доску:<p>
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
function db_boards_get_specifed_byname($link, $board_name)
{
	$result = mysqli_query($link, "call sp_boards_get_specifed_byname('$board_name')");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$board = null;
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) !== null)
	{
		$board['id'] = $row['id'];
		$board['name'] = $row['name'];
		$board['title'] = $row['title'];
		$board['bump_limit'] = $row['bump_limit'];
		$board['force_anonymous'] = $row['force_anonymous'];
		$board['default_name'] = $row['default_name'];
		$board['with_files'] = $row['with_files'];
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
 * Получает доску по заданному идентификатору.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id string <p>Идентификатор доски.</p>
 * @return array
 * Возвращает доску:<p>
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
function db_boards_get_by_id($link, $id)
{
	$result = mysqli_query($link, "call sp_boards_get_by_id($id)");
	if(!$result)
	{
		throw new CommonException(mysqli_error($link));
	}
	$board = null;
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) !== null)
	{
		$board['id'] = $row['id'];
		$board['name'] = $row['name'];
		$board['title'] = $row['title'];
		$board['bump_limit'] = $row['bump_limit'];
		$board['force_anonymous'] = $row['force_anonymous'];
		$board['default_name'] = $row['default_name'];
		$board['with_files'] = $row['with_files'];
		$board['same_upload'] = $row['same_upload'];
		$board['popdown_handler'] = $row['popdown_handler'];
		$board['category'] = $row['category'];
	}
	if($board === null)
	{
		throw new NodataException(NodataException::$messages['BOARD_NOT_FOUND']);
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board;
}
/**
 * Редактирует параметры доски.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_edit($link, $id, $title, $bump_limit, $force_anonymous,
	$default_name, $with_files, $same_upload, $popdown_handler, $category)
{
	$title = ($title === null ? 'null' : "'$title'");
	$default_name = ($default_name === null ? 'null' : "'$default_name'");
	if(!mysqli_query($link, "call sp_boards_edit($id, $title, $bump_limit,
			$force_anonymous, $default_name, $with_files, '$same_upload',
			$popdown_handler, $category)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Редактирует аннотацию доски.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор.</p>
 * @param annotation string <p>Аннотация.</p>
 */
function db_boards_edit_annotation($link, $id, $annotation)
{
	$annotation = ($annotation === null ? 'null' : "'$annotation'");
	if(!mysqli_query($link, "call sp_boards_edit_annotation($id, $annotation)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Добавляет доску.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_add($link, $name, $title, $bump_limit, $force_anonymous,
	$default_name, $with_files, $same_upload, $popdown_handler, $category)
{
	$title = ($title === null ? 'null' : "'$title'");
	$default_name = ($default_name === null ? 'null' : "'$default_name'");
	if(!mysqli_query($link, "call sp_boards_add('$name', $title, $bump_limit,
			$force_anonymous, $default_name, $with_files, '$same_upload',
			$popdown_handler, $category)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет заданную доску.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор доски.</p>
 */
function db_boards_delete($link, $id)
{
	if(!mysqli_query($link, "call sp_boards_delete($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/*************************
 * Работа с категориями. *
 *************************/

/**
 * Получает все категории.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает пустой массив, если нет ни одной категории или массив категорий:
 * 'id' - идентификатор категории.
 * 'name' - имя категории.
 */
function db_categories_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_categories_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$categories = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($categories, array('id' => $row['id'],
					'name' => $row['name']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $categories;
}
/**
 * Добавляет новую категорию с именем $name.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $name - имя новой категории.
 */
function db_categories_add($link, $name)
{
	if(!mysqli_query($link, "call sp_categories_add('$name')"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет категорию с идентификатором $id.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $id - идентификатор категории для удаления.
 */
function db_categories_delete($link, $id)
{
	if(!mysqli_query($link, "call sp_categories_delete($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/****************************
 * Работа с пользователями. *
 ****************************/

/**
 * Получает настройки ползователя по заданному ключевому слову.
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
 * 'password' - пароль для удаления сообщений.<br>
 * 'goto' - перенаправление при постинге.<br>
 * 'groups' - группы, в которые входит пользователь.</p>
 */
function db_users_get_by_keyword($link, $keyword)
{
	if(mysqli_multi_query($link,
			"call sp_users_get_by_keyword('$keyword')") == false)
	{
		throw new CommonException(mysqli_error($link));
	}
// Настройки пользователя.
	if(($result = mysqli_store_result($link)) == false)
	{
		throw new CommonException(mysqli_error($link));
	}
	if(($row = mysqli_fetch_assoc($result)) !== null)
	{
		$user_settings['id'] = $row['id'];
		$user_settings['posts_per_thread'] = $row['posts_per_thread'];
		$user_settings['threads_per_page'] = $row['threads_per_page'];
		$user_settings['lines_per_post'] = $row['lines_per_post'];
		$user_settings['language'] = $row['language'];
		$user_settings['stylesheet'] = $row['stylesheet'];
		$user_settings['password'] = $row['password'];
		$user_settings['goto'] = $row['goto'];
	}
	else
	{
		throw new PremissionException(sprintf(PremissionException::$messages['USER_NOT_EXIST']),
			$keyword);
	}
	mysqli_free_result($result);
	if(!mysqli_next_result($link))
	{
		throw new CommonException(mysqli_error($link));
	}
// Группы пользователя.
	if(($result = mysqli_store_result($link)) == false)
	{
		throw new CommonException(mysqli_error($link));
	}
	$user_settings['groups'] = array();
	while(($row = mysqli_fetch_assoc($result)) !== null)
	{
		array_push($user_settings['groups'], $row['name']);
	}
	if(count($user_settings['groups']) <= 0)
	{
		throw new NodataException(sprintf(NodataException::$messages['USER_WITHOUT_GROUP']),
			$keyword);
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $user_settings;
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
 * @param password mixed <p>Пароль для удаления сообщений.</p>
 * @param goto string <p>Перенаправление при постинге.</p>
 */
function db_users_edit_bykeyword($link, $keyword, $threads_per_page,
	$posts_per_thread, $lines_per_post, $stylesheet, $language, $password, $goto)
{
	$password = $password === null? 'null' : "'$password'";
	$goto = $goto === null? 'null' : "'$goto'";
	if(!mysqli_query($link, "call sp_users_edit_bykeyword('$keyword',
			$threads_per_page, $posts_per_thread, $lines_per_post, $stylesheet,
			$language, $password, $goto)"))
	{
		throw new CommonException(mysqli_error($link));
	}
	if(mysqli_affected_rows($link) <= 0)
	{
		throw new DataExchangeException(DataExchangeException::$messages['SAVE_USER_SETTINGS']);
	}
	db_cleanup_link($link);
}
/**
 * Устанавливает пароль для удаления сообщений заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор пользователя.</p>
 * @param password mixed <p>Пароль для удаления сообщений.</p>
 */
function db_users_set_password($link, $id, $password)
{
	$password = ($password === null? 'null' : "'$password'");
	if(!mysqli_query($link, "call sp_users_set_password($id, $password)"))
	{
		throw new CommonException(mysqli_error($link));
	}
	db_cleanup_link($link);
}
/**
 * Получает всех пользователей.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает идентификаторы пользователей:<p>
 * 'id' - идентификатор пользователя.</p>
 */
function db_users_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_users_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$users = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($users, array('id' => $row['id']));
	else
		throw new NodataException(NodataException::$messages['USERS_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $users;
}

/*********************************
 * Работа со стилями оформления. *
 *********************************/

/**
 * Получает все стили оформления.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает стили оформления:
 * 'id' - идентификатор стиля оформления.
 * 'name' - имя стиля оформления.
 */
function db_stylesheets_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_stylesheets_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$stylesheets = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($stylesheets, array('id' => $row['id'],
					'name' => $row['name']));
	else
		throw new NodataException(NodataException::$messages['STYLESHEETS_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $stylesheets;
}
/**
 * Добавляет новый стиль оформления с именем $name.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $name - имя нового стиля оформления.
 */
function db_stylesheets_add($link, $name)
{
	if(!mysqli_query($link, "call sp_stylesheets_add('$name')"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет стиль оформления с идентификатором $id.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $id - идентификатор стиля для удаления.
 */
function db_stylesheets_delete($link, $id)
{
	if(!mysqli_query($link, "call sp_stylesheets_delete($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/*********************
 * Работа с языками. *
 *********************/

/**
 * Получает все языки.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает языки:
 * 'id' - идентификатор языка.
 * 'name' - имя языка.
 */
function db_languages_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_languages_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$languages = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($languages, array('id' => $row['id'],
					'name' => $row['name']));
	else
		throw new NodataException(NodataException::$messages['LANGUAGES_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $languages;
}
/**
 * Добавляет новый язык с именем $name.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $name - имя нового языка.
 */
function db_languages_add($link, $name)
{
	$result = mysqli_query($link, "call sp_languages_add('$name')");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет язык с идентификатором $id.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $id - идентификатор языка для удаления.
 */
function db_languages_delete($link, $id)
{
	if(($result = mysqli_query($link, "call sp_languages_delete($id)")) == false)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/**********************
 * Работа с группами. *
 **********************/

/**
 * Получает все группы.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает группы:
 * 'id' - идентификатор группы.
 * 'name' - имя группы.
 */
function db_groups_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_groups_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$groups = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($groups, array('id' => $row['id'], 'name' => $row['name']));
	else
		throw new NodataException(NodataException::$messages['GROUPS_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $groups;
}
/**
 * Добавляет группу с именем $group_name, а так же стандартные разрешения на
 * чтение.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $group_name - имя группы.
 */
function db_groups_add($link, $group_name)
{
	if(($result = mysqli_query($link, "call sp_groups_add('$group_name')")) == false)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет группы, идентификаторы которых перечислены в массиве $group_ids, а
 * так же всех пользователей, которые входят в эти группы и все права, которые
 * заданы для этих групп.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $group_ids - массив идентификаторов групп для удаления.
 */
function db_groups_delete($link, $group_ids)
{
	foreach($group_ids as $id)
	{
		$result = mysqli_query($link, "call sp_groups_delete($id)");
		if(!$result)
			throw new CommonException(mysqli_error($link));
		db_cleanup_link($link);
	}
}

/*****************************************************
 * Работа с закреплениями пользователей за группами. *
 *****************************************************/

/**
 * Получает закрепления пользователей за группами.
 *
 * Аргументы:
 * $link - связь с базой данных
 *
 * Возвращает массив закреплений:
 * 'user' - идентификатор пользователя.
 * 'group' - идентификатор группы.
 */
function db_user_groups_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_user_groups_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$user_groups = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($user_groups,
				array('user' => $row['user'],
						'group' => $row['group']));
	else
		throw new NodataException(NodataException::$messages['USER_GROUPS_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $user_groups;
}
/**
 * Добавляет пользователя с идентификатором $user в группу с идентификатором
 * $group.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $user_id - идентификатор пользователя.
 * $group_id - идентификатор группы.
 */
function db_user_groups_add($link, $user_id, $group_id)
{
	$result = mysqli_query($link, "call sp_user_groups_add($user_id, $group_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Переносит пользователя с идентификатором $user_id из группы с идентификатором
 * $old_group_id в группу с идентификатором $new_group_id.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $user_id - идентификатор пользователя.
 * $old_group_id - идентификатор старой группы.
 * $new_group_id - идентификатор новой группы.
 */
function db_user_groups_edit($link, $user_id, $old_group_id, $new_group_id)
{
	$result = mysqli_query($link, "call sp_user_groups_edit($user_id,
		$old_group_id, $new_group_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет пользователя с идентификатором $user_id из группы с идентификатором
 * $group_id.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $user_id - идентификатор пользователя.
 * $group_id - идентификатор группы.
 */
function db_user_groups_delete($link, $user_id, $group_id)
{
	$result = mysqli_query($link, "call sp_user_groups_delete($user_id, $group_id)");
	if(!$result )
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/***************************************
 * Работа со списком контроля доступа. *
 ***************************************/

/**
 * Получает список контроля доступа.
 *
 * Аргументы:
 * $link - связь с базой данных.
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
function db_acl_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_acl_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
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
	else
		throw new NodataException(NodataException::$messages['ACL_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $acl;
}
/**
 * Редактирует запись в списке контроля доступа.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $group_id - идентификатор группы или null для всех групп.
 * $board_id - идентификатор доски или null для всех досок.
 * $thread_id - идентификатор нити или null для всех нитей.
 * $post_id - идентификатор сообщения или null для всех сообщений.
 * $view - право на чтение.
 * $change - право на изменение.
 * $moderate - право на модерирование.
 */
function db_acl_edit($link, $group_id, $board_id, $thread_id, $post_id, $view,
	$change, $moderate)
{
	// Так как null при преобразовании к строке даёт пустую строку.
	$group_id = ($group_id === null ? 'null' : $group_id);
	$board_id = ($board_id === null ? 'null' : $board_id);
	$thread_id = ($thread_id === null ? 'null' : $thread_id);
	$post_id = ($post_id === null ? 'null' : $post_id);
	if(($result = mysqli_query($link, "call sp_acl_edit($group_id, $board_id,
				$thread_id, $post_id, $view, $change, $moderate)")) == false)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Добавляет новую запись в список контроля доступа.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $group_id - идентификатор группы или null для всех групп.
 * $board_id - идентификатор доски или null для всех досок.
 * $thread_id - идентификатор нити или null для всех нитей.
 * $post_id - идентификатор сообщения или null для всех сообщений.
 * $view - право на чтение. 0 или 1.
 * $change - право на изменение. 0 или 1.
 * $moderate - право на модерирование. 0 или 1.
 */
function db_acl_add($link, $group_id, $board_id, $thread_id, $post_id, $view,
	$change, $moderate)
{
	// Так как null при преобразовании к строке даёт пустую строку.
	$group_id = ($group_id === null ? 'null' : $group_id);
	$board_id = ($board_id === null ? 'null' : $board_id);
	$thread_id = ($thread_id === null ? 'null' : $thread_id);
	$post_id = ($post_id === null ? 'null' : $post_id);
	$result = mysqli_query($link, "call sp_acl_add($group_id, $board_id,
		$thread_id, $post_id, $view, $change, $moderate)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет запись из списка контроля доступа.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $group_id - идентификатор группы или null для всех групп.
 * $board_id - идентификатор доски или null для всех досок.
 * $thread_id - идентификатор нити или null для всех нитей.
 * $post_id - идентификатор сообщения или null для всех сообщений.
 */
function db_acl_delete($link, $group_id, $board_id, $thread_id, $post_id)
{
	// Так как null при преобразовании к строке даёт пустую строку.
	$group_id = ($group_id === null ? 'null' : $group_id);
	$board_id = ($board_id === null ? 'null' : $board_id);
	$thread_id = ($thread_id === null ? 'null' : $thread_id);
	$post_id = ($post_id === null ? 'null' : $post_id);
	$result = mysqli_query($link, "call sp_acl_delete($group_id, $board_id,
		$thread_id, $post_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/**********************************************
 * Работа с обработчиками загружаемых файлов. *
 **********************************************/

/**
 * Получает все обработчики загружаемых файлов.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает обработчики загружаемых файлов:
 * 'id' - идентификатор обработчика.
 * 'name' - имя обработчика.
 */
function db_upload_handlers_get_all($link)
{
	$result = mysqli_query($link, 'call sp_upload_handlers_get_all()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$upload_handlers = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($upload_handlers, array('id' => $row['id'],
					'name' => $row['name']));
	db_cleanup_link($link);
	return $upload_handlers;
}
/**
 * Добавляет новый обработчик загружаемых файлов.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $name - имя нового обработчика загружаемых файлов.
 */
function db_upload_handlers_add($link, $name)
{
	if(!mysqli_query($link, "call sp_upload_handlers_add('$name')"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет обработчик загружаемых файлов.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $id - идентификатор обработчика загружаемых файлов для удаления.
 */
function db_upload_handlers_delete($link, $id)
{
	if(!mysqli_query($link, "call sp_upload_handlers_delete($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/******************************************
 * Работа с обработчиками удаления нитей. *
 ******************************************/

/**
 * Получает все обработчики удаления нитей.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает обработчики удаления нитей:
 * 'id' - идентификатор обработчика.
 * 'name' - имя обработчика.
 */
function db_popdown_handlers_get_all($link)
{
	$result = mysqli_query($link, 'call sp_popdown_handlers_get_all()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$popdown_handlers = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($popdown_handlers, array('id' => $row['id'],
					'name' => $row['name']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $popdown_handlers;
}
/**
 * Добавляет новый обработчик удаления нитей.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $name - имя нового обработчика удаления нитей.
 */
function db_popdown_handlers_add($link, $name)
{
	if(!mysqli_query($link, "call sp_popdown_handlers_add('$name')"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет обработчик удаления нитей.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $id - идентификатор обработчика для удаления.
 */
function db_popdown_handlers_delete($link, $id)
{
	if(!mysqli_query($link, "call sp_popdown_handlers_delete($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/***************************************
 * Работа с типами загружаемых файлов. *
 ***************************************/

/**
 * Получает все типы загружаемых файлов.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает массив типов загружаемых файлов:
 * 'id' - идентификатор типа.
 * 'extension' - расширение файла.
 * 'store_extension' - сохраняемое расширение файла.
 * 'upload_handler' - обработчик загружаемых файлов, обслуживающий данный тип.
 * 'thumbnail_image' - имя картинки для файлов, не являющихся изображением.
 */
function db_upload_types_get_all($link)
{
	$result = mysqli_query($link, 'call sp_upload_types_get_all()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$upload_types = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($upload_types, array('id' => $row['id'],
					'extension' => $row['extension'],
					'store_extension' => $row['store_extension'],
					'is_image' => $row['is_image'],
					'upload_handler' => $row['upload_handler'],
					'thumbnail_image' => $row['thumbnail_image']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $upload_types;
}
/**
 * Получает типы файлов, доступных для загрузки на доске с идентификатором
 * $board_id.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $board_id - идентификатор доски.
 *
 * Возвращает типы загружаемых файлов:
 * 'id' - идентификатор.
 * 'extension' - расширение файла.
 * 'store_extension' - сохраняемое расширение файла.
 * 'is_image' - файлы этого типа являются изображениями.
 * 'upload_handler' - идентификатор обработчика загружаемых файлов.
 * 'upload_handler_name' - имя обработчика загружаемых файлов.
 * 'thumbnail_image' - имя картинки для файлов, не являющихся изображением.
 */
function db_upload_types_get_board($link, $board_id)
{
	$result = mysqli_query($link, "call sp_upload_types_get_board($board_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$upload_types = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($upload_types,
				array('id' => $row['id'],
						'extension' => $row['extension'],
						'store_extension' => $row['store_extension'],
						'is_image' => $row['is_image'],
						'upload_handler' => $row['upload_handler'],
						'upload_handler_name' => $row['upload_handler_name'],
						'thumbnail_image' => $row['thumbnail_image']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $upload_types;
}
/**
 * Редактирует тип загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор типа.</p>
 * @param store_extension string <p>Сохраняемое расширение файла.</p>
 * @param is_image mixed <p>Флаг типа файлов изображений.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемых
 * файлов.</p>
 * @param thumbnail_image string <p>Имя картинки для файлов, не являющихся
 * изображением.</p>
 */
function db_upload_types_edit($link, $id, $store_extension, $is_image,
	$upload_handler_id, $thumbnail_image)
{
	$thumbnail_image = ($thumbnail_image === null ? 'null' : "'$thumbnail_image'");
	if(!mysqli_query($link, "call sp_upload_types_edit($id, '$store_extension',
			$is_image, $upload_handler_id, $thumbnail_image)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Добавляет новый тип загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param extension string <p>Расширение файла.</p>
 * @param store_extension string <p>Сохраняемое расширение файла.</p>
 * @param is_image mixed <p>Флаг типа файлов изображений.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемых
 * файлов.</p>
 * @param thumbnail_image string <p>Имя картинки для файлов, не являющихся
 * изображением.</p>
 */
function db_upload_types_add($link, $extension, $store_extension, $is_image,
	$upload_handler_id, $thumbnail_image)
{
	$thumbnail_image = ($thumbnail_image === null ? 'null' : "'$thumbnail_image'");
	if(!mysqli_query($link, "call sp_upload_types_add('$extension',
			'$store_extension', $is_image, $upload_handler_id, $thumbnail_image)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет тип загружаемых файлов.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $id - идентифаикатор типа загружаемых файлов.
 */
function db_upload_types_delete($link, $id)
{
	if(!mysqli_query($link, "call sp_upload_types_delete($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/*********************************************
 * Работа со связями типов файлов с досками. *
 *********************************************/

/**
 * Получает все связи типов файлов с досками.
 *
 * Аргументы:
 * $link - связь с базой данных.
 *
 * Возвращает связи:
 * 'board' - идентификатор доски.
 * 'upload_type' - идентификатор типа файла.
 */
function db_board_upload_types_get_all($link)
{
	$result = mysqli_query($link, 'call sp_board_upload_types_get_all()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$board_upload_types = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($board_upload_types, array('board' => $row['board'],
					'upload_type' => $row['upload_type']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board_upload_types;
}
/**
 * Добавляет связь типа загружаемого файла с доской.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $board_id - идентификатор доски.
 * $upload_type_id - идтенификатор типа загружаемого файла.
 */
function db_board_upload_types_add($link, $board_id, $upload_type_id)
{
	if(!mysqli_query($link, "call sp_board_upload_types_add($board_id,
			$upload_type_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет связь типа загружаемого файла с доской.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $board_id - идентификатор доски.
 * $upload_type_id - идтенификатор типа загружаемого файла.
 */
function db_board_upload_types_delete($link, $board_id, $upload_type_id)
{
	if(!mysqli_query($link, "call sp_board_upload_types_delete($board_id,
			$upload_type_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/********************
 * Работа с нитями. *
 ********************/

/**
 * Получает все нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_all($link)
{
	$result = mysqli_query($link, "call sp_threads_get_all()");
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
						'sticky' => $row['sticky'],
						'sage' => $row['sage'],
						'with_files' => $row['with_files']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Получает все нити, помеченные для архивирования.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_all_archived($link)
{
	$result = mysqli_query($link, 'call sp_threads_get_all_archived()');
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
						'sticky' => $row['sticky'],
						'sage' => $row['sage'],
						'with_files' => $row['with_files']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Редактирует настройки нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sticky mixed <p>Флаг закрепления.</p>
 * @param sage mixed <p>Флаг поднятия нити при ответе.</p>
 * @param with_files mixed <p>Флаг загрузки файлов.</p>
 */
function db_threads_edit($link, $thread_id, $bump_limit, $sticky, $sage,
	$with_files)
{
	$bump_limit = ($bump_limit === null ? 'null' : $bump_limit);
	$with_files = ($with_files === null ? 'null' : $with_files);
	if(!mysqli_query($link, "call sp_threads_edit($thread_id, $bump_limit,
			$sticky, $sage, $with_files)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Редактирует оригинальное сообщение нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param original_post mixed <p>Номер оригинального сообщения нити.</p>
 */
function db_threads_edit_originalpost($link, $thread_id, $original_post)
{
	if(!mysqli_query($link, "call sp_threads_edit_originalpost($thread_id,
			$original_post)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Создаёт нить. Если номер оригинального сообщения null, то будет создана
 * пустая нить.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param original_post mixed <p>Номер оригинального сообщения.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sage mixed <p>Флаг поднятия нити.</p>
 * @param with_files mixed <p>Флаг загрузки файлов.</p>
 * @return array
 * Возвращает нить.
 */
function db_threads_add($link, $board_id, $original_post, $bump_limit, $sage,
	$with_files)
{
	$original_post = ($original_post === null ? 'null' : $original_post);
	$bump_limit = ($bump_limit === null ? 'null' : $bump_limit);
	$sage = $sage ? '1' : '0';
	$with_files = $with_files === null ? 'null' : $with_files;
	$result = mysqli_query($link, "call sp_threads_add($board_id,
		$original_post, $bump_limit, $sage, $with_files)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $row;
}
/**
 * Получает нити, доступные для модерирования заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_all_moderate($link, $user_id)
{
	$result = mysqli_query($link, "call sp_threads_get_all_moderate($user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$threads = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($threads,
				array('id' => $row['id'],
						'board' => $row['board'],
						'original_post' => $row['original_post'],
						'bump_limit' => $row['bump_limit'],
						'sticky' => $row['sticky'],
						'sage' => $row['sage'],
						'with_files' => $row['with_files']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Получает доступные для просмотра пользователю нити и количество сообщений в
 * них, с заданной страницы доски.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_board_view($link, $board_id, $page, $user_id,
	$threads_per_page)
{
	$threads = array();
	$sticky_threads = array();
	/*
	 * Количество нитей, которое нужно пропустить, чтобы выбирать нити только
	 * для нужной страницы.
	 */
	$skip = $threads_per_page * ($page - 1);
	$number = 0;	// Номер записи с не закреплённой нитью. Начинается с 1.
	$received = 0;	// Число выбранных не закреплённых нитей.
	$result = mysqli_query($link,
		"call sp_threads_get_board_view($board_id, $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
		{
			if($row['sticky'])
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
								'with_files' => $row['with_files'],
								'posts_count' => $row['posts_count']));
				}
				continue;
			}
			$number++;
			if($number > $skip && $received < $threads_per_page)
			{
				array_push($threads,
					array('id' => $row['id'],
							'original_post' => $row['original_post'],
							'bump_limit' => $row['bump_limit'],
							'sticky' => $row['sticky'],
							'sage' => $row['sage'],
							'with_files' => $row['with_files'],
							'posts_count' => $row['posts_count']));
				$received++;
			}
		}
	if($page == 1)
	{
		$threads = array_merge($sticky_threads, $threads);
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Вычисляет количество нитей, доступных для просмотра заданному пользователю
 * на заданной доске.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param board_id mixed <p>идентификатор доски.</p>
 * @return string
 * Возвращает число нитей.
 */
function db_threads_get_view_threadscount($link, $user_id, $board_id)
{
	$result = mysqli_query($link,
		"call sp_threads_get_view_threadscount($user_id, $board_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) != null)
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		return $row['threads_count'];
	}
	else
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		return '0';
	}
}
/**
 * Получает доступную для просмотра пользователю нить с заданной страницы доски,
 * и количество сообщений в ней.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_num mixed <p>Номер нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param sticky mixed <p>Флаг закрепления.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'archived' - нить помечена для архивирования.<br>
 * 'posts_count' - число доступных для просмотра сообщений в нити.</p>
 */
function db_threads_get_specifed_view($link, $board_id, $thread_num, $user_id)
{
	$result = mysqli_query($link,
		"call sp_threads_get_specifed_view($board_id, $thread_num, $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) <= 0)
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new PremissionException(PremissionException::$messages['THREAD_NOT_ALLOWED']);
	}
	$row = mysqli_fetch_assoc($result);
	if(isset($row['error']) && $row['error'] == 'NOT_FOUND')
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new NodataException(sprintf(NodataException::$messages['THREAD_NOT_FOUND'],
				$thread_num, $board_id));
	}
	$thread = array('id' => $row['id'],
					'board' => $board_id,
					'original_post' => $row['original_post'],
					'bump_limit' => $row['bump_limit'],
					'sage' => $row['sage'],
					'sticky' => $row['sticky'],
					'with_files' => $row['with_files'],
					'archived' => $row['archived'],
					'posts_count' => $row['visible_posts_count']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
}
/**
 * Получает доступную для просмотра пользователю скрытую нить с заданной доски
 * и количество сообщений в ней.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_num mixed <p>Номер нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param sticky mixed <p>Флаг закрепления.</p>
 * @return array
 * Возвращает нить:<p>
 * 'id' - идентификатор.<br>
 * 'board' - идентификатор доски.<br>
 * 'original_post' - оригинальное сообщение.<br>
 * 'bump_limit' - специфичный для нити бамплимит.<br>
 * 'sticky' - флаг закрепления.<br>
 * 'sage' - флаг поднятия нити<br>
 * 'with_files' - флаг загрузки файлов.<br>
 * 'archived' - нить помечена для архивирования.<br>
 * 'posts_count' - число доступных для просмотра сообщений в нити.</p>
 */
function db_threads_get_specifed_view_hiden($link, $board_id, $thread_num,
	$user_id)
{
	$result = mysqli_query($link,
		"call sp_threads_get_specifed_view_hiden($board_id, $thread_num,
			$user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) <= 0)
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new PremissionException(PremissionException::$messages['THREAD_NOT_ALLOWED']);
	}
	$row = mysqli_fetch_assoc($result);
	if(isset($row['error']) && $row['error'] == 'NOT_FOUND')
	{
		mysqli_free_result($result);
		db_cleanup_link($link);
		throw new NodataException(sprintf(NodataException::$messages['THREAD_NOT_FOUND'],
				$thread_num, $board_id));
	}
	$thread = array('id' => $row['id'],
					'board' => $board_id,
					'original_post' => $row['original_post'],
					'bump_limit' => $row['bump_limit'],
					'sage' => $row['sage'],
					'sticky' => $row['sticky'],
					'with_files' => $row['with_files'],
					'archived' => $row['archived'],
					'posts_count' => $row['visible_posts_count']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
}
/**
 * Получает нить доступную для редактирования заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_specifed_change($link, $thread_id, $user_id)
{
	$result = mysqli_query($link,
		"call sp_threads_get_specifed_change($thread_id, $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$thread = array();
	if(mysqli_affected_rows($link) > 0)
	{
		if(($row = mysqli_fetch_assoc($result)) !== null)
		{
			$thread['id'] = $row['id'];
			$thread['board'] = $row['board'];
			$thread['original_post'] = $row['original_post'];
			$thread['bump_limit'] = $row['bump_limit'];
			$thread['archived'] = $row['archived'];
			$thread['sage'] = $row['sage'];
			$thread['with_files'] = $row['with_files'];
		}
	}
	else
		throw new PremissionException(PremissionException::$messages['THREAD_NOT_ALLOWED']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
}
/**
 * Проверяет, доступна ли нить для модерирования пользователю.
 *
 * Аргументы:
 * $link - связь с базой данных.
 * $thread_id - идентификатор нити.
 * $user_id - идентификатор пользователя.
 *
 * Возвращает true или false.
 */
function db_threads_check_specifed_moderate($link, $thread_id, $user_id)
{
	$result = mysqli_query($link,
		"call sp_threads_check_specifed_moderate($thread_id, $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$affected_rows = mysqli_affected_rows($link);
	mysqli_free_result($result);
	db_cleanup_link($link);
	if($affected_rows <= 0)
		return false;
	return true;
}

/*************************
 * Работа с сообщениями. *
 *************************/

/**
 * Получает $posts_per_thread сообщений и оригинальное сообщение для каждой
 * нити из $threads, доступных для чтения пользователю с идентификатором
 * $user_id.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
 * 'tripcode' - трипкод.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function db_posts_get_threads_view($link, $threads, $user_id, $posts_per_thread)
{
	$posts = array();
	foreach($threads as $t)
	{
		$result = mysqli_query($link, "call sp_posts_get_thread_view({$t['id']},
			$user_id, $posts_per_thread)");
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
			while(($row = mysqli_fetch_assoc($result)) != null)
				array_push($posts,
					array('id' => $row['id'],
							'thread' => $row['thread'],
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
	}
	return $posts;
}
/**
 * Получает все сообщения заданной нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id array <p>Идентификатор нити.</p>
 * @return array
 * Возвращает сообщения:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
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
function db_posts_get_thread($link, $thread_id)
{
	$posts = array();
	$result = mysqli_query($link, "call sp_posts_get_thread($thread_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($posts,
				array('id' => $row['id'],
						'thread' => $row['thread'],
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
 * Получает сообщение по номеру.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
 * 'tripcode' - трипкод.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function db_posts_get_specifed_view_bynumber($link, $board_id, $post_num,
	$user_id)
{
	$result = mysqli_query($link, "call sp_posts_get_specifed_view_bynumber($board_id,
		$post_num, $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$post = null;
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) != null)
	{
		$post['id'] = $row['id'];
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
	if($post === null)
		throw new NodataException(NodataException::$messages['POST_NUMBER_NOT_FOUND']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $post;
}
/**
 * Получает сообщение, доступное для чтения заданному пользоватею, по
 * идентификатору.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает сообщение:<p>
 * 'id' - идентификатор.<br>
 * 'thread' - идентификатор нити.<br>
 * 'board' - идентификатор доски.<br>
 * 'number' - номер.<br>
 * 'password' - пароль для удаления.<br>
 * 'name' - имя отправителя.<br>
 * 'ip' - ip адрес отправителя.<br>
 * 'subject' - тема.<br>
 * 'date_time' - время сохранения.<br>
 * 'text' - текст.<br>
 * 'sage' - флаг поднятия нити.</p>
 */
function db_posts_get_by_id_view($link, $post_id, $user_id)
{
	// TODO Проверка на помеченный на архивирование тред.
	$result = mysqli_query($link, "call sp_posts_get_by_id_view($post_id,
		$user_id)");
	if(!$result)
	{
		throw new CommonException(mysqli_error($link));
	}
	$post = null;
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) != null)
	{
		$post['id'] = $row['id'];
		$post['thread'] = $row['thread'];
		$post['board'] = $row['board'];
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
	if($post === null)
	{
		throw new NodataException(NodataException::$messages['POST_ID_NOT_FOUND']);
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $post;
}
/**
 * Получает все сообщения.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_posts_get_all($link)
{
	$result = mysqli_query($link, 'call sp_posts_get_all()');
	if(!$result)
	{
		throw new CommonException(mysqli_error($link));
	}
	$posts = array();
	if(mysqli_affected_rows($link) > 0)
	{
		while(($row = mysqli_fetch_assoc($result)) !== null)
		{
			array_push($posts,
				array('id' => $row['id'],
						'thread' => $row['thread'],
						'thread_number' => $row['thread_number'],
						'board' => $row['board'],
						'board_name' => $row['board_name'],
						'number' => $row['number'],
						'password' => $row['password'],
						'name' => $row['name'],
						'tripcode' => $row['tripcode'],
						'ip' => $row['ip'],
						'subject' => $row['subject'],
						'date_time' => $row['date_time'],
						'text' => $row['text'],
						'sage' => $row['sage']));
		}
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $posts;
}
/**
 * Получает сообщения с заданных досок.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_posts_get_by_boards($link, $boards)
{
	$posts = array();
	foreach($boards as $b)
	{
		$result = mysqli_query($link, "call sp_posts_get_by_board({$b['id']})");
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
			while(($row = mysqli_fetch_assoc($result)) !== null)
				array_push($posts,
					array('id' => $row['id'],
							'thread' => $row['thread'],
							'thread_number' => $row['thread_number'],
							'board' => $row['board'],
							'board_name' => $row['board_name'],
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
	}
	return $posts;
}
/**
 * Добавляет сообщение.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
 * @param sage mixed <p>Не поднимать нить этим сообщением.</p>
 * @return array
 * Возвращает сообщение.
 */
function db_posts_add($link, $board_id, $thread_id, $user_id, $password, $name,
	$tripcode, $ip, $subject, $datetime, $text, $sage)
{
	$sage = $sage === null ? 'null' : $sage;
	$password = $password == null ? 'null' : "'$password'";
	$result = mysqli_query($link, "call sp_posts_add($board_id, $thread_id,
		$user_id, $password, '$name', '$tripcode', $ip, '$subject',
		'$datetime', '$text', $sage)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $row;
}
/**
 * Удаляет сообщение с заданным идентификатором.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор сообщения.</p>
 */
function db_posts_delete($link, $id)
{
	if(!mysqli_query($link, "call sp_posts_delete($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет сообщение с заданным идентификатором и все сообщения с ip адреса
 * отправителя, оставленные с заданного момента.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param date_time mixed <p>Момент времени.</p>
 */
function db_posts_delete_last($link, $id, $date_time)
{
	if(!mysqli_query($link, "call sp_posts_delete_last($id, '$date_time')"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет сообщения, помеченные на удаление.
 */
function db_posts_delete_all_marked($link)
{
	if(!mysqli_query($link, 'call sp_posts_delete_all_marked()'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Добавляет текст в конец текста заданного сообщения.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param text string <p>Текст.</p>
 */
function db_posts_edit_specifed_addtext($link, $id, $text)
{
	if(!mysqli_query($link, "call sp_posts_edit_specifed_addtext($id, '$text')"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все сообщения с номерами нитей и именами досок.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает сообщения:<p>
 * 'post' - номер сообщения.<br>
 * 'thread' - номер нити.<br>
 * 'board' - номер доски.</p>
 */
function db_posts_get_all_numbers($link)
{
	$result = mysqli_query($link, 'call sp_posts_get_all_numbers()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$data = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($data, array('post' => $row['post'],
									'thread' => $row['thread'],
									'board' => $row['board']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $data;
}

/**********************************************************
 * Работа со связями сообщений с информацией о загрузках. *
 **********************************************************/

/**
 * Получает для сообщений их связи с информацией о загрузках.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param posts array <p>Сообщения.</p>
 * @return array
 * Возвращает связи:<p>
 * 'post' - идентификатор сообщения.<br>
 * 'upload' - идентификатор записи с информацией о загрузке.</p>
 */
function db_posts_uploads_get_posts($link, $posts)
{
	$posts_uploads = array();
	foreach($posts as $p)
	{
		$result = mysqli_query($link,
			"call sp_posts_uploads_get_post({$p['id']})");
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
			while(($row = mysqli_fetch_assoc($result)) != null)
				array_push($posts_uploads,
					array('post' => $row['post'],
							'upload' => $row['upload']));
		mysqli_free_result($result);
		db_cleanup_link($link);
	}
	return $posts_uploads;
}
/**
 * Связывает сообщение с информацией о загрузке.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>идентификатор сообщения.</p>
 * @param upload_id mixed <p>идентификатор записи с информацией о загрузке.</p>
 */
function db_posts_uploads_add($link, $post_id, $upload_id)
{
	if(!mysqli_query($link, "call sp_posts_uploads_add($post_id, $upload_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет все связи сообщения с информацией о загрузках.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 */
function db_posts_uploads_delete_post($link, $post_id)
{
	if(!mysqli_query($link, "call sp_posts_uploads_delete_post($post_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/*************************************
 * Работа с информацией о загрузках. *
 *************************************/

/**
 * Получает для каждого сообщения из массива сообщений информацию о загрузках.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_uploads_get_posts($link, $posts)
{
	$uploads = array();
	foreach($posts as $p)
	{
		$result = mysqli_query($link, "call sp_uploads_get_post({$p['id']})");
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
			while(($row = mysqli_fetch_assoc($result)) != null)
				array_push($uploads,
					array('id' => $row['id'],
							'hash' => $row['hash'],
							'is_image' => $row['is_image'],
							'link_type' => $row['link_type'],
							'file' => $row['file'],
							'file_w' => $row['file_w'],
							'file_h' => $row['file_h'],
							'size' => $row['size'],
							'thumbnail' => $row['thumbnail'],
							'thumbnail_w' => $row['thumbnail_w'],
							'thumbnail_h' => $row['thumbnail_h']));
		mysqli_free_result($result);
		db_cleanup_link($link);
	}
	return $uploads;
}
/**
 * Получает одинаковые файлы, загруженные на заданную доску.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_uploads_get_same($link, $board_id, $hash, $user_id)
{
	$result = mysqli_query($link,
		"call sp_uploads_get_same($board_id, '$hash', $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$uploads = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($uploads,
				array('id' => $row['id'],
						'hash' => $row['hash'],
						'is_image' => $row['is_image'],
						'link_type' => $row['link_type'],
						'file' => $row['file'],
						'file_w' => $row['file_w'],
						'file_h' => $row['file_h'],
						'size' => $row['size'],
						'thumbnail' => $row['thumbnail'],
						'thumbnail_w' => $row['thumbnail_w'],
						'thumbnail_h' => $row['thumbnail_h'],
						'post_number' => $row['number'],
						'thread_number' => $row['original_post'],
						'view' => $row['view']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $uploads;
}
/**
 * Сохраняет данные о загрузке.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_uploads_add($link, $hash, $is_image, $link_type, $file, $file_w,
	$file_h, $size, $thumbnail, $thumbnail_w, $thumbnail_h)
{
	$is_image = $is_image ? '1' : '0';
	$hash = $hash ? "'$hash'" : 'null';
	$file_w = $file_w ? $file_w : 'null';
	$file_h = $file_h ? $file_h : 'null';
	$thumbnail = $thumbnail ? "'$thumbnail'" : 'null';
	$thumbnail_w = $thumbnail_w ? $thumbnail_w : 'null';
	$thumbnail_h = $thumbnail_h ? $thumbnail_h : 'null';
	$result = mysqli_query($link, "call sp_uploads_add($hash, $is_image,
		$link_type, '$file', $file_w, $file_h, $size, $thumbnail, $thumbnail_w,
		$thumbnail_h)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $row['id'];
}
/**
 * Получает информацию о висячих загрузках (не связанных с сообщениями).
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает информация о висячих загрузках:<p>
 * 'id' - идентификатор.<br>
 * 'hash' - хеш файла.<br>
 * 'is_image' - флаг картинки.<br>
 * 'upload_type' - тип загрузки.<br>
 * 'link' - имя файла, ссылка или код видео.<br>
 * 'image_w' - ширина изображения.<br>
 * 'image_h' - высота изображения.<br>
 * 'size' - размер файла в байтах.<br>
 * 'thumbnail' - имя уменьшенной копии.<br>
 * 'thumbnail_w' - ширина уменьшенной копии.<br>
 * 'thumbnail_h' - высота уменьшенной копии.</p>
 */
// TODO Продолжить работу по переименованию полей таблицы.
function db_uploads_get_all_dangling($link)
{
	$result = mysqli_query($link, 'call sp_uploads_get_all_dangling()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$uploads = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($uploads,
				array('id' => $row['id'],
						'hash' => $row['hash'],
						'is_image' => $row['is_image'],
						'upload_type' => $row['link_type'],
						'link' => $row['file'],
						'image_w' => $row['file_w'],
						'image_h' => $row['file_h'],
						'size' => $row['size'],
						'thumbnail' => $row['thumbnail'],
						'thumbnail_w' => $row['thumbnail_w'],
						'thumbnail_h' => $row['thumbnail_h']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $uploads;
}
/**
 * Удаляет заданную информацию о загрузке.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id string <p>Идентификатор информации о загрузке.</p>
 */
function db_uploads_delete_specifed($link, $id)
{
	if(!mysqli_query($link, "call sp_uploads_delete_specifed($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}

/******************************
 * Работа со скрытыми нитями. *
 ******************************/

/**
 * Получает нити, скрыте пользователем на заданной доске.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает скрытые нити:<p>
 * 'number' - номер оригинального сообщения.<br>
 * 'id' - идентификатор нити.</p>
 */
function db_hidden_threads_get_board($link, $board_id, $user_id)
{
	$result = mysqli_query($link,
		"call sp_hidden_threads_get_board($board_id, $user_id)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$hidden_threads = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			array_push($hidden_threads,
				array('number' => $row['original_post'],
						'id' => $row['id']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $hidden_threads;
}
/**
 * Скрывает нить.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function db_hidden_threads_add($link, $thread_id, $user_id)
{
	if(!mysqli_query($link, "call sp_hidden_threads_add($thread_id, $user_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Отменяет скрытие нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор доски.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function db_hidden_threads_delete($link, $thread_id, $user_id)
{
	if(!mysqli_query($link, "call sp_hidden_threads_delete($thread_id,
			$user_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
?>