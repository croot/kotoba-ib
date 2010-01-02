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
Код оригинального сообщения в просмотре доски.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$board - просматриваемая доска.
    $thread - нить.
	$is_admin - флаг администратора.

	$original_post - оригинальное сообщение.
	$original_uploads - файлы, прикрепленные к оригинальному сообщению.
	$sticky - флаг закрепления.
	$skipped - количество не показанных сообщений.
*}
<div>
{if $original_post.with_files && !$original_uploads[0].is_embed}
	<span class="filesize">Файл: <a target="_blank" href="{$original_uploads[0].file_link}">{$original_uploads[0].file_name}</a> -(<em>{$original_uploads[0].size} Байт {$original_uploads[0].image_w}x{$original_uploads[0].image_h}</em>)</span>
	<br><a target="_blank" href="{$original_uploads[0].file_link}"><img src="{$original_uploads[0].file_thumbnail_link}" class="thumb" width="{$original_uploads[0].thumbnail_w}" height="{$original_uploads[0].thumbnail_h}"></a>
{/if}
<a href="{$DIR_PATH}/hide_thread.php?thread={$thread.id}&submit=1"><img src="{$DIR_PATH}/css/hide.png" alt="[Скрыть]" title="Скрыть нить" border="0"/></a>
<a href="{$DIR_PATH}/remove_post.php?post={$original_post.id}&submit=1"><img src="{$DIR_PATH}/css/delete.png" alt="[Удалить]" title="Удалить нить" border="0"/></a>
{if $original_post.with_files}
	<a href="{$DIR_PATH}/remove_upload.php?post={$original_post.id}&submit=1"><img src="{$DIR_PATH}/css/delfile.png" alt="[Удалить файл]" title="Удалить файл" border="0"/></a>
{/if}
<a href="{$DIR_PATH}/report.php?post={$original_post.id}&submit=1"><img src="{$DIR_PATH}/css/report.png" alt="[Пожаловаться]" title="Пожаловаться на сообщение" border="0"/></a>
<span class="filetitle">{$original_post.subject}</span> <span class="postername">{$original_post.name}</span>{if $original_post.tripcode != null}<span class="postertrip">!{$original_post.tripcode}</span>{/if} {$original_post.date_time}
<span class="reflink">
	<span onclick="insert('>>{$original_post.number}');">#</span>
	<a href="{$DIR_PATH}/{$board.name}/{$thread.original_post}#{$original_post.number}">{$original_post.number}</a>
	[<a href="{$DIR_PATH}/{$board.name}/{$thread.original_post}">Ответить</a>]
</span>
{if $sticky} Нить закреплена.{/if}
{if $is_admin}{include file='mod_mini_panel.tpl' post_id=$original_post.id ip=$original_post.ip board_name=$board.name post_num=$original_post.number}{/if}
<a name="{$original_post.number}"></a>
{if $original_post.with_files && $original_uploads[0].is_embed}
	<br><br>{$original_uploads[0].file_link}
{/if}
<br>
<blockquote>
{$original_post.text}
</blockquote>
{if $original_post.text_cutted == 1}<br><span class="omittedposts">Нажмите "Ответ" для просмотра сообщения целиком.</span>{/if}
{if $skipped > 0}<br><span class="omittedposts">Сообщений пропущено: {$skipped}</span>
<br><br>{/if}
