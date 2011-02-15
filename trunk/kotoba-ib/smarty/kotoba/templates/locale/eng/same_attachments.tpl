{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of same attachments page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $board - board.
    $same_attachments - same attachments.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Same files'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<p>File was uploaded before:</p>
{section name=i loop=$same_attachments}
{if $same_attachments[i].view}<a href="{$DIR_PATH}/{$board.name}/{$same_attachments[i].post.thread.number}#{$same_uploads[i].post.number}">&gt;&gt;{$same_uploads[i].post.number}</a><br>{/if}
{/section}
{include file='footer.tpl'}