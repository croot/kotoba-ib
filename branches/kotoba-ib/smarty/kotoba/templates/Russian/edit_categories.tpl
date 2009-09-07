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
Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления.
	$categories - список категорий досок.
*}
{include file='header.tpl' page_title='Редактирование категорий досок' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_categories.php" method="post">
<table border="1">
<tr>
	<td colspan="2">Пометьте категории для удаления или введине имя новой категории, чтобы создать новую категорию.</td>
</tr>
{section name=i loop=$categories}
<tr>
	<td>{$categories[i].name}</td><td><input type="checkbox" name="delete_{$categories[i].id}" value="1"></td>
</tr>
{/section}
<tr>
	<td colspan="2"><input type="text" name="new_category"></td>
</tr>
</table><br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}