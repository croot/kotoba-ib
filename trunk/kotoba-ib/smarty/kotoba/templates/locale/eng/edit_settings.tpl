{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of user settings page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $categories - categories.
    $boards - boards.
    $sess - Session information.
    $settings - Current settings.
    $languages - Languages.
    $stylesheets - Stylesheets.
    $favorites - Favorite threads.
    $hidden_threads - Hidden threads.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Settings'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH categories=$categories boards=$boards}

<div class="logo">Settings</div>
<br>
{$sess.name}={$sess.id}<br>
Session expire in: {$sess.expire - intval(($sess.curtime - $settings.kotoba_session_start_time) / 60)}/{$sess.expire} minutes.<br>
<br/>
<form action="{$DIR_PATH}/edit_settings.php" method="post">
<i>Enter keyword to load your settings.</i><br/>
    <input type="text" name="keyword_load" size="32">
    <input type="submit" value="Load">
</form>
<form action="{$DIR_PATH}/edit_settings.php" method="post">
<h4>Board view settings:</h4>
    <table border="0">
        <tr valign="top"><td>Count of threads per page on board view: </td><td><input type="text" name="threads_per_page" size="10" value="{$settings.threads_per_page}"></td></tr>
        <tr valign="top"><td>Count of posts per thread on board view: </td><td><input type="text" name="posts_per_thread" size="10" value="{$settings.posts_per_thread}"></td></tr>
        <tr valign="top"><td>Count of lines per post on board view: </td><td><input type="text" name="lines_per_post" size="10" value="{$settings.lines_per_post}"></td></tr>
        <tr valign="top"><td>Redirection: </td><td>
            <select name="goto">
                <option value="t"{if $settings.goto == 't'} selected{/if}>To thread</option>
                <option value="b"{if $settings.goto == 'b'} selected{/if}>To board</option>
            </select>
        </td></tr>
    </table>
<h4>Other:</h4>
    <table border="0">
        <tr valign="top"><td>Language: </td><td><select name="language_id">{section name=j loop=$languages}<option value="{$languages[j].id}"{if $settings.language == $languages[j].code} selected{/if}>{$languages[j].code}</option>{/section}</select></td></tr>
        <tr valign="top"><td>Stylesheet: </td><td><select name="stylesheet_id">{section name=i loop=$stylesheets}<option value="{$stylesheets[i].id}"{if $STYLESHEET == $stylesheets[i].name} selected{/if}>{$stylesheets[i].name}</option>{/section}</select></td></tr>
    </table>
<i>Enter keyword to save your settings.<br/>
Further use this keyword to load your settings.</i><br/>
    <input type="text" name="keyword_save" size="32">
    <input type="submit" value="Save">
</form>
<h4>Favorites:</h4>
{if count($favorites) > 0}
{section name=i loop=$favorites}
<a href="{$DIR_PATH}/{$favorites[i].thread.board.name}/{$favorites[i].thread.original_post}/" title="Refer to go to the thread">/{$favorites[i].thread.board.name}/{$favorites[i].thread.original_post}/</a> <span class="filetitle">{$favorites[i].post.subject}</span> <span class="postername">{$favorites[i].post.name}</span> {if $favorites[i].unread > 0}<span style="color:red;">{$favorites[i].unread} new posts!</span>{else}0 new posts.{/if} <a href="{$DIR_PATH}/favorites.php?action=delete&thread={$favorites[i].thread.id}" title="Remove from favorites">[X]</a><br/>
{/section}
<br/><a href="{$DIR_PATH}/favorites.php?action=mark_all_readed" title="Mark all as read">Mark all as read</a><br/>
{else}
You have no favorites.<br/>
{/if}
<h4>Hidden threads:</h4>
{if count($hidden_threads) > 0}
{section name=i loop=$hidden_threads}
<a href="{$DIR_PATH}/unhide_thread.php?thread={$hidden_threads[i].thread}" title="Refer to unhide thread">/{$hidden_threads[i].board_name}/{$hidden_threads[i].thread_number}</a>
{/section}<br/>
{else}
You have no hidden threads.<br/>
{/if}
{include file='footer.tpl'}