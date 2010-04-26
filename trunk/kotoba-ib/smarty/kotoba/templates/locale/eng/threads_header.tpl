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
Код начала страницы просмотра нити.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления (см. config.default).
	$board - доска, на которой расположена просматриваемая нить.
	$boards - доски.
	$thread - просматриваемая нить.
	$is_moderatable - текущая нить доступна для модерирования.
	$is_admin - флаг администратора.
	$rempass - пароль на удаление сообщений и нитей.
	$upload_types - типы файлов, доступные для загрузки.
	$goto - переход к нити или доске.

Специальные переменные (не входит в котобу):
	$event_daynight_active - запущен ли эвент времени суток.
	$event_daynight_code - код, добавляемый к html коду страницы, эвентом.
*}
{include file='header.tpl' page_title="Просмотр нити `$thread[0].original_post`" DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
{* Начало кода эвента времени суток (не входит в котобу). *}
{if isset($event_daynight_active) && $event_daynight_active}{$event_daynight_code}{/if}
{* Конец кода эвента времени суток. *}

<div class="navbar">{include file='board_list.tpl' boards=$boards DIR_PATH=$DIR_PATH} [<a href="{$DIR_PATH}/">Главная</a>]</div>

<div class="logo">✿Kotoba — /{$board.name}/{$thread[0].original_post}</div>
<hr>

<form name="reply_form" action="{$DIR_PATH}/reply.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1560576">
<table align="center" border="0">
<tbody>
{if !$board.force_anonymous}
<tr valign="top"><td class="postblock">Имя: </td><td><input type="text" name="name" size="30"></td></tr>
{/if}
<tr valign="top"><td class="postblock">Тема: </td><td><input type="text" name="subject" size="56"> <input type="submit" value="Ответить"></td></tr>
<tr valign="top"><td class="postblock">Сообщение: </td><td><textarea name="text" rows="7" cols="50"></textarea></td></tr>
{if $thread.with_files || ($thread.with_files === null && $board.with_files)}
<tr valign="top"><td class="postblock">Файл: </td><td><input type="file" name="file" size="54"></td></tr>
<tr valign="top"><td class="postblock">Макрос: </td>
<td>
	<select name="macrochan_tag">
	<option value="" selected></option>
	{section name=i loop=$macrochan_tags}
		<option value="{$macrochan_tags[i]}">{$macrochan_tags[i]}</option>
	{/section}
	</select>
</td>
</tr>
{/if}
{if !$is_admin}<tr valign="top"><td class="postblock">Капча: </td><td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/securimage/securimage_show.php?' + Math.random(); return false"><img id="captcha" src="{$DIR_PATH}/securimage/securimage_show.php" alt="CAPTCHA Image" /></a> <input type="text" name="captcha_code" size="10" maxlength="6" /></tr>{/if}
<tr valign="top"><td class="postblock">Пароль: </td><td><input type="password" name="message_pass" size="30" value="{$rempass}"></td></tr>
<tr valign="top"><td class="postblock">Перейти: </td><td>(нить: <input type="radio" name="goto" value="t"{if $goto == 't'} checked{/if}>) (доска: <input type="radio" name="goto" value="b"{if $goto == 'b'} checked{/if}>)</td></tr>
<tr valign="top"><td class="postblock">Sage: </td><td><input type="checkbox" name="sage" value="sage"></td></tr>

<tr valign="top"><td colspan = "2" class="rules">
<ul style="margin-left: 10pt; margin-top: 0pt; margin-bottom: 0pt; padding-left: 0pt;">
<li>Типы файлов, доступных для загрузки: {section name=i loop=$upload_types} {$upload_types[i].extension}{/section}</li>
<li>Бамплимит доски: {$board.bump_limit}</li>
<li>Бамплимит нити: {$thread[0].bump_limit}</li>
<li>Число сообщений: {$thread[0].posts_count}</li>
</ul>
{$board.annotation}
</td></tr>
</tbody>
</table>
<input type="hidden" name="t" value="{$thread[0].id}">
</form>
{if $is_moderatable}{include file='threads_settings_list.tpl' boards=$boards threads=$thread DIR_PATH=$DIR_PATH}{/if}
<hr>