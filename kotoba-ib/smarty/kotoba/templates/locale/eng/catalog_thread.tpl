{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of catalog entry.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $ATTACHMENT_TYPE_FILE - attachment type is file (see config.default).
    $ATTACHMENT_TYPE_LINK - attachment type is link (see config.default).
    $ATTACHMENT_TYPE_VIDEO - attachment type is video (see config.default).
    $ATTACHMENT_TYPE_IMAGE - attachment type is image (see config.default).
    $post -
    $attachments - 
*}

<a href="{$DIR_PATH}/{$post.board.name}/{$post.number}">
<table border="1" style="float: left; min-height: 150px; height: 150px; min-width: 150px; width: 150px;">
{if $post.board.with_attachments && count($attachments) > 0}
{if $attachments[0].deleted}
<tr>
    <td rowspan="3">
        <img src="{$DIR_PATH}/img/deleted.png" alt="deleted" class="thumb" width="80">
    </td>
</tr>
{else}
{if $attachments[0].attachment_type == $ATTACHMENT_TYPE_FILE}
<tr>
    <td rowspan="3">
        <img src="{$DIR_PATH}/img/{$attachments[0].thumbnail}" alt="{$post.number}" class="thumb" width="80">
    </td>
</tr>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_IMAGE}
<tr>
    <td rowspan="3">
        <img src="{$DIR_PATH}/{$post.board.name}/thumb/{$attachments[0].thumbnail}" alt="{$post.number}" class="thumb" width="80">
    </td>
</tr>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_LINK}
<tr>
    <td rowspan="3">
        <img src="{$attachments[0].thumbnail}" alt="{$post.number}" class="thumb" width="80">
    </td>
</tr>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_VIDEO}
<tr>
    <td rowspan="3">
        <br>
        <br>
        {*include file='youtube.tpl' code=$attachments[0].code*}YouTube video.
    </td>
</tr>
{/if}{* $attachments[0].attachment_type == ... *}
{/if}{* $attachments[0].deleted *}
{else}
<tr>
    <td>
        <span class="filetitle">{$post.subject}</span>
    </td>
</tr>
<tr>
    <td>
        <span class="postername">{$post.name}</span>{if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{/if}
    </td>
</tr>
<tr>
    <td>{$post.date_time}</td>
</tr>
{/if}{* $post.board.with_attachments *}
</table>
</a>