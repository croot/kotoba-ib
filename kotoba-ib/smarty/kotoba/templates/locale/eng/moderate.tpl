{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of moderators main script.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $pages - pages.
    $page - page number.
    $filter - posts filter.
    $is_admin - is current user are admin.
    $moderate_posts - array of code of filtred posts for moderation.
*}
{include file='header.tpl'
         DIR_PATH=$DIR_PATH
         STYLESHEET=$STYLESHEET
         page_title='Moderators main page'}

{include file='adminbar.tpl'
         DIR_PATH=$DIR_PATH
         show_control=$show_control}

{include file='navbar.tpl'
         DIR_PATH=$DIR_PATH
         boards=$boards}

<div class="logo">Moderators main page</div>
{include file='moderate_pages_list.tpl'
         pages=$pages
         page=$page
         filter=$filter}

<hr>
<form action="{$DIR_PATH}/admin/moderate.php" method="post">
<input type="hidden" name="page" value="{$page}">
<table border="1">
<tr>
    <td>доска
    <select name="filter[board]">
        <option value="" selected></option>
        {if $is_admin}<option value="all"{if $filter.board == 'all'} selected{/if}>All</option>{/if}

{section name=i loop=$boards}
        <option value="{$boards[i].id}"{if $filter.board == $boards[i].id} selected{/if}>{$boards[i].name}</option>{/section}

    </select>
    </td>
    <td>Date <input type="text" name="filter[date_time]" value="{$filter.date_time}"></td>
    <td>Post number <input type="text" name="filter[number]" value="{$filter.number}"></td>
    <td>IP-address <input type="text" name="filter[ip]" value="{$filter.ip}"></td>
    <td><input type="submit" name="do_filter" value="Select"> <input type="reset" value="Reset"></td>
</tr>
<tr>
    <td colspan="5">Show only posts with attachments <input type="checkbox" name="attachments_only" value="1"></td>
</tr>
</table>
<hr>
<table border="1">
<tr>
    <td>Ban type<br>
        [<input type="radio" name="action[ban_type]" value="none" checked>Not ban]<br>
        [<input type="radio" name="action[ban_type]" value="simple">Ban]<br>
        [<input type="radio" name="action[ban_type]" value="hard">Hard ban]
    </td>
    <td colspan="2">Deletion type<br>
        [<input type="radio" name="action[del_type]" value="none" checked>Not delete]<br>
        [<input type="radio" name="action[del_type]" value="post">Delete post]<br>
        [<input type="radio" name="action[del_type]" value="file">Delete attachments]<br>
        [<input type="radio" name="action[del_type]" value="last">Delete and delete all last posts]
    </td>
    <td><input type="submit" name="do_action" value="Ok"> <input type="reset" value="Reset"></td>
</tr>
<tr>
    <td>Mark posts</td>
    <td colspan="3">Post</td>
</tr>
{section name=i loop=$moderate_posts}
<tr>{$moderate_posts[i]}</tr>{/section}

</table>
</form>
{include file='footer.tpl'}