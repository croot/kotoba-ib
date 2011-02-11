{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit user groups relations.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $groups - groups.
    $users - users.
    $user_groups - user groups relations.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Редактирование закреплений пользователей за группами"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Редактирование закреплений пользователей за группами</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_user_groups.php" method="post">
<table border="1">
<tr>
    <td colspan="3">Введите id пользователя и выберите ему группу из списка, чтобы закрепить пользователя ещё за одной группой<br>
    или найдите id пользователя в списке и измените группу, за которой он закреплён.<br>
    Чтобы удалить закрпление, пометьете его и сохраните изменения.</td>
</tr>
{section name=i loop=$user_groups}
<tr>
    <td>{$user_groups[i].user}</td>
    <td>
        <select name="group_{$user_groups[i].user}_{$user_groups[i].group}">
{section name=j loop=$groups}
            <option value="{$groups[j].id}"{if $groups[j].id == $user_groups[i].group} selected{/if}>{$groups[j].name}</option>{/section}

        </select>
    </td>
    <td><input type="checkbox" name="delete_{$user_groups[i].user}_{$user_groups[i].group}"></td>
</tr>
{/section}
<tr>
    <td>
    <select name="new_bind_user">
        <option value="" selected></option>
{section name=k loop=$users}
        <option value="{$users[k].id}">{$users[k].id}</option>{/section}

    </select>
    </td>
    <td colspan="2">
    <select name="new_bind_group">
        <option value="" selected></option>
{section name=j loop=$groups}
        <option value="{$groups[j].id}">{$groups[j].name}</option>{/section}

    </select>
    </td>
</tr>
</table>
<br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
{include file='footer.tpl'}