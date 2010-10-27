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
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
*}
<ul>
<li><a href="{$DIR_PATH}/admin/moderate.php">Основной скрипт модератора.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_bans.php">Баны.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_threads.php">Нити.</a></li>
</ul>