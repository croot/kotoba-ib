{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit groups page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $groups - groups.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Редактирование групп"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Редактирование групп</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_groups.php" method="post">
<table border="1">
<tr>
    <td colspan="2">Пометьте группы для удаления или введине имя новой группы, чтобы создать новую группу.</td>
</tr>
{section name=i loop=$groups}
<tr>
    <td>{$groups[i].name}</td><td><input type="checkbox" name="delete_{$groups[i].id}" value="{$groups[i].id}"></td>
</tr>
{/section}
<tr>
    <td colspan="2"><input type="text" name="new_group"></td>
</tr>
</table><br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
{include file='footer.tpl'}