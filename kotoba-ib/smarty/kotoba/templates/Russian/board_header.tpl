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
Код заголовка страницы просмотра доски.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления (см. config.default).
	$boards - доски.
	$rempass - пароль на удаление сообщений и нитей.
	$board_name - имя просматриваемой доски.
	$board_id - идентификатор просматриваемой доски.
	$board_title - заголовок просматриваемой доски.
	$upload_types - типы файлов, доступных для загрузки на просматриваемой доске.
	$is_guest - флаг гостя.
	$bump_limit - бамплимит доски.
	$pages - номера страниц.
	$page - номер просматриваемой страницы.
	$with_files - флаг загрузки файлов.
	$force_anonymous - флаг отображения имени отправителя.
	$annotation - аннотация.
	$goto - переход к нити или доске.

Специальные переменные (не входит в котобу):
	$event_daynight_active - запущен ли эвент времени суток.
	$event_daynight_code - код, добавляемый к html коду страницы, эвентом.
*}
{assign var="page_title" value="Просмотр доски $board_name страница $page"}
{include file='header.tpl' page_title=$page_title DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
{* Начало кода эвента времени суток (не входит в котобу). *}
{if isset($event_daynight_active) && $event_daynight_active}{$event_daynight_code}{/if}
{* Конец кода эвента времени суток. *}
Список досок: {include file='board_list.tpl' board_list=$boards DIR_PATH=$DIR_PATH}<br>
<a href="{$DIR_PATH}/edit_settings.php"{if $is_guest} title="Отредактируйте ваши настройки."{/if}>Мои настройки</a><br>

<h4 align=center>✿Kotoba</h4>
<center><b>/{$board_name}/ {$board_title}</b></center><br><br>
Бамплимит доски: {$bump_limit}<br>
{include file='pages_list.tpl' board_name=$board_name pages=$pages page=$page}
<hr>

<form action="{$DIR_PATH}/create_thread.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1560576">
<table align="center" border="0">
{if !$force_anonymous}<tr valign="top"><td>Имя: </td><td><input type="text" name="message_name" size="30"></td></tr>{/if}
<tr valign="top"><td>Тема: </td><td><input type="text" name="message_theme" size="48"> <input type="submit" value="Создать нить"></td></tr>
<tr valign="top"><td>Сообщение: </td><td><textarea name="message_text" rows="7" cols="50"></textarea></td></tr>
{if $with_files}<tr valign="top"><td>Файл: </td><td><input type="file" name="file" size="54"></td></tr>{/if}
<tr valign="top"><td>Пароль: </td><td><input type="password" name="message_pass" size="30" value="{$rempass}"></td></tr>
<tr valign="top"><td>Перейти: </td><td>(нить: <input type="radio" name="goto" value="t"{if $goto == 't'} checked{/if}>) (доска: <input type="radio" name="goto" value="b"{if $goto == 'b'} checked{/if}>)</td></tr>
<tr valign="top"><td>Капча: </td><td><img id="captcha" src="{$DIR_PATH}/securimage/securimage_show.php" alt="CAPTCHA Image" /> <input type="text" name="captcha_code" size="10" maxlength="6" /></tr>
<tr valign="top"><td colspan = "2">Типы файлов, доступных для загрузки:{section name=i loop=$upload_types} {$upload_types[i].extension}{/section}</td></tr>
<tr valign="top"><td colspan = "2">{$annotation}</td></tr>
</table>
<input type="hidden" name="board" value="{$board_id}">
</form>
<hr>
