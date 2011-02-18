{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of threads catalog page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $board - board.
    $ATTACHMENT_TYPE_FILE - attachment type is file (see config.default).
    $ATTACHMENT_TYPE_LINK - attachment type is link (see config.default).
    $ATTACHMENT_TYPE_VIDEO - attachment type is video (see config.default).
    $ATTACHMENT_TYPE_IMAGE - attachment type is image (see config.default).
    $posts - original posts of threads.
    $posts_attachments (optional) - posts attachments relations.
    $attachments (optional) - attachments.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Threads catalog /`$board.name`/ `$board.title`"}

<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>
<script src="{$DIR_PATH}/kotoba.js"></script>

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<br />
<br />
<div style="float: left;">
{section name=i loop=$posts}
<a href="{$DIR_PATH}/{$board.name}/{$posts[i].number}">
<table border="1" style="float: left; min-height: 150px; height: 150px; min-width: 150px; width: 150px;">
{if $board.with_attachments}
{section name=j loop=$posts_attachments}
{section name=k loop=$attachments}
{if $attachments[k].attachment_type == $ATTACHMENT_TYPE_FILE}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].file == $attachments[k].id}
    <tr><td rowspan="3"><img src="{if !$posts_attachments[j].deleted}{$DIR_PATH}/img/{$attachments[k].thumbnail}{else}{$DIR_PATH}/img/deleted.png{/if}" class="thumb" width="80"></td></tr>
{/if}
{elseif $attachments[k].attachment_type == $ATTACHMENT_TYPE_IMAGE}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].image == $attachments[k].id}
    <tr><td rowspan="3"><img src="{if !$posts_attachments[j].deleted}{$DIR_PATH}/{$board.name}/thumb/{$attachments[k].thumbnail}{else}{$DIR_PATH}/img/deleted.png{/if}" class="thumb" width="80"></td></tr>
{/if}
{elseif $attachments[k].attachment_type == $ATTACHMENT_TYPE_LINK}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].link == $attachments[k].id}
    <tr><td rowspan="3"><img src="{if !$posts_attachments[j].deleted}{$attachments[k].thumbnail}{else}{$DIR_PATH}/img/deleted.png{/if}" class="thumb" width="80"></td></tr>
{/if}
{elseif $attachments[k].attachment_type == $ATTACHMENT_TYPE_VIDEO}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].video == $attachments[k].id}
    <tr><td rowspan="3"><br><br>
    {if !$posts_attachments[j].deleted}{*include file='youtube.tpl' code=$attachments[k].code*}{else}{$DIR_PATH}/img/deleted.png{/if}
    </td></tr>
{/if}
{/if}
{/section}
{/section}
{else}
    <tr><td><span class="filetitle">{$posts[i].subject}</span></td></tr>
    <tr><td><span class="postername">{$posts[i].name}</span>{if $posts[i].tripcode != null}<span class="postertrip">!{$posts[i].tripcode}</span>{/if}</td></tr>
    <tr><td>{$posts[i].date_time}</td></tr>
    {/if}
</table>
</a>
{/section}
</div>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="footer" style="clear: both;">- <a href="http://code.google.com/p/kotoba-ib/" target="_top">Kotoba 1.2</a> -</div>
{include file='footer.tpl'}