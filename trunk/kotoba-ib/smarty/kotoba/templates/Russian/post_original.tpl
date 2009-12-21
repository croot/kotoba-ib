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
	$board - просматриваемая доска.
    $thread - нить.
	$is_admin - флаг администратора.

	$original_post - оригинальное сообщение.
	$original_uploads - файлы, прикрепленные к оригинальному сообщению.
	$sticky - флаг закрепления.
*}
<div>
{if $original_post.with_files && !$original_uploads[0].is_embed}
	<span class="filesize">Файл: <a target="_blank" href="{$original_uploads[0].file_link}">{$original_uploads[0].file_name}</a> -(<em>{$original_uploads[0].size} Байт {$original_uploads[0].file_w}x{$original_uploads[0].file_h}</em>)</span>
	<br><a target="_blank" href="{$original_uploads[0].file_link}"><img src="{$original_uploads[0].file_thumbnail_link}" class="thumb" width="{$original_uploads[0].thumbnail_w}" height="{$original_uploads[0].thumbnail_h}"></a>
{/if}
<span class="filetitle">{$original_post.subject}</span> <span class="postername">{$original_post.name}</span>{if $original_post.tripcode != null}<span class="postertrip">!{$original_post.tripcode}</span>{/if} {$original_post.date_time}
<span class="reflink">
	<span onclick="insert('>>{$original_post.number}');">#</span>
	<a href="{$DIR_PATH}/{$board.name}/{$thread[0].original_post}#{$original_post.number}">{$original_post.number}</a>
</span>
<span class="hidebtn">[<a href="{$DIR_PATH}/{$board.name}/h{$thread[0].original_post}" title="Скрыть">-</a>]</span>
<span class="delbtn">[<a href="{$DIR_PATH}/{$board.name}/r{$original_post.number}" title="Удалить">×</a>]</span>
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
<br><br>
