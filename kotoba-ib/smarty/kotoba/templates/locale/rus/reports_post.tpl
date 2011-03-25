{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of post on report handing page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $ATTACHMENT_TYPE_FILE - attachment type is file (see config.default).
    $ATTACHMENT_TYPE_LINK - attachment type is link (see config.default).
    $ATTACHMENT_TYPE_VIDEO - attachment type is video (see config.default).
    $ATTACHMENT_TYPE_IMAGE - attachment type is image (see config.default).
    $post - post.
    $author_admin - autor of message admin.
    $attachments - attachments.
    $enable_translation - Translation flag. (see config.default).
*}
<td><input type="checkbox" name="marked[]" value="{$post.id}"></td>
<td colspan="3" class="reply"{if isset($post.deleted) && $post.deleted} style="background-color:pink"{/if}>
    <a name="{$post.number}"></a>
    <span class="filetitle">{$post.subject}</span>
    <span class="postername">{$post.name}</span>
    {if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{else}<!-- There is no tripcode -->{/if} {if $author_admin}<span class="admin">❀❀&nbsp;Админ&nbsp;❀❀</span>{else}<!-- Author is not admin -->{/if}

    {$post.date_time}
    <span class="reflink">
        <a href="{$DIR_PATH}/{$post.board.name}/{$post.thread.original_post}#{$post.number}">#</a>
        <a href="{$DIR_PATH}/threads.php?board={$post.board.name}&thread={$post.thread.original_post}&quote={$post.number}">{$post.number}</a>
    </span>
    {$post.ip}
    <br>
{if $post.with_attachments}
{if $attachments[0].deleted}
    <br>
    <a target="_blank" href="{$DIR_PATH}/img/deleted.png">
        <img src="{$DIR_PATH}/img/deleted.png" class="thumb" width="200" height="200">
    </a>
{else}
{if $attachments[0].attachment_type == $ATTACHMENT_TYPE_FILE}
    <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].file_link}">{$attachments[0].name}</a>-({$attachments[0].size} Байт)</span>
    <br>
    <a target="_blank" href="{$attachments[0].file_link}">
        <img src="{$attachments[0].thumbnail_link}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}">
    </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_IMAGE}
    <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].image_link}">{$attachments[0].name}</a>-({$attachments[0].size} Байт, {$attachments[0].widht}x{$attachments[0].height})</span>
    <br>
    <a target="_blank" href="{$attachments[0].image_link}">
        <img src="{if $attachments[0].spoiler}{$DIR_PATH}/img/spoiler.png{else}{$attachments[0].thumbnail_link}{/if}" class="thumb"{if !$attachments[0].spoiler} width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}{/if}">
    </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_LINK}
    <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].url}">{$attachments[0].url}</a>-({$attachments[0].size} Байт, {$attachments[0].widht}x{$attachments[0].height})</span>
    <br>
    <a target="_blank" href="{$attachments[0].url}">
        <img src="{$attachments[0].thumbnail}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}">
    </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_VIDEO}
    <br>
    <br>{$attachments[0].video_link}
{else}
    <!-- Unknown attachment type :o -->
{/if}
{/if}
{else}
    <!-- There is no attachments -->
{/if}
    <blockquote id="post{$post.number}">
{$post.text}
    </blockquote>
    {if $enable_translation && $post.text}<blockquote id="translation{$post.number}"></blockquote><a href="#" onclick="javascript:translate('{$post.number}'); return false;">Lolšto?</a>{else}<!-- Translation disabled or empty message -->{/if}

</td>