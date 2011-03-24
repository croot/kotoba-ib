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
    $pages - pages.
    $page - page number.
    $is_admin - is current user are admin.
    $reported_posts - array of code of reported posts.
    $prev_filter_board - previous value of board filter between two script calls.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Жалобы'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Жалобы</div>
{include file='reports_pages_list.tpl'
         pages=$pages
         page=$page
         prev_filter_board=$prev_filter_board}

<hr>
<form action="{$DIR_PATH}/admin/reports.php" method="post">
    <table border="1">
    <tr>
        <td colspan=3>доска
            <select name="filter_board">
            <option value="" selected></option>
            {if $is_admin}<option value="all"{if $prev_filter_board == 'all'} selected{/if}>Все</option>{/if}

            {section name=i loop=$boards}
            <option value="{$boards[i].id}"{if $prev_filter_board == $boards[i].id} selected{/if}>{$boards[i].name}</option>

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
        <td>Тип бана<br>
        [<input type="radio" name="ban_type" value="none" checked>Не банить]<br>
        [<input type="radio" name="ban_type" value="simple">Бан]<br>
        [<input type="radio" name="ban_type" value="hard">Бан в фаерволе]
        </td>
        <td>Тип удаления<br>
        [<input type="radio" name="del_type" value="none" checked>Не удалять]<br>
        [<input type="radio" name="del_type" value="post">Удалить сообщение]<br>
        [<input type="radio" name="del_type" value="file">Удалить файл]<br>
        [<input type="radio" name="del_type" value="last">Удалить последние сообщения]
        </td>
        <td>Действие с жалобой<br>
        [<input type="radio" name="report_act" value=1 checked> Удалить]<br>
        [<input type="radio" name="report_act" value=0> Не удалять]
        </td>
        <td><input type="submit" name="action" value="Ок"> <input type="reset" value="Сброс"></td>
    </tr>
    <tr>
        <td>Отметьте сообщения</td>
        <td colspan="3">Сообщение</td>
    </tr>
    {section name=i loop=$reported_posts}
    <tr>{$reported_posts[i]}</tr>

    {/section}
    </table>
</form>
{include file='footer.tpl'}