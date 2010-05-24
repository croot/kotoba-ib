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
Код начала страницы просмотра доски.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления (см. config.default).
	$ib_name - название имейджборды  (см. config.default).
	$enable_macro - Включение интеграции с макрочаном (см. config.default).
	$enable_youtube - Включение постинга видео с ютуба (см. config.default).
	$board - просматриваемая доска.
	$boards - доски.
	$is_admin - флаг администратора.
	$password - пароль для удаления сообщений.
	$upload_types - типы файлов, доступных для загрузки на просматриваемой доске.
	$pages - номера страниц.
	$page - номер просматриваемой страницы.
	$goto - переход к нити или доске.
	$macrochan_tags - теги макросов.

Специальные переменные (не входит в котобу):
	$event_daynight_active - запущен ли эвент времени суток.
	$event_daynight_code - код, добавляемый к html коду страницы, эвентом.
*}
{include file='header.tpl' page_title="`$ib_name` — /`$board.name`/ `$board.title`. Просмотр, страница $page" DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
{* Начало кода эвента времени суток (не входит в котобу). *}
{if isset($event_daynight_active) && $event_daynight_active}{$event_daynight_code}{/if}
{* Конец кода эвента времени суток. *}
<script src="{$DIR_PATH}/kotoba.js"></script>
<div class="navbar">{include file='board_list.tpl' boards=$boards DIR_PATH=$DIR_PATH} [<a href="{$DIR_PATH}/">Главная</a>]</div>

<div class="logo">{$ib_name} — /{$board.name}/ {$board.title}</div>
<div class="search">
<form name="searchform" id="searchform" action="{$DIR_PATH}/search.php" method="post">
<input type="text" name="search">&nbsp;<input type="submit" value="Искать">
<input type="hidden" name="board" value="{$board.name}">
</form>
</div>
{include file='search_pages_list.tpl' pages=$pages page=$page}
<hr>
