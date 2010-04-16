{* Smarty *}
{*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************
 *********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Панель модератора.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php
		(см. config.default).
*}
<a href="{$DIR_PATH}/admin/moderate.php">Основной скрипт модератора.</a><br>
<a href="{$DIR_PATH}/admin/edit_bans.php">Редактирование банов</a><br>
<a href="{$DIR_PATH}/admin/edit_threads.php">Редактирование настроек нитей</a>