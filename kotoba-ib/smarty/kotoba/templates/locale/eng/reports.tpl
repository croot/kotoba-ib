{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Reports handing page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $is_admin - is current user are admin.
    $reported_posts - array of code of reported posts.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Жалобы'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<br>
<form action="{$DIR_PATH}/admin/reports.php" method="post">
    <table border="1">
    <tr>
        <td colspan=3>Board
            <select name="filter_board">
            <option value="" selected></option>
            {if $is_admin}<option value="all">All</option>{/if}

            {section name=i loop=$boards}
            <option value="{$boards[i].id}">{$boards[i].name}</option>

            {/section}
            </select>
        </td>
        <td><input type="submit" name="filter" value="Выбрать"> <input type="reset" value="Сброс"></td>
    </tr>
    </table>
</form>
<hr>
<form action="{$DIR_PATH}/admin/reports.php" method="post">
    <table border="1">
    <tr>
        <td>Ban tyep<br>
        [<input type="radio" name="ban_type" value="none" checked>Not ban]<br>
        [<input type="radio" name="ban_type" value="simple">Ban]<br>
        [<input type="radio" name="ban_type" value="hard">Hard ban]
        </td>
        <td>Deletion type<br>
        [<input type="radio" name="del_type" value="none" checked>Not delete]<br>
        [<input type="radio" name="del_type" value="post">Delete post]<br>
        [<input type="radio" name="del_type" value="file">Delete attachement]<br>
        [<input type="radio" name="del_type" value="last">Delete and delete all last posts]
        </td>
        <td>This report<br>
        [<input type="radio" name="report_act" value=1 checked> Delete]<br>
        [<input type="radio" name="report_act" value=0> Not delete]
        </td>
        <td><input type="submit" name="action" value="Ок"> <input type="reset" value="Сброс"></td>
    </tr>
    <tr>
        <td>Mark posts</td>
        <td colspan="3">Post</td>
    </tr>
    {section name=i loop=$reported_posts}
    <tr>{$reported_posts[i]}</tr>

    {/section}
    </table>
</form>
<br>
<a href="{$DIR_PATH}/">Home</a>
{include file='footer.tpl'}