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
Код страницы редактирования стилей оформления.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$stylesheets - стили оформления.
*}
{include file='header.tpl' page_title='Редактирование стилей оформления' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_stylesheets.php" method="post">
<table border="1">
<tr>
	<td colspan="2">Пометьте стили для удаления или введине имя нового стиля, чтобы создать новый стиль.</td>
</tr>
{section name=i loop=$stylesheets}
<tr>
	<td>{$stylesheets[i].name}</td><td><input type="checkbox" name="delete_{$stylesheets[i].id}" value="1"></td>
</tr>
{/section}
<tr>
	<td colspan="2"><input type="text" name="new_stylesheet"></td>
</tr>
</table><br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}