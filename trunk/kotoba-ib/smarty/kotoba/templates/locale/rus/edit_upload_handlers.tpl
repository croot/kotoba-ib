{* Smarty *}
{*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************
 *********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Код страницы редактирования обработчиков загружаемых файлов.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$upload_handlers - список обработчиков загружаемых файлов.
*}
{include file='header.tpl' page_title='Редактирование обработчиков загружаемых файлов' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_upload_handlers.php" method="post">
<table border="1">
<tr>
	<td colspan="2">Пометьте обработчики для удаления или введине имя нового обработчика,<br>
	чтобы зарегистрировать новый обработчик.</td>
</tr>
{section name=i loop=$upload_handlers}
<tr>
	<td>{$upload_handlers[i].name}</td><td><input type="checkbox" name="delete_{$upload_handlers[i].id}" value="1"></td>
</tr>
{/section}
<tr>
	<td colspan="2"><input type="text" name="new_upload_handler"></td>
</tr>
</table><br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}