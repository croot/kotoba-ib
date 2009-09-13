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
Этот шаблон содержит код просмотра нити.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления.
	$boards - список досок.
	$rempass - пароль на удаление своих сообщений и нитей.
	$board_name - имя доски, на которой расположена нить.
    $original_post - номер оригинального сообщения.
	$thread_id - идентификатор нити.
	$bump_limit - индивидуальный бамплимит, если задан.
	$posts_count - число сообщений в нити.
	$upload_types - типы файлов, доступные для загрузки.
	$thread_mod - текущая нить, если она доступна для модерирования.
*}
{assign var="page_title" value="Просмотр нити $original_post"}
{include file='header.tpl' page_title=$page_title DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
Список досок: {include file='board_list.tpl' board_list=$boards DIR_PATH=$DIR_PATH}<br>
<a href="{$DIR_PATH}/edit_settings.php"{if $is_guest} title="Отредактируйте ваши настройки."{/if}>Мои настройки</a><br>

<h4 align=center>azuchan</h4>
<br><center><b>Kotoba - {$board_name}/{$original_post}</b></center>
{if $bump_limit > 0}Индивидуальный бамплимит: {$bump_limit}<br>{/if}
Число сообщений: {$posts_count}
<hr>

<form name="reply_form" action="{$DIR_PATH}/reply.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1560576">
<table align="center" border="0">
<tr valign="top"><td>Имя: </td><td><input type="text" name="message_name" size="30"></td></tr>
<tr valign="top"><td>Тема: </td><td><input type="text" name="message_theme" size="56"> <input type="submit" value="Ответить"></td></tr>
<tr valign="top"><td>Сообщение: </td><td><textarea name="message_text" rows="7" cols="50"></textarea></td></tr>
<tr valign="top"><td>Изображение: </td><td><input type="file" name="message_img" size="54"></td></tr>
<tr valign="top"><td>Пароль: </td><td><input type="password" name="message_pass" size="30" value="{$rempass}"></td></tr>
<tr valign="top"><td>Перейти к: </td><td>(нити: <input type="radio" name="goto" value="t" checked>) (доске: <input type="radio" name="goto" value="b">)</td></tr>
<tr valign="top"><td>Sage: </td><td><input type="checkbox" name="sage" value="sage"></td></tr>
<tr valign="top"><td colspan = "2">Типы файлов, доступных для загрзки:{section name=i loop=$upload_types} {$upload_types[i].extension}{/section}</td></tr>
</table>
<input type="hidden" name="t" value="{$thread_id}">
</form>
<hr>
{if count($thread_mod) > 0}{include file='threads_settings_list.tpl' boards=$boards threads=$thread_mod DIR_PATH=$DIR_PATH}{/if}