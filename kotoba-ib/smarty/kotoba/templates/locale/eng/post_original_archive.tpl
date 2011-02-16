{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of original message.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $ATTACHMENT_TYPE_FILE - attachment type is file (see config.default).
    $ATTACHMENT_TYPE_LINK - attachment type is link (see config.default).
    $ATTACHMENT_TYPE_VIDEO - attachment type is video (see config.default).
    $ATTACHMENT_TYPE_IMAGE - attachment type is image (see config.default).
    $post - Original post.
    $attachments - Attachments.
    $author_admin - Author of this post is admin.
*}
{if $post.with_attachments}
{if $attachments[0].deleted}
                <br>
                <a target="_blank" href="{$DIR_PATH}/img/deleted.png">
                    <img src="{$DIR_PATH}/img/deleted.png" class="thumb" width="200" height="200">
                </a>
{else}
{if $attachments[0].attachment_type == $ATTACHMENT_TYPE_FILE}
    <span class="filesize">File: <a target="_blank" href="{$attachments[0].file_link}">{$attachments[0].name}</a>-({$attachments[0].size} Bytes)</span>
    <br>
    <a target="_blank" href="{$attachments[0].file_link}">
        <img src="{$attachments[0].thumbnail_link}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}">
    </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_IMAGE}
    <span class="filesize">File: <a target="_blank" href="{$attachments[0].image_link}">{$attachments[0].name}</a>-({$attachments[0].size} Bytes, {$attachments[0].widht}x{$attachments[0].height})</span>
    <br>
    <a target="_blank" href="{$attachments[0].image_link}">
        <img src="{if $attachments[0].spoiler}{$DIR_PATH}/img/spoiler.png{else}{$attachments[0].thumbnail_link}{/if}" class="thumb"{if !$attachments[0].spoiler} width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}{/if}">
    </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_LINK}
    <span class="filesize">File: <a target="_blank" href="{$attachments[0].url}">{$attachments[0].url}</a>-({$attachments[0].size} Bytes, {$attachments[0].widht}x{$attachments[0].height})</span>
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
    <a name="{$post.number}"></a>
    <span class="filetitle">{$post.subject}</span>
    <span class="postername">{$post.name}</span>
    {if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{else}<!-- There is no tripcode -->{/if} {if $author_admin}<span class="admin">❀❀&nbsp;Админ&nbsp;❀❀</span>{else}<!-- Author is not admin -->{/if}

    {$post.date_time}
    <span class="reflink">
        <a href="{$DIR_PATH}/{$post.board.name}/arch/{$post.thread.original_post}.html#{$post.number}">#</a>
        {$post.number}

    </span>
    <blockquote id="post{$post.thread.original_post}">
{$post.text}
    </blockquote>