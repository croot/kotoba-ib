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
    $page - page number.
    $is_admin - is current user are admin.
    $prev_filter_board - previous value of board filter between two script calls.
    $prev_filter_date_time - previous value of date-time filter between two script calls.
    $prev_filter_number - previous value of number filter between two script calls.
    $prev_filter_ip - previous value of ip filter between two script calls.
    $moderate_posts - array of code of filtred posts for moderation.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Основная страница модератора'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Основная страница модератора</div>
{include file='moderate_pages_list.tpl'
         pages=$pages
         page=$page
         prev_filter_board=$prev_filter_board
         prev_filter_date_time=$prev_filter_date_time
         prev_filter_number=$prev_filter_number
         prev_filter_ip=$prev_filter_ip}

<hr>
<form action="{$DIR_PATH}/admin/moderate.php" method="post">
<table border="1">
<tr>
    <td>доска
    <select name="filter_board">
        <option value="" selected></option>
        {if $is_admin}<option value="all"{if $prev_filter_board == 'all'} selected{/if}>Все</option>{/if}

{section name=i loop=$boards}
        <option value="{$boards[i].id}"{if $prev_filter_board == $boards[i].id} selected{/if}>{$boards[i].name}</option>{/section}

    </select>
    </td>
    <td>дата <input type="text" name="filter_date_time" value="{$prev_filter_date_time}"></td>
    <td>номер сообщения <input type="text" name="filter_number" value="{$prev_filter_number}"></td>
    <td>IP-адрес <input type="text" name="filter_ip" value="{$prev_filter_ip}"></td>
    <td><input type="submit" name="filter" value="Выбрать"> <input type="reset" value="Сброс"></td>
</tr>
<tr>
    <td colspan="5">Показывать только сообщения с вложениями <input type="checkbox" name="attachments_only" value="1"></td>
</tr>
</table>
</form>
<hr>
<form action="{$DIR_PATH}/admin/moderate.php" method="post">
<table border="1">
<tr>
    <td>Тип бана<br>
        [<input type="radio" name="ban_type" value="none" checked>Не банить]<br>
        [<input type="radio" name="ban_type" value="simple">Бан]<br>
        [<input type="radio" name="ban_type" value="hard">Бан в фаерволе]
    </td>
    <td colspan="2">Тип удаления<br>
        [<input type="radio" name="del_type" value="none" checked>Не удалять]<br>
        [<input type="radio" name="del_type" value="post">Удалить сообщение]<br>
        [<input type="radio" name="del_type" value="file">Удалить файл]<br>
        [<input type="radio" name="del_type" value="last">Удалить последние сообщения]
    </td>
    <td><input type="submit" name="action" value="Ок"> <input type="reset" value="Сброс"></td>
</tr>
<tr>
    <td>Отметьте сообщения</td>
    <td colspan="3">Сообщение</td>
</tr>
{section name=i loop=$moderate_posts}
<tr>{$moderate_posts[i]}</tr>{/section}

</table>
</form>
{include file='footer.tpl'}