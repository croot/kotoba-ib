{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit upload handlers page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $upload_handlers - upload handlers.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Редактирование обработчиков загружаемых файлов"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Редактирование обработчиков загружаемых файлов</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_upload_handlers.php" method="post">
<table border="1">
<tr>
    <td colspan="2">Пометьте обработчики для удаления или введине имя нового обработчика,<br>
    чтобы зарегистрировать новый обработчик.</td>
</tr>
{section name=i loop=$upload_handlers}
<tr><td>{$upload_handlers[i].name}</td><td><input type="checkbox" name="delete_{$upload_handlers[i].id}" value="1"></td></tr>{/section}

<tr>
    <td colspan="2"><input type="text" name="new_upload_handler"></td>
</tr>
</table>
<br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
{include file='footer.tpl'}