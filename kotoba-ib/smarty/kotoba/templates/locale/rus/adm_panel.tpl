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
Код панели администратора.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php
		(см. config.default).
*}
<a href="{$DIR_PATH}/admin/edit_boards_annotation.php">Аннотации досок.</a><br>
<a href="{$DIR_PATH}/admin/edit_bans.php">Баны.</a><br>
<a href="{$DIR_PATH}/admin/edit_words.php">Фильтрация слов.</a><br>
<a href="{$DIR_PATH}/admin/edit_groups.php">Группы пользователей.</a><br>
<a href="{$DIR_PATH}/admin/edit_boards.php">Доски.</a><br>
<a href="{$DIR_PATH}/admin/edit_user_groups.php">Закрепления пользователей за группами.</a><br>
<a href="{$DIR_PATH}/admin/edit_categories.php">Категории досок.</a><br>
<a href="{$DIR_PATH}/admin/edit_threads.php">Настройки нитей.</a><br>
<a href="{$DIR_PATH}/admin/edit_upload_handlers.php">Обработчики загружаемых файлов.</a><br>
<a href="{$DIR_PATH}/admin/edit_popdown_handlers.php">Обработчики удаления нитей.</a><br>
<a href="{$DIR_PATH}/admin/edit_acl.php">Список контроля доступа.</a><br>
<a href="{$DIR_PATH}/admin/edit_stylesheets.php">Стили оформления.</a><br>
<a href="{$DIR_PATH}/admin/edit_upload_types.php">Типы загружаемых файлов.</a><br>
<a href="{$DIR_PATH}/admin/edit_board_upload_types.php">Типов загружаемых файлов для досок.</a><br>
<a href="{$DIR_PATH}/admin/edit_languages.php">Языки.</a><br><br>

<a href="{$DIR_PATH}/admin/moderate.php">Основной скрипт модератора.</a><br>
<a href="{$DIR_PATH}/admin/archive.php">Произвести архивирование.</a><br>
<a href="{$DIR_PATH}/admin/delete_marked_posts.php">Удалить помеченные на удаление сообщения и нити.</a><br>
<a href="{$DIR_PATH}/admin/delete_dangling_files.php">Удалить висячие загрузки.</a>
<br><br><a href="{$DIR_PATH}/">На главную</a>