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
/*
 * en: database connect module with mysqli interface
 * ru: Скрипт соединения с базой данных при помощи библиотеки mysqli
 */

require_once 'config.php';
require_once 'exception_processing.php';

/*
 * en: Connect to database
 * return database link
 * no arguments
 * ru: Устанавливает соединение с сервером баз данных и возвращает
 * соединение (объект, представляющий соединение с бд).
 */

function dbconnect() {
	$link = @mysqli_connect(KOTOBA_DB_HOST, KOTOBA_DB_USER, KOTOBA_DB_PASS, KOTOBA_DB_BASENAME);
    if(!$link)
		kotoba_error(mysqli_connect_error());
	// TODO: charset should be configurable
	if(!mysqli_set_charset($link, 'utf8'))
		kotoba_error(mysqli_error($link));
	return $link;
}
?>
