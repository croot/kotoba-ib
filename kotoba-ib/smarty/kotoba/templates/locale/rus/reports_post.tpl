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
Код сообщения на странице жалоб.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $post - сообщение.
    $attachments - вложения.
    $board - доска, на которой расположено сообщение.
*}
<tr>
<td><input type="checkbox" name="mark_{$post.id}" value="1"></td>
<td colspan="3" class="reply">
    <span class="filetitle">{$post.subject}</span> <span class="postername">{$post.name}</span>{if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{/if} {$post.date_time}
    <span class="reflink"><span onclick="insert('>>{$post.number}');">#</span> <a href="{$DIR_PATH}/{$board.name}/{$thread[0].original_post}#{$post.number}">{$post.number}</a></span>
    {* {include file='mod_mini_panel.tpl' post_id=$post.id ip=$post.ip board_name=$board.name post_num=$post.number} *}
    {$post.ip}
    <a name="{$post.number}"></a><br/>
    {if $post.with_attachments}
            {if $attachments[0].attachment_type == $ATTACHMENT_TYPE_FILE}
                    <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].file_link}">{$attachments[0].name}</a> -(<em>{$attachments[0].size} Байт</em>)</span>
                    <br><a target="_blank" href="{$attachments[0].file_link}"><img src="{$attachments[0].thumbnail_link}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}"></a>
            {elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_IMAGE}
                    <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].image_link}">{$attachments[0].name}</a> -(<em>{$attachments[0].size} Байт {$attachments[0].widht}x{$attachments[0].height}</em>)</span>
                    <br><a target="_blank" href="{$attachments[0].image_link}"><img src="{$attachments[0].thumbnail_link}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}"></a>
            {elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_LINK}
                    <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].url}">{$attachments[0].url}</a> -(<em>{$attachments[0].size} Байт {$attachments[0].widht}x{$attachments[0].height}</em>)</span>
                    <br><a target="_blank" href="{$attachments[0].url}"><img src="{$attachments[0].thumbnail}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}"></a>
            {elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_VIDEO}
                    <br><br>{$attachments[0].video_link}
            {/if}
    {/if}
    <blockquote>
        {$post.text}
    </blockquote>
</td>
</tr>