{* Smarty *}
{*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.		   *
 *************************************
 *********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Код оригинального сообщения в просмотре нити.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$ATTACHMENT_TYPE_FILE - тип вложения файл (см. config.default).
	$ATTACHMENT_TYPE_LINK - тип вложения ссылка (см. config.default).
	$ATTACHMENT_TYPE_VIDEO - тип вложения видео (см. config.default).
	$ATTACHMENT_TYPE_IMAGE - тип вложения изображение (см. config.default).
	$board - просматриваемая доска.
    $thread - нить.
	$is_admin - флаг администратора.

	$original_post - оригинальное сообщение.
	$original_attachments - вложения.
	$sticky - флаг закрепления.
*}
<div>
{if $original_post.with_attachments}
	{if $original_attachments[0].attachment_type == $ATTACHMENT_TYPE_FILE}
		<span class="filesize">Файл: <a target="_blank" href="{$original_attachments[0].file_link}">{$original_attachments[0].name}</a> -(<em>{$original_attachments[0].size} Байт</em>)</span>
		<br><a target="_blank" href="{$original_attachments[0].file_link}"><img src="{$original_attachments[0].thumbnail_link}" class="thumb" width="{$original_attachments[0].thumbnail_w}" height="{$original_attachments[0].thumbnail_h}"></a>
	{elseif $original_attachments[0].attachment_type == $ATTACHMENT_TYPE_IMAGE}
		<span class="filesize">Файл: <a target="_blank" href="{$original_attachments[0].image_link}">{$original_attachments[0].name}</a> -(<em>{$original_attachments[0].size} Байт {$original_attachments[0].widht}x{$original_attachments[0].height}</em>)</span>
		<br><a target="_blank" href="{$original_attachments[0].image_link}"><img src="{$original_attachments[0].thumbnail_link}" class="thumb" width="{$original_attachments[0].thumbnail_w}" height="{$original_attachments[0].thumbnail_h}"></a>
	{elseif $original_attachments[0].attachment_type == $ATTACHMENT_TYPE_LINK}
		<span class="filesize">Файл: <a target="_blank" href="{$original_attachments[0].url}">{$original_attachments[0].url}</a> -(<em>{$original_attachments[0].size} Байт {$original_attachments[0].widht}x{$original_attachments[0].height}</em>)</span>
		<br><a target="_blank" href="{$original_attachments[0].url}"><img src="{$original_attachments[0].thumbnail}" class="thumb" width="{$original_attachments[0].thumbnail_w}" height="{$original_attachments[0].thumbnail_h}"></a>
	{elseif $original_attachments[0].attachment_type == $ATTACHMENT_TYPE_VIDEO}
		<br><br>{$original_attachments[0].video_link}
	{/if}
{/if}
<a href="{$DIR_PATH}/hide_thread.php?thread={$thread[0].id}&submit=1&board_name={$board.name}"><img src="{$DIR_PATH}/css/hide.png" alt="[Скрыть]" title="Скрыть нить" border="0"/></a>
<a href="{$DIR_PATH}/remove_post.php?post={$original_post.id}&submit=1"><img src="{$DIR_PATH}/css/delete.png" alt="[Удалить]" title="Удалить нить" border="0"/></a>
{if $original_post.with_attachments}
	<a href="{$DIR_PATH}/remove_upload.php?post={$original_post.id}&submit=1"><img src="{$DIR_PATH}/css/delfile.png" alt="[Удалить файл]" title="Удалить файл" border="0"/></a>
{/if}
<a href="{$DIR_PATH}/report.php?post={$original_post.id}&submit=1"><img src="{$DIR_PATH}/css/report.png" alt="[Пожаловаться]" title="Пожаловаться на сообщение" border="0"/></a>
<span class="filetitle">{$original_post.subject}</span> <span class="postername">{$original_post.name}</span>{if $original_post.tripcode != null}<span class="postertrip">!{$original_post.tripcode}</span>{/if} {$original_post.date_time}
<span class="reflink">
	<span onclick="insert('>>{$original_post.number}');">#</span>
	<a href="{$DIR_PATH}/{$board.name}/{$thread[0].original_post}#{$original_post.number}">{$original_post.number}</a>
</span>
{if $sticky} Нить закреплена.{/if}
{if $is_admin}{include file='mod_mini_panel.tpl' post_id=$original_post.id ip=$original_post.ip board_name=$board.name post_num=$original_post.number}{/if}
<a name="{$original_post.number}"></a>
<br>
<blockquote>
{$original_post.text}
</blockquote>
<br><br>
