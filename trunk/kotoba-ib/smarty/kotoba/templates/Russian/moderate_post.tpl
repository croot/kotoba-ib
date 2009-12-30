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
Код заголовка основной страницы модератора.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php
		(см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$post - сообщение.
	$uploads - информация о загрузках сообщения.
*}
	<tr>
		<td><input type="checkbox" name="mark_{$post.id}" value="1"></td>
		<td colspan="3" class="reply">
			<span class="filetitle">{$post.subject}</span> <span class="postername">{$post.name}</span>{if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{/if} {$post.date_time}
			{if $post.with_files && !$uploads[0].is_embed}
				<span class="filesize">Файл: <a target="_blank" href="{$uploads[0].file_link}">{$uploads[0].file_name}</a> -(<em>{$uploads[0].size} Байт {$uploads[0].file_w}x{$uploads[0].file_h}</em>)</span>
			{/if}
            <span class="reflink"># <a href="{$DIR_PATH}/{$post.board_name}/{$post.thread_number}#{$post.number}">{$post.number}</a></span>
			{if $post.with_files}
				{if $uploads[0].is_embed}
					<br><br>{$uploads[0].file_link}
				{else}
					<br><a target="_blank" href="{$uploads[0].file_link}"><img src="{$uploads[0].file_thumbnail_link}" class="thumb" width="{$uploads[0].thumbnail_w}" height="{$uploads[0].thumbnail_h}"></a>
				{/if}
			{/if}
            <blockquote>
            {$post.text}
            </blockquote>
		</td>
	</tr>