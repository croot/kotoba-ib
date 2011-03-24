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
    $is_admin - is current user are admin.
    $prev_filter_board - previous value of board filter between two script calls.
    $prev_filter_date_time - previous value of date-time filter between two script calls.
    $prev_filter_number - previous value of number filter between two script calls.
    $prev_filter_ip - previous value of ip filter between two script calls.
    $is_admin - is current user are admin.
    $moderate_posts - array of code of filtred posts for moderation.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Moderators main page'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Moderators main page</div>
<hr>
<form action="{$DIR_PATH}/admin/moderate.php" method="post">
{include file='moderate_pages_list.tpl'
         pages=$pages
         page=$page
         prev_filter_board=$prev_filter_board
         prev_filter_date_time=$prev_filter_date_time
         prev_filter_number=$prev_filter_number
         prev_filter_ip=$prev_filter_ip}

<table border="1">
<tr>
    <td>доска
    <select name="filter_board">
        <option value="" selected></option>
        {if $is_admin}<option value="all"{if $prev_filter_board == 'all'} selected{/if}>All</option>{/if}

{section name=i loop=$boards}
        <option value="{$boards[i].id}"{if $prev_filter_board == $boards[i].id} selected{/if}>{$boards[i].name}</option>{/section}

    </select>
    </td>
    <td>Date <input type="text" name="filter_date_time" value="{$prev_filter_date_time}"></td>
    <td>Post number <input type="text" name="filter_number" value="{$prev_filter_number}"></td>
    <td>IP-address <input type="text" name="filter_ip" value="{$prev_filter_ip}"></td>
    <td><input type="submit" name="filter" value="Select"> <input type="reset" value="Reset"></td>
</tr>
<tr>
    <td colspan="5">Show only posts with attachments <input type="checkbox" name="attachments_only" value="1"></td>
</tr>
</table>
</form>
<hr>
<form action="{$DIR_PATH}/admin/moderate.php" method="post">
<table border="1">
<tr>
    <td>Ban type<br>
        [<input type="radio" name="ban_type" value="none" checked>Not ban]<br>
        [<input type="radio" name="ban_type" value="simple">Ban]<br>
        [<input type="radio" name="ban_type" value="hard">Hard ban]
    </td>
    <td colspan="2">Deletion type<br>
        [<input type="radio" name="del_type" value="none" checked>Not delete]<br>
        [<input type="radio" name="del_type" value="post">Delete post]<br>
        [<input type="radio" name="del_type" value="file">Delete attachments]<br>
        [<input type="radio" name="del_type" value="last">Delete and delete all last posts]
    </td>
    <td><input type="submit" name="action" value="Ок"> <input type="reset" value="Сброс"></td>
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