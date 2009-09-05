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
require_once '../config.php';
$debug = false;

/*
 * Измените пути к SQL скриптам на свои!
 */
$tables_path = 'c:\Apache\htdocs\kotoba\res\data.sql';
$data_path = 'c:\Apache\htdocs\kotoba\res\tables.sql';

echo 'Прежде чем начать установку, вы должны отредактировать конфигурационный файл. ' .
	'Сделайте копию файла config.default, назовите его config.php и отредактируйте. ' .
	'Если вы уже сделали это, то для начала установки введите "продолжить" без кавычек ' .
	'или что-либо другое, чтобы выйти из установки: ';
$stdin = fopen('php://stdin', 'r');
$option = fgets($stdin);

if($debug)
	echo $option;

if($option != "продолжить\r\n")
	exit;

echo "\nНачинается установка.\n\nВведите полный путь к mysql.exe. Например c:\MySQL5\bin: ";
$mysql_path = fgets($stdin);
$mysql_path = substr($mysql_path, 0, strlen($mysql_path) - strlen("\r\n"));

if($debug)
	echo $mysql_path;

echo "\nСоздание таблиц.\n";
$db_name = ($debug ? 'test' : KOTOBA_DB_BASENAME);
exec("$mysql_path\mysql.exe --database $db_name -u " . KOTOBA_DB_USER . (KOTOBA_DB_PASS == '' ? '' : '-p ' . KOTOBA_DB_PASS) . "< \"$tables_path\"");
echo "Добавление начальных данных.\n";
exec("$mysql_path\mysql.exe --database $db_name -u " . KOTOBA_DB_USER . (KOTOBA_DB_PASS == '' ? '' : '-p ' . KOTOBA_DB_PASS) . "< \"$data_path\"");
echo "Установка завершена.";
?>