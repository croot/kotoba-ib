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
Код обычного сообщения.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$board - просматриваемая доска.
    $thread - нить.

	$simple_post - сообщение.
	$simple_uploads - файлы, прикрепленные к сообщению.
*}
    <table>
	<tbody>
    <tr>
		<td class="doubledash">
			&gt;&gt;
		</td>
        <td class="reply">
			<a href="{$DIR_PATH}/remove_post.php?post={$simple_post.id}&submit=1"><img src="{$DIR_PATH}/css/delete.png" alt="[Удалить]" title="Удалить сообщение" border="0"/></a>
			{if $simple_post.with_files}
				<a href="{$DIR_PATH}/remove_upload.php?post={$simple_post.id}&submit=1"><img src="{$DIR_PATH}/css/delfile.png" alt="[Удалить файл]" title="Удалить файл" border="0"/></a>
			{/if}
			<a href="{$DIR_PATH}/report.php?post={$simple_post.id}&submit=1"><img src="{$DIR_PATH}/css/report.png" alt="[Пожаловаться]" title="Пожаловаться на сообщение" border="0"/></a>
            <span class="filetitle">{$simple_post.subject}</span> <span class="postername">{$simple_post.name}</span>{if $simple_post.tripcode != null}<span class="postertrip">!{$simple_post.tripcode}</span>{/if} {$simple_post.date_time}
            <span class="reflink"><span onclick="insert('>>{$simple_post.number}');">#</span> <a href="{$DIR_PATH}/{$board.name}/{$thread[0].original_post}#{$simple_post.number}">{$simple_post.number}</a></span>
            {if $is_admin}{include file='mod_mini_panel.tpl' post_id=$simple_post.id ip=$simple_post.ip board_name=$board.name post_num=$simple_post.number}{/if}
            <a name="{$simple_post.number}"></a><br/>
{if $simple_post.with_files and !$simple_uploads[0].is_embed}
				<span class="filesize">Файл: <a target="_blank" href="{$simple_uploads[0].file_link}">{$simple_uploads[0].file_name}</a> -(<em>{$simple_uploads[0].size} Байт {$simple_uploads[0].image_w}x{$simple_uploads[0].image_h}</em>)</span>
			{/if}
			{if $simple_post.with_files}
				{if $simple_uploads[0].is_embed}
					<br><br>{$simple_uploads[0].file_link}
				{else}
					<br><a target="_blank" href="{$simple_uploads[0].file_link}"><img src="{$simple_uploads[0].file_thumbnail_link}" class="thumb" width="{$simple_uploads[0].thumbnail_w}" height="{$simple_uploads[0].thumbnail_h}"></a>
				{/if}
			{/if}
            <blockquote>
            {$simple_post.text}
            </blockquote>
			{if $simple_post.text_cutted == 1}<br><span class="omittedposts">Нажмите "Ответ" для просмотра сообщения целиком.</span>{/if}
        </td>
    </tr>
	</tbody>
    </table>