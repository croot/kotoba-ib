{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of administrator panel.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
*}
<ul>
<li><a href="{$DIR_PATH}/admin/moderate.php">Основной скрипт модератора.</a></li>
<li><a href="{$DIR_PATH}/admin/reports.php">Жалобы.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_bans.php">Баны.</a></li>
<li><a href="{$DIR_PATH}/admin/hard_ban.php">Бан в фаерволе.</a></li>
<li><a href="{$DIR_PATH}/admin/mass_ban.php">Бан по списку.</a></li>
</ul>
Доски:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_boards.php">Доски.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_categories.php">Категории.</a></li>
</ul>
Нити:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_threads.php">Нити.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_popdown_handlers.php">Обработчики автоматического удаления нитей.</a></li>
<li><a href="{$DIR_PATH}/admin/move_thread.php">Перенос нити.</a></li>
</ul>
Вложения:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_upload_types.php">Типы загружаемых файлов.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_board_upload_types.php">Закрепление типов загружаемых файлов за досками.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_upload_handlers.php">Обработчики загружаемых файлов.</a></li>
<li><a href="{$DIR_PATH}/admin/delete_dangling_attachments.php">Удаление висячих вложений.</a></li>
<li><a href="{$DIR_PATH}/admin/update_macrochan.php">Загрузка новых данных с Макрочана.</a></li>
</ul>
Пользователи и Группы:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_groups.php">Группы пользователей.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_user_groups.php">Закрепления пользователей за группами.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_acl.php">Список контроля доступа.</a></li>
</ul>
Удаление и архивирование:
<ul>
<li><a href="{$DIR_PATH}/admin/archive.php">Произвести архивирование.</a></li>
<li><a href="{$DIR_PATH}/admin/delete_marked.php">Удалить помеченные на удаление сообщения, нити, связи сообщений и вложений.</a></li>
</ul>
Разное:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_stylesheets.php">Стили.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_languages.php">Языки.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_words.php">Фильтрация слов.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_spamfilter.php">Спамфильтр.</a></li>
<li><a href="{$DIR_PATH}/admin/log_view.php">Просмотр лога.</a></li>
</ul>