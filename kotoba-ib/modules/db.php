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
 * @param link MySQLi <p>Связь с базой данных.</p>
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

/***************************************
 * Работа со списком контроля доступа. *
 ***************************************/

/**
 * Добавляет правило в список контроля доступа.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param group_id mixed <p>Идентификатор группы.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_id mixed <p>Идентификатор нить.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @param view mixed <p>Право на просмотр.</p>
 * @param change mixed <p>Право на изменение.</p>
 * @param moderate mixed <p>Право на модерирование.</p>
 */
function db_acl_add($link, $group_id, $board_id, $thread_id, $post_id, $view,
	$change, $moderate)
{
    if($group_id === null)
        $group_id = 'null';
    if($board_id === null)
        $board_id = 'null';
    if($thread_id === null)
        $thread_id = 'null';
    if($post_id === null)
        $post_id = 'null';
    $result = mysqli_query($link, 'call sp_acl_add(' . $group_id . ', '
        . $board_id . ', ' . $thread_id . ', ' . $post_id . ', '
        . $view . ', ' . $change . ', ' . $moderate . ')');
    if(!$result)
        throw new CommonException(mysqli_error($link));
    db_cleanup_link($link);
}
/**
 * Удаляет правило из списка контроля доступа.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param group_id mixed <p>Группа.</p>
 * @param board_id mixed <p>Доска.</p>
 * @param thread_id mixed <p>Нить.</p>
 * @param post_id mixed <p>Сообщение.</p>
 */
function db_acl_delete($link, $group_id, $board_id, $thread_id, $post_id)
{
	$group_id = ($group_id === null ? 'null' : $group_id);
	$board_id = ($board_id === null ? 'null' : $board_id);
	$thread_id = ($thread_id === null ? 'null' : $thread_id);
	$post_id = ($post_id === null ? 'null' : $post_id);
	$result = mysqli_query($link, 'call sp_acl_delete(' . $group_id . ', '
			. $board_id . ', ' . $thread_id . ', ' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Редактирует правило в списке контроля доступа.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param group_id mixed <p>Группа.</p>
 * @param board_id mixed <p>Доска.</p>
 * @param thread_id mixed <p>Нить.</p>
 * @param post_id mixed <p>Сообщение.</p>
 * @param view mixed <p>Право на просмотр.</p>
 * @param change mixed <p>Право на изменение.</p>
 * @param moderate mixed <p>Право на модерирование.</p>
 */
function db_acl_edit($link, $group_id, $board_id, $thread_id, $post_id, $view,
	$change, $moderate)
{
	$group_id = ($group_id === null ? 'null' : $group_id);
	$board_id = ($board_id === null ? 'null' : $board_id);
	$thread_id = ($thread_id === null ? 'null' : $thread_id);
	$post_id = ($post_id === null ? 'null' : $post_id);
	$result = mysqli_query($link, 'call sp_acl_edit(' . $group_id . ', '
			. $board_id . ', ' . $thread_id . ', ' . $post_id . ', '
			. $view . ', ' . $change . ', ' . $moderate . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает список контроля доступа.
 * @param link MySQLi <p>Связь с базой данных.</p>
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

/**************************
 * Работа с блокировками. *
 **************************/

/**
 * Блокирует заданный диапазон IP-адресов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param range_beg int <p>Начало диапазона IP-адресов.</p>
 * @param range_end int <p>Конец диапазона IP-адресов.</p>
 * @param reason string <p>Причина блокировки.</p>
 * @param untill string <p>Время истечения блокировки.</p>
 */
function db_bans_add($link, $range_beg, $range_end, $reason, $untill)
{
    if($reason === null)
        $reason = 'null';
    if(!mysqli_query($link, 'call sp_bans_add(' . $range_beg . ', '
        . $range_end . ', \'' . $reason . '\', \'' . $untill . '\')'))
    {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Проверяет, заблокирован ли IP-адрес. Если да, то завершает работу скрипта.
 * @param smarty SmartyKotobaSetup <p>Экземпляр шаблонизатора.</p>
 * @param ip string <p>IP-адрес.</p>
 * @return mixed
 * Возвращает false, если адрес не заблокирован и массив, если заблокирован:<p>
 * 'range_beg' - Начало диапазона IP-адресов.<br>
 * 'range_end' - Конец диапазона IP-адресов.<br>
 * 'reason' - Причина блокировки.<br>
 * 'untill' - Время истечения блокировки.</p>
 */
function db_bans_check($link, $ip)
{
	$result = mysqli_query($link, 'call sp_bans_check(' . $ip . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
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
 * Удаляет блокировку с заданным идентификатором.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id miexd <p>Идентификатор блокировки.</p>
 */
function db_bans_delete_by_id($link, $id)
{
	if(!mysqli_query($link, 'call sp_bans_delete_by_id(' . $id . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет блокировки с заданным IP-адресом.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param ip int <p>IP-адрес.</p>
 */
function db_bans_delete_by_ip($link, $ip)
{
	if(!mysqli_query($link, 'call sp_bans_delete_by_ip(' . $ip . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все блокировки.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает блокировки:<p>
 * 'id' - Идентификатор.<br>
 * 'range_beg' - Начало диапазона IP-адресов.<br>
 * 'range_end' - Конец диапазона IP-адресов.<br>
 * 'reason' - Причина блокировки.<br>
 * 'untill' - Время истечения блокировки.</p>
 */
function db_bans_get_all($link)
{
	$result = mysqli_query($link, 'call sp_bans_get_all()');
	if(!$result)
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

/*******************************************************
 * Работа со связями досок и типов загружаемых файлов. *
 *******************************************************/

/**
 * Добавляет связь доски с типом загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param upload_type_id mixed <p>Идентификатор типа загружаемого файла.</p>
 */
function db_board_upload_types_add($link, $board, $upload_type)
{
	if(!mysqli_query($link, 'call sp_board_upload_types_add(' . $board . ', '
        . $upload_type . ')'))
    {
		throw new CommonException(mysqli_error($link));
    }
	db_cleanup_link($link);
}
/**
 * Удаляет связь доски с типом загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board mixed <p>Доска.</p>
 * @param upload_type mixed <p>Тип загружаемого файла.</p>
 */
function db_board_upload_types_delete($link, $board, $upload_type)
{
	if(!mysqli_query($link, 'call sp_board_upload_types_delete(' . $board . ', '
			. $upload_type . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все связи досок с типами загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает связи:<p>
 * 'board' - Доска.<br>
 * 'upload_type' - Тип загружаемого файла.</p>
 */
function db_board_upload_types_get_all($link)
{
	$result = mysqli_query($link, 'call sp_board_upload_types_get_all()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$board_upload_types = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($board_upload_types, array('board' => $row['board'],
					'upload_type' => $row['upload_type']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $board_upload_types;
}

/*********************
 * Работа с досками. *
 *********************/

/**
 * Добавляет доску.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
 * @param popdown_handler mixed <p>Идентификатор обработчика автоматического
 * удаления нитей.</p>
 * @param category mixed <p>Идентификатор категории.</p>
 */
function db_boards_add($link, $name, $title, $annotation, $bump_limit,
	$force_anonymous, $default_name, $with_attachments, $enable_macro,
	$enable_youtube, $enable_captcha, $same_upload, $popdown_handler, $category)
{
    if($title === null)
        $title = 'null';
    if($annotation === null)
        $annotation = 'null';
    if($default_name === null)
        $default_name = 'null';
    if($enable_macro === null)
        $enable_macro = 'null';
    if($enable_youtube === null)
        $enable_youtube = 'null';
    if($enable_captcha === null)
        $enable_captcha = 'null';
    if(!mysqli_query($link, 'call sp_boards_add(\'' . $name . '\', \''
        . $title . '\', \'' . $annotation . '\', ' . $bump_limit . ', '
        . $force_anonymous . ', \'' . $default_name . '\', '
        . $with_attachments . ', ' . $enable_macro . ', ' . $enable_youtube . ', '
        . $enable_captcha . ', \'' . $same_upload . '\', ' . $popdown_handler . ', '
        . $category . ')'))
    {
        throw new CommonException(mysqli_error($link));
    }
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
/**
 * Редактирует доску.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_edit($link, $id, $title, $annotation, $bump_limit,
	$force_anonymous, $default_name, $with_attachments, $enable_macro,
	$enable_youtube, $enable_captcha, $same_upload, $popdown_handler, $category)
{
	if($title === null)
		$title = 'null';
	if($annotation === null)
		$annotation = 'null';
	if($default_name === null)
		$default_name = 'null';
	if($enable_macro === null)
		$enable_macro = 'null';
	if($enable_youtube === null)
		$enable_youtube = 'null';
	if($enable_captcha === null)
		$enable_captcha = 'null';
	if(!mysqli_query($link, "call sp_boards_edit($id, $title, $bump_limit,
			$force_anonymous, $default_name, $with_files, '$same_upload',
			$popdown_handler, $category)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
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
function db_boards_get_all($link)
{
	$result = mysqli_query($link, 'call sp_boards_get_all()');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$boards = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
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
						'same_upload' => $row['same_upload'],
						'popdown_handler' => $row['popdown_handler'],
						'category' => $row['category']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $boards;
}
/**
 * Получает заданную доску.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
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
function db_boards_get_by_id($link, $board_id)
{
	$result = mysqli_query($link, 'call sp_boards_get_by_id(' . $board_id . ')');
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
		$board['with_attachments'] = $row['with_files'];
		$board['enable_macro'] = $row['enable_macro'];
		$board['enable_youtube'] = $row['enable_youtube'];
		$board['enable_captcha'] = $row['enable_captcha'];
		$board['same_upload'] = $row['same_upload'];
		$board['popdown_handler'] = $row['popdown_handler'];
		$board['category'] = $row['category'];
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
 * Получает заданную доску, доступную для редактирования заданному
 * пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_boards_get_changeable_by_id($link, $board_id, $user_id)
{
	$result = mysqli_query($link,
		'call sp_boards_get_changeable_by_id(' . $board_id . ', ' . $user_id . ')');
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
 * Получает доски, доступные для просмотра заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return array
 * Возвращает доски:<p>
 * 'id' - идентификатор.<br>
 * 'name' - имя.<br>
 * 'title' - заголовок.<br>
 * 'annotation' - аннотация.<br>
 * 'bump_limit' - спецефиный для доски бамплимит.<br>
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
function db_boards_get_visible($link, $user_id)
{
	$result = mysqli_query($link, 'call sp_boards_get_visible(' . $user_id . ')');
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

/*************************
 * Работа с категориями. *
 *************************/

/**
 * Добавляет категорию.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param name string <p>Имя категории.</p>
 */
function db_categories_add($link, $name)
{
    if(!mysqli_query($link, 'call sp_categories_add(\'' . $name . '\')'))
        throw new CommonException(mysqli_error($link));
    db_cleanup_link($link);
}
/**
 * Удаляет заданную категорию.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор.</p>
 */
function db_categories_delete($link, $id)
{
	if(!mysqli_query($link, 'call sp_categories_delete(' . $id . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все категории.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает категории:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.</p>
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

/********************************
 * Работа с вложенными файлами. *
 ********************************/

/**
 * Получает файлы, вложенные в заданное сообщение.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @return array
 * Возвращает вложенные файлы:<p>
 * 'id' - Идентификатор.<br>
 * 'hash' - Хеш.<br>
 * 'name' - Имя.<br>
 * 'size' - Размер в байтах.<br>
 * 'thumbnail' - Уменьшенная копия.<br>
 * 'thumbnail_w' - Ширина уменьшенной копии.<br>
 * 'thumbnail_h' - Высота уменьшенной копии.<br>
 * 'attachment_type' - Тип вложения - файл.</p>
 */
function db_files_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_files_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$files = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($files,
				array('id' => $row['id'],
						'hash' => $row['hash'],
						'name' => $row['name'],
						'size' => $row['size'],
						'thumbnail' => $row['thumbnail'],
						'thumbnail_w' => $row['thumbnail_w'],
						'thumbnail_h' => $row['thumbnail_h'],
						'attachment_type' => Config::ATTACHMENT_TYPE_FILE));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $files;
}

/**********************
 * Работа с группами. *
 **********************/

/**
 * Добавляет группу.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param name string <p>Имя группы.</p>
 * @return string
 * Возвращает идентификатор добавленной группы. Или null, если что-то пошло не
 * так.
 */
function db_groups_add($link, $name)
{
    $result = mysqli_query($link, 'call sp_groups_add(\'' . $name . '\')');
    if(!$result)
        throw new CommonException(mysqli_error($link));
    if(mysqli_affected_rows($link) <= 0
        || ($row = mysqli_fetch_assoc($result)) === null)
    {
        throw new CommonException(CommonException::$messages['GROUPS_ADD']);
    }
    mysqli_free_result($result);
    db_cleanup_link($link);
    return $row['id'];
}
/**
 * Удаляет заданные группы, а так же всех пользователей, которые входят в эти
 * группы и все правила в ACL, распространяющиеся на эти группы.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param groups array <p>Группы.</p>
 */
function db_groups_delete($link, $group_ids)
{
	foreach($group_ids as $id)
	{
		$result = mysqli_query($link, 'call sp_groups_delete(' . $id . ')');
		if(!$result)
			throw new CommonException(mysqli_error($link));
		db_cleanup_link($link);
	}
}
/**
 * Получает все группы.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает группы:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя.</p>
 */
function db_groups_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_groups_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$groups = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($groups, array('id' => $row['id'], 'name' => $row['name']));
	else
		throw new NodataException(NodataException::$messages['GROUPS_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $groups;
}

/******************************
 * Работа со скрытыми нитями. *
 ******************************/

/**
 * Скрывает нить.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function db_hidden_threads_add($link, $thread_id, $user_id)
{
    if(!mysqli_query($link, 'call sp_hidden_threads_add(' . $thread_id . ', '
        . $user_id . ')'))
    {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Отменяет скрытие нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 */
function db_hidden_threads_delete($link, $thread_id, $user_id)
{
	if(!mysqli_query($link, 'call sp_hidden_threads_delete(' . $thread_id . ', '
			. $user_id . ')'))
	{
		throw new CommonException(mysqli_error($link));
	}
	db_cleanup_link($link);
}
/**
 * Возвращает отфильтрованные скрытые нити на заданных досках.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param boards array <p>Доски.</p>
 * @return array
 * Возвращает скрытые нити:<p>
 * 'thread' - Идентификатор нити.<br>
 * 'thread_number' - Номер оригинального сообщения.<br>
 * 'user' - Идентификатор пользователя.</p>
 */
function db_hidden_threads_get_by_boards($link, $boards)
{
	$threads = array();
	foreach($boards as $b)
	{
		$result = mysqli_query($link,
			'call sp_hidden_threads_get_by_board(' . $b['id'] . ')');
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
			while(($row = mysqli_fetch_assoc($result)) != null)
				array_push($threads,
					array('thread' => $row['thread'],
							'thread_number' => $row['original_post'],
							'user' => $row['user']));
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


/**************************************
 * Работа с вложенными изображениями. *
 **************************************/

/**
 * Получает изображения, вложенные в заданное сообщение.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @return array
 * Возвращает вложенные изображения:<p>
 * 'id' - Идентификатор.<br>
 * 'hash' - Хеш.<br>
 * 'name' - Имя.<br>
 * 'widht' - Ширина.<br>
 * 'height' - Высота.<br>
 * 'size' - Размер в байтах.<br>
 * 'thumbnail' - Уменьшенная копия.<br>
 * 'thumbnail_w' - Ширина уменьшенной копии.<br>
 * 'thumbnail_h' - Высота уменьшенной копии.<br>
 * 'attachment_type' - Тип вложения (изображение).</p>
 */
function db_images_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_images_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$images = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
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
						'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $images;
}
/**
 * Получает одинаковые вложенные изображения на заданной доски.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_images_get_same($link, $board_id, $image_hash, $user_id)
{
	$result = mysqli_query($link, 'call sp_images_get_same(' . $board_id
			. ', \'' . $image_hash . '\', ' . $user_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$images = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
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
						'post_number' => $row['number'],
						'thread_number' => $row['original_post'],
						'view' => $row['view']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $images;
}

/*********************
 * Работа с языками. *
 *********************/

/**
 * Добавляет язык.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param code string <p>ISO_639-2 код языка.</p>
 */
function db_languages_add($link, $code)
{
    $result = mysqli_query($link, 'call sp_languages_add(\'' . $code . '\')');
    if(!$result)
        throw new CommonException(mysqli_error($link));
    db_cleanup_link($link);
}
/**
 * Удаляет язык с заданным идентификатором.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор языка.</p>
 */
function db_languages_delete($link, $id)
{
	$result = mysqli_query($link, 'call sp_languages_delete(' . $id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все языки.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает языки:<p>
 * 'id' - Идентификатор.<br>
 * 'code' - Код ISO_639-2.</p>
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

/************************************************
 * Работа с вложенными ссылками на изображения. *
 ************************************************/

/**
 * Получает ссылки на изображения, вложенные в заданное сообщение.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @return array
 * Возвращает вложенные ссылки на изображения:<p>
 * 'id' - Идентификатор.<br>
 * 'url' - URL.<br>
 * 'widht' - Ширина.<br>
 * 'height' - Высота.<br>
 * 'size' - Размер в байтах.<br>
 * 'thumbnail' - URL уменьшенной копии.<br>
 * 'thumbnail_w' - Ширина уменьшенной копии.<br>
 * 'thumbnail_h' - Высота уменьшенной копии.<br>
 * 'attachment_type' - Тип вложения - ссылка.</p>
 */
function db_links_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_links_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$links = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
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
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $links;
}

/**********************************************************
 * Работа с обработчиками автоматического удаления нитей. *
 **********************************************************/

/**
 * Добавляет обработчик автоматического удаления нитей.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param name string <p>Имя функции обработчика автоматического удаления
 * нитей.</p>
 */
function db_popdown_handlers_add($link, $name)
{
    if(!mysqli_query($link, 'call sp_popdown_handlers_add(\'' . $name . '\')'))
        throw new CommonException(mysqli_error($link));
    db_cleanup_link($link);
}
/**
 * Удаляет обработчик автоматического удаления нитей.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор обработчика автоматического удаления
 * нитей.</p>
 */
function db_popdown_handlers_delete($link, $id)
{
	if(!mysqli_query($link, 'call sp_popdown_handlers_delete(' . $id . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все обработчики автоматического удаления нитей.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает обработчики автоматического удаления нитей:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя функции.</p>
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

/*************************
 * Работа с сообщениями. *
 *************************/

/**
 * Добавляет сообщение.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param password string <p>Пароль на удаление сообщения.</p>
 * @param name string <p>Имя отправителя.</p>
 * @param tripcode string <p>Трипкод.</p>
 * @param ip int <p>IP-адрес отправителя.</p>
 * @param subject string <p>Тема.</p>
 * @param date_time string <p>Время сохранения.</p>
 * @param text string <p>Текст.</p>
 * @param sage mixed <p>Флаг поднятия нити.</p>
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
 * Или null, если что-то пошло не так.
 */
function db_posts_add($link, $board_id, $thread_id, $user_id, $password, $name,
    $tripcode, $ip, $subject, $date_time, $text, $sage)
{
    if($sage === null)
        $sage = 'null';
    if($password == null)
        $password = 'null';
    $result = mysqli_query($link, 'call sp_posts_add(' . $board_id . ', '
        . $thread_id . ', ' . $user_id . ', \'' . $password . '\', \''
        . $name . '\', \'' . $tripcode . '\', ' . $ip . ', \''
        . $subject . '\', \'' . $date_time . '\', \'' . $text . '\', '
        . $sage . ')');
    if(!$result)
        throw new CommonException(mysqli_error($link));
    $post = null;
    if(mysqli_affected_rows($link) > 0
        && ($row = mysqli_fetch_assoc($result)) !== null)
    {
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
 * Удаляет сообщение с заданным идентификатором.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор сообщения.</p>
 */
function db_posts_delete($link, $id)
{
	if(!mysqli_query($link, 'call sp_posts_delete(' . $id . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет сообщение с заданным идентификатором и все сообщения с ip адреса
 * отправителя, оставленные с заданного момента времени.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param date_time mixed <p>Момент времени.</p>
 */
function db_posts_delete_last($link, $id, $date_time)
{
	if(!mysqli_query($link, 'call sp_posts_delete_last(' . $id . ', \'' . $date_time . '\')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Удаляет сообщения, помеченные на удаление.
 */
function db_posts_delete_marked($link)
{
	if(!mysqli_query($link, 'call sp_posts_delete_marked()'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Редактирует текст заданного сообщения, добавляя в конец его текста заданный
 * текст.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор сообщения.</p>
 * @param text string <p>Текст.</p>
 */
function db_posts_edit_text_by_id($link, $id, $text)
{
	if(!mysqli_query($link, 'call sp_posts_add_text_by_id(' . $id . ', \''
			. $text . '\')'))
	{
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
 * Получает сообщения с заданных досок.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_posts_get_by_boards($link, $boards)
{
	$posts = array();
	foreach($boards as $b)
	{
		$result = mysqli_query($link, 'call sp_posts_get_by_board('
			. $b['id'] . ')');
		if(!$result)
			throw new CommonException(mysqli_error($link));
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
	}
	return $posts;
}
/**
 * Получает сообщения заданной нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_posts_get_by_thread($link, $thread_id)
{
	$posts = array();
	$result = mysqli_query($link, 'call sp_posts_get_by_thread('
			. $thread_id . ')');
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
 * Получает заданное сообщение, доступное для просмотра заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
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
function db_posts_get_visible_by_id($link, $post_id, $user_id)
{
	$result = mysqli_query($link, 'call sp_posts_get_visible_by_id('
		. $post_id . ', ' . $user_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$post = null;
	if(mysqli_affected_rows($link) > 0
		&& ($row = mysqli_fetch_assoc($result)) != null)
	{
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
	if($post === null)
		throw new NodataException(NodataException::$messages['POST_NOT_FOUND']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	$board = db_boards_get_by_id($link, $post['board']);
	$post['board_name'] = $board['name'];
	return $post;
}
/**
 * Получает заданное сообщение, доступное для просмотра заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_posts_get_visible_by_number($link, $board_id, $post_number,
	$user_id)
{
	$result = mysqli_query($link, 'call sp_posts_get_visible_by_number('
			. $board_id . ', ' . $post_number . ', ' . $user_id . ')');
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
		throw new NodataException(NodataException::$messages['POST_NOT_FOUND']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $post;
}
/**
 * Для каждой нити получает отфильтрованные сообщения, доступные для просмотра
 * заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param threads array <p>Нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param filter mixed <p>Фильтр (лямбда).</p>
 * @param args array <p>Аргументы для фильтра.</p>
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
function db_posts_get_visible_filtred_by_threads($link, $threads, $user_id,
	$filter, $args)
{
	$posts = array();
	$arg = count($args);
	foreach($threads as $t)
	{
		$result = mysqli_query($link, 'call sp_posts_get_visible_by_thread('
			. $t['id'] . ', ' . $user_id . ')');
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
		{
			$args[$arg + 1] = $t;
			while(($row = mysqli_fetch_assoc($result)) != null)
			{
				$args[$arg + 2] = $row;
				if(call_user_func_array($filter, $args))
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
			}
		}
		mysqli_free_result($result);
		db_cleanup_link($link);
	}
	return $posts;
}

/***************************************************
 * Работа со связями сообщений и вложенных файлов. *
 ***************************************************/

/**
 * Получает связи заданного сообщения с вложенными файлами.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @return array
 * Возвращает связи сообщения с вложенными файлами:<p>
 * 'post' - Идентификатор сообщения.<br>
 * 'file' - Идентификатор вложенного файла.<br>
 * 'deleted' - Флаг удаления.<br>
 * 'attachment_type' - Тип вложения - файл.</p>
 */
function db_posts_files_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_posts_files_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$posts_files = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($posts_files,
				array('post' => $row['post'],
						'file' => $row['file'],
						'deleted' => $row['deleted'],
						'attachment_type' => Config::ATTACHMENT_TYPE_FILE));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $posts_files;
}

/********************************************************
 * Работа со связями сообщений и вложенных изображений. *
 ********************************************************/

/**
 * Получает связи заданного сообщения с вложенными изображениями.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @return array
 * Возвращает связи сообщения с вложенными изображениями:<p>
 * 'post' - Идентификатор сообщения.<br>
 * 'image' - Идентификатор вложенного изображения.<br>
 * 'deleted' - Флаг удаления.<br>
 * 'attachment_type' - Тип вложения - изображение.</p>
 */
function db_posts_images_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_posts_images_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$posts_images = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($posts_images,
				array('post' => $row['post'],
						'image' => $row['image'],
						'deleted' => $row['deleted'],
						'attachment_type' => Config::ATTACHMENT_TYPE_IMAGE));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $posts_images;
}

/******************************************************************
 * Работа со связями сообщений и вложенных ссылок на изображения. *
 ******************************************************************/

/**
 * Получает связи заданного сообщения с вложенными ссылками на изображения.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @return array
 * Возвращает связи сообщения с вложенными ссылками на изображения:<p>
 * 'post' - Идентификатор сообщения.<br>
 * 'link' - Идентификатор вложенной ссылки на изображение.<br>
 * 'deleted' - Флаг удаления.<br>
 * 'attachment_type' - Тип вложения - ссылка на изображение.</p>
 */
function db_posts_links_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_posts_links_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$posts_links = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($posts_links,
				array('post' => $row['post'],
						'link' => $row['link'],
						'deleted' => $row['deleted'],
						'attachment_type' => Config::ATTACHMENT_TYPE_LINK));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $posts_links;
}

/***************************************************
 * Работа со связями сообщений и вложенного видео. *
 ***************************************************/

/**
 * Получает связи заданного сообщения с вложенным видео.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 * @return array
 * Возвращает связи сообщения с вложенным видео:<p>
 * 'post' - Идентификатор сообщения.<br>
 * 'video' - Идентификатор вложенного видео.<br>
 * 'deleted' - Флаг удаления.<br>
 * 'attachment_type' - Тип вложения - видео.</p>
 */
function db_posts_videos_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_posts_videos_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$posts_videos = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($posts_videos,
				array('post' => $row['post'],
						'video' => $row['video'],
						'deleted' => $row['deleted'],
						'attachment_type' => Config::ATTACHMENT_TYPE_VIDEO));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $posts_videos;
}

/**********************
 * Работа со стилями. *
 **********************/

/**
 * Добавляет стиль.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param name string <p>Имя файла стиля.</p>
 */
function db_stylesheets_add($link, $name)
{
    if(!mysqli_query($link, 'call sp_stylesheets_add(\'' . $name . '\')'))
        throw new CommonException(mysqli_error($link));
    db_cleanup_link($link);
}
/**
 * Удаляет заданный стиль.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор стиля.</p>
 */
function db_stylesheets_delete($link, $id)
{
	if(!mysqli_query($link, 'call sp_stylesheets_delete(' . $id . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все стили.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает стили:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя файла.</p>
 */
function db_stylesheets_get_all($link)
{
	$result = mysqli_query($link, 'call sp_stylesheets_get_all()');
	if(!$result)
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

/********************
 * Работа с нитями. *
 ********************/

/**
 * Добавляет нить. Если номер оригинального сообщения null, то будет создана
 * пустая нить.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param original_post mixed <p>Номер оригинального сообщения.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sage mixed <p>Флаг поднятия нити.</p>
 * @param with_attachments mixed <p>Флаг вложений.</p>
 * @return mixed
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
function db_threads_add($link, $board_id, $original_post, $bump_limit, $sage,
    $with_files)
{
    if($original_post === null)
        $original_post = 'null';
    if($bump_limit === null)
        $bump_limit = 'null';
    $sage = $sage ? '1' : '0';
    if($with_files === null)
        $with_files = 'null';
    $result = mysqli_query($link, 'call sp_threads_add(' . $board_id . ', '
        . $original_post . ', ' . $bump_limit . ', ' . $sage . ', '
        . $with_files . ')');
    if(!$result)
        throw new CommonException(mysqli_error($link));
    $thread = null;
    if(mysqli_affected_rows($link) > 0
        && ($row = mysqli_fetch_assoc($result)) !== null)
    {
        $thread['id'] = $row['id'];
        $thread['board'] = $row['board'];
        $thread['original_post'] = $row['original_post'];
        $thread['bump_limit'] = $row['bump_limit'];
        $thread['sage'] = $row['sage'];
        $thread['sticky'] = $row['sticky'];
        $thread['with_attachments'] = $row['with_attachments'];
    }
    mysqli_free_result($result);
    db_cleanup_link($link);
    return $thread;
}
/**
 * Редактирует заданную нить.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param bump_limit mixed <p>Специфичный для нити бамплимит.</p>
 * @param sage mixed <p>Флаг поднятия нити.</p>
 * @param sticky mixed <p>Флаг закрепления.</p>
 * @param with_attachments mixed <p>Флаг вложений.</p>
 */
function db_threads_edit($link, $thread_id, $bump_limit, $sticky, $sage,
	$with_attachments)
{
	$bump_limit = ($bump_limit === null ? 'null' : $bump_limit);
	$with_attachments = ($with_attachments === null ? 'null' : $with_attachments);
	if(!mysqli_query($link, 'call sp_threads_edit(' . $thread_id . ', '
		. $bump_limit . ', ' . $sticky . ', ' . $sage . ', '
		. $with_attachments . ')'))
	{
		throw new CommonException(mysqli_error($link));
	}
	db_cleanup_link($link);
}
/**
 * Редактирует номер оригинального сообщения нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор нити.</p>
 * @param original_post mixed <p>Номер оригинального сообщения нити.</p>
 */
function db_threads_edit_original_post($link, $id, $original_post)
{
	if(!mysqli_query($link, 'call sp_threads_edit_original_post(' . $id . ', '
			. $original_post . ')'))
	{
		throw new CommonException(mysqli_error($link));
	}
	db_cleanup_link($link);
}
/**
 * Получает все нити.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_all($link)
{
	$result = mysqli_query($link, 'call sp_threads_get_all()');
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
 * Получает заданную нить, доступную для редактирования заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_changeable_by_id($link, $thread_id, $user_id)
{
	$result = mysqli_query($link, 'call sp_threads_get_changeable_by_id('
		. $thread_id . ', ' . $user_id . ')');
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
			$thread['with_attachments'] = $row['with_attachments'];
		}
	}
	else
		throw new PermissionException(PermissionException::$messages['THREAD_NOT_ALLOWED']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
}
/**
 * Получает нити, доступные для модерирования заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
function db_threads_get_moderatable($link, $user_id)
{
	$result = mysqli_query($link, 'call sp_threads_get_moderatable('
		. $user_id . ')');
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
						'sage' => $row['sage'],
						'sticky' => $row['sticky'],
						'with_attachments' => $row['with_attachments']));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Получает заданную нить, доступную для модерирования заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param thread_id mixed <p>Идентификатор нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @return mixed
 * Возвращает нить:<p>
 * 'id' - Идентификатор.</p>
 * Или null, если заданная нить не доступна для модерирования.
 */
function db_threads_get_moderatable_by_id($link, $thread_id, $user_id)
{
	$result = mysqli_query($link, 'call sp_threads_get_moderatable_by_id('
		. $thread_id . ', ' . $user_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$thread = null;
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
			$thread['id'] = $row['id'];
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
}
/**
 * Получает с заданной страницы доски доступные для просмотра пользователю нити
 * и количество сообщений в них.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param page mixed <p>Номер страницы.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param threads_per_page mixed <p>Количество нитей на странице.</p>
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
function db_threads_get_visible_by_board($link, $board_id, $page, $user_id,
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
	$result = mysqli_query($link, 'call sp_threads_get_visible_by_board('
		. $board_id . ', ' . $user_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) != null)
		{
			if($row['sticky'])
			{
				if($page == 1)
					// Закреплённые нити будут показаны только на 1 странице.
					array_push($sticky_threads,
						array('id' => $row['id'],
								'original_post' => $row['original_post'],
								'bump_limit' => $row['bump_limit'],
								'sticky' => $row['sticky'],
								'sage' => $row['sage'],
								'with_attachments' => $row['with_attachments'],
								'posts_count' => $row['posts_count']));
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
							'with_attachments' => $row['with_attachments'],
							'posts_count' => $row['posts_count']));
				$received++;
			}
		}
	if($page == 1)
		$threads = array_merge($sticky_threads, $threads);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $threads;
}
/**
 * Получает заданную нить, доступную для просмотра заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
 * @param thread_num mixed <p>Номер нити.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
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
function db_threads_get_visible_by_id($link, $board_id, $thread_num, $user_id)
{
	$result = mysqli_query($link, 'call sp_threads_get_visible_by_id('
		. $board_id . ', ' . $thread_num . ', ' . $user_id . ')');
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
					'sage' => $row['sage'],
					'sticky' => $row['sticky'],
					'with_attachments' => $row['with_attachments'],
					'archived' => $row['archived'],
					'posts_count' => $row['visible_posts_count']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $thread;
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
function db_threads_get_visible_count($link, $user_id, $board_id)
{
	$result = mysqli_query($link, 'call sp_threads_get_visible_count('
		. $user_id . ', ' . $board_id . ')');
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

/**********************************************
 * Работа с обработчиками загружаемых файлов. *
 **********************************************/

/**
 * Добавляет обработчик загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param name string <p>Имя фукнции обработчика загружаемых файлов.</p>
 */
function db_upload_handlers_add($link, $name)
{
    if(!mysqli_query($link, 'call sp_upload_handlers_add(\'' . $name . '\')'))
        throw new CommonException(mysqli_error($link));
    db_cleanup_link($link);
}
/**
 * Удаляет обработчик загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор обработчика загружаемых файлов.</p>
 */
function db_upload_handlers_delete($link, $id)
{
	if(!mysqli_query($link, 'call sp_upload_handlers_delete(' . $id . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все обработчики загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает обработчики загружаемых файлов:<p>
 * 'id' - Идентификатор.<br>
 * 'name' - Имя фукнции.</p>
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

/***************************************
 * Работа с типами загружаемых файлов. *
 ***************************************/

/**
 * Добавляет тип загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param extension string <p>Расширение.</p>
 * @param store_extension string <p>Сохраняемое расширение.</p>
 * @param is_image mixed <p>Флаг изображения.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемых
 * файлов.</p>
 * @param thumbnail_image string <p>Имя файла уменьшенной копии.</p>
 */
function db_upload_types_add($link, $extension, $store_extension, $is_image,
    $upload_handler_id, $thumbnail_image)
{
    $thumbnail_image = ($thumbnail_image === null
        ? 'null' : '\'' . $thumbnail_image . '\'');
    if(!mysqli_query($link, 'call sp_upload_types_add(\''
        . $extension . '\', \'' . $store_extension . '\', ' . $is_image . ', '
        . $upload_handler_id . ', ' . $thumbnail_image . ')'))
    {
        throw new CommonException(mysqli_error($link));
    }
    db_cleanup_link($link);
}
/**
 * Удаляет заданный тип загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор.</p>
 */
function db_upload_types_delete($link, $id)
{
	if(!mysqli_query($link, 'call sp_upload_types_delete(' . $id . ')'))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Редактирует заданный тип загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор.</p>
 * @param store_extension string <p>Сохраняемое расширение.</p>
 * @param is_image mixed <p>Флаг изображения.</p>
 * @param upload_handler_id mixed <p>Идентификатор обработчика загружаемых
 * файлов.</p>
 * @param thumbnail_image string <p>Имя файла уменьшенной копии.</p>
 */
function db_upload_types_edit($link, $id, $store_extension, $is_image,
	$upload_handler_id, $thumbnail_image)
{
	$thumbnail_image = ($thumbnail_image === null
		? 'null' : '\'' . $thumbnail_image . '\'');
	if(!mysqli_query($link, 'call sp_upload_types_edit(' . $id . ', \''
		. $store_extension . '\', ' . $is_image . ', '
		. $upload_handler_id . ', ' . $thumbnail_image . ')'))
	{
		throw new CommonException(mysqli_error($link));
	}
	db_cleanup_link($link);
}
/**
 * Получает все типы загружаемых файлов.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает типы загружаемых файлов:<p>
 * 'id' - Идентификатор.<br>
 * 'extension' - Расширение.<br>
 * 'store_extension' - Сохраняемое расширение.<br>
 * 'is_image' - Флаг изображения.<br>
 * 'upload_handler' - Идентификатор обработчика загружаемых файлов.<br>
 * 'thumbnail_image' - Имя файла уменьшенной копии.</p>
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
 * Получает типы загружаемых файлов, доступных для загрузки на заданной доске.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param board_id mixed <p>Идентификатор доски.</p>
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
function db_upload_types_get_by_board($link, $board_id)
{
	$result = mysqli_query($link, 'call sp_upload_types_get_by_board('
		. $board_id . ')');
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

/***********************************************
 * Работа со связями пользователей с группами. *
 ***********************************************/

/**
 * Добавляет пользователя в группу.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param group_id mixed <p>Идентификатор группы.</p>
 */
function db_user_groups_add($link, $user_id, $group_id)
{
    $result = mysqli_query($link, 'call sp_user_groups_add(' . $user_id . ', '
        . $group_id . ')');
    if(!$result)
        throw new CommonException(mysqli_error($link));
    db_cleanup_link($link);
}
/**
 * Удаляет заданного пользователя из заданной группы.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param group_id mixed <p>Идентификатор группы.</p>
 */
function db_user_groups_delete($link, $user_id, $group_id)
{
	$result = mysqli_query($link, 'call sp_user_groups_delete(' . $user_id
		. ', ' . $group_id . ')');
	if(!$result )
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Переносит заданного пользователя из одной группы в другую.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param user_id mixed <p>Идентификатор пользователя.</p>
 * @param old_group_id mixed <p>Идентификатор старой группы.</p>
 * @param new_group_id mixed <p>Идентификатор новой группы.</p>
 */
function db_user_groups_edit($link, $user_id, $old_group_id, $new_group_id)
{
	$result = mysqli_query($link, 'call sp_user_groups_edit(' . $user_id
		. ', ' . $old_group_id . ', ' . $new_group_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}
/**
 * Получает все связи пользователей с группами.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает связи пользователей с группами:<p>
 * 'user' - Идентификатор пользователя.<br>
 * 'group' - Идентификатор группы.</p>
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

/****************************
 * Работа с пользователями. *
 ****************************/

/**
 * Редактирует пользователя с заданным ключевым словом или добавляет нового.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @param posts_per_thread mixed <p>Число сообщений в нити на странице просмотра
 * доски.</p>
 * @param threads_per_page mixed <p>Число нитей на странице просмотра доски.</p>
 * @param lines_per_post mixed <p>Количество строк в предпросмотре
 * сообщения.</p>
 * @param language mixed <p>Идентификатор языка.</p>
 * @param stylesheet mixed <p>Идентификатор стиля.</p>
 * @param password mixed <p>Пароль для удаления сообщений.</p>
 * @param goto string <p>Перенаправление.</p>
 */
function db_users_edit_by_keyword($link, $keyword, $posts_per_thread,
    $threads_per_page, $lines_per_post, $language, $stylesheet, $password,
    $goto)
{
	$password = $password === null? 'null' : '\'' . $password . '\'';
	$goto = $goto === null? 'null' : '\'' . $goto . '\'';
	if(!mysqli_query($link, 'call sp_users_edit_by_keyword(\''
        . $keyword . '\', ' . $posts_per_thread . ', '
        . $threads_per_page . ', ' . $lines_per_post . ', ' . $language . ', '
        . $stylesheet . ', ' . $password . ', ' . $goto . ')'))
	{
		throw new CommonException(mysqli_error($link));
	}
	db_cleanup_link($link);
}
/**
 * Получает всех пользователей.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает пользователей:<p>
 * 'id' - Идентификатор.</p>
 */
function db_users_get_all($link)
{
	if(($result = mysqli_query($link, 'call sp_users_get_all()')) == false)
		throw new CommonException(mysqli_error($link));
	$users = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($users, array('id' => $row['id']));
	else
		throw new NodataException(NodataException::$messages['USERS_NOT_EXIST']);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $users;
}
/**
 * Получает пользователя с заданным ключевым словом.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param keyword string <p>Хеш ключевого слова.</p>
 * @return array
 * Возвращает пользователя:<p>
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
function db_users_get_by_keyword($link, $keyword)
{
	if(mysqli_multi_query($link,
        'call sp_users_get_by_keyword(\'' . $keyword . '\')') == false)
	{
		throw new CommonException(mysqli_error($link));
	}
// Пользователь.
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
		throw new PermissionException(PermissionException::$messages['USER_NOT_EXIST']);
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
		throw new NodataException(NodataException::$messages['USER_WITHOUT_GROUP']);
	}
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $user_settings;
}
/**
 * Устанавливает пароль для удаления сообщений заданному пользователю.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id mixed <p>Идентификатор пользователя.</p>
 * @param password mixed <p>Пароль для удаления сообщений.</p>
 */
function db_users_set_password($link, $id, $password)
{
	$password = ($password === null? 'null' : '\'' . $password . '\'');
	if(!mysqli_query($link,
        'call sp_users_set_password(' . $id . ', ' . $password . ')'))
	{
		throw new CommonException(mysqli_error($link));
	}
	db_cleanup_link($link);
}

/*****************************
 * Работа с вложенным видео. *
 *****************************/

/**
 * Получает видео, вложенные в заданное сообщение.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 */
function db_videos_get_by_post($link, $post_id)
{
	$result = mysqli_query($link,
		'call sp_videos_get_by_post(' . $post_id . ')');
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$videos = array();
	if(mysqli_affected_rows($link) > 0)
		while(($row = mysqli_fetch_assoc($result)) !== null)
			array_push($videos,
				array('id' => $row['id'],
						'code' => $row['code'],
						'widht' => $row['widht'],
						'height' => $row['height'],
						'attachment_type' => Config::ATTACHMENT_TYPE_VIDEO));
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $videos;
}

/***************************************************
 * Работа с закреплениями загрузок за сообщениями. *
 ***************************************************/

/**
 * Получает закрепления загрузок за заданными сообщениями.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param posts array <p>Сообщения.</p>
 * @return array
 * Возвращает закрепления:<p>
 * 'post' - идентификатор сообщения.<br>
 * 'upload' - идентификатор загрузки.</p>
 */
/*function db_posts_uploads_get_by_posts($link, $posts)
{
	$posts_uploads = array();
	foreach($posts as $p)
	{
		$result = mysqli_query($link,
			"call sp_posts_uploads_get_by_post({$p['id']})");
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
}*/
/**
 * Связывает сообщение с информацией о загрузке.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>идентификатор сообщения.</p>
 * @param upload_id mixed <p>идентификатор записи с информацией о загрузке.</p>
 */
/*function db_posts_uploads_add($link, $post_id, $upload_id)
{
	if(!mysqli_query($link, "call sp_posts_uploads_add($post_id, $upload_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}*/
/**
 * Удаляет закрепления загрузок за заданным сообщением.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param post_id mixed <p>Идентификатор сообщения.</p>
 */
/*function db_posts_uploads_delete_by_post($link, $post_id)
{
	if(!mysqli_query($link, "call sp_posts_uploads_delete_by_post($post_id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}*/

/************************
 * Работа с загрузками. *
 ************************/

/**
 * Сохраняет загрузку.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
/*function db_uploads_add($link, $hash, $is_image, $upload_type, $file, $image_w,
	$image_h, $size, $thumbnail, $thumbnail_w, $thumbnail_h)
{
	$is_image = $is_image ? '1' : '0';
	$hash = $hash ? "'$hash'" : 'null';
	$image_w = $image_w ? $image_w : 'null';
	$image_h = $image_h ? $image_h : 'null';
	$thumbnail = $thumbnail ? "'$thumbnail'" : 'null';
	$thumbnail_w = $thumbnail_w ? $thumbnail_w : 'null';
	$thumbnail_h = $thumbnail_h ? $thumbnail_h : 'null';
	$result = mysqli_query($link, "call sp_uploads_add($hash, $is_image,
		$upload_type, '$file', $image_w, $image_h, $size, $thumbnail,
		$thumbnail_w, $thumbnail_h)");
	if(!$result)
		throw new CommonException(mysqli_error($link));
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	db_cleanup_link($link);
	return $row['id'];
}*/
/**
 * Удаляет заданную загрузку.
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @param id string <p>Идентификатор загрузки.</p>
 */
/*function db_uploads_delete_by_id($link, $id)
{
	if(!mysqli_query($link, "call sp_uploads_delete_by_id($id)"))
		throw new CommonException(mysqli_error($link));
	db_cleanup_link($link);
}*/
/**
 * Получает загрузки для заданных сообщений.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
/*function db_uploads_get_by_posts($link, $posts)
{
	$uploads = array();
	foreach($posts as $p)
	{
		$result = mysqli_query($link, "call sp_uploads_get_by_post({$p['id']})");
		if(!$result)
			throw new CommonException(mysqli_error($link));
		if(mysqli_affected_rows($link) > 0)
			while(($row = mysqli_fetch_assoc($result)) != null)
				array_push($uploads,
					array('id' => $row['id'],
							'hash' => $row['hash'],
							'is_image' => $row['is_image'],
							'upload_type' => $row['upload_type'],
							'file' => $row['file'],
							'image_w' => $row['image_w'],
							'image_h' => $row['image_h'],
							'size' => $row['size'],
							'thumbnail' => $row['thumbnail'],
							'thumbnail_w' => $row['thumbnail_w'],
							'thumbnail_h' => $row['thumbnail_h']));
		mysqli_free_result($result);
		db_cleanup_link($link);
	}
	return $uploads;
}*/
/**
 * Получает информацию о висячих загрузках (не связанных с сообщениями).
 * @param link MySQLi <p>Связь с базой данных.</p>
 * @return array
 * Возвращает информация о висячих загрузках:<p>
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
/*function db_uploads_get_dangling($link)
{
	$result = mysqli_query($link, 'call sp_uploads_get_dangling()');
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
}*/
/**
 * Получает одинаковые загрузки для заданной доски.
 * @param link MySQLi <p>Связь с базой данных.</p>
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
/*function db_uploads_get_same($link, $board_id, $hash, $user_id)
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
						'upload_type' => $row['upload_type'],
						'file' => $row['file'],
						'image_w' => $row['image_w'],
						'image_h' => $row['image_h'],
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
}*/
?>