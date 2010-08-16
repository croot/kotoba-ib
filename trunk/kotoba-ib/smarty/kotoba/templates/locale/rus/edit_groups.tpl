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
Код страницы редактирования группы.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$groups - массив групп.
*}
{include file='header.tpl' page_title='Редактирование групп' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
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
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}