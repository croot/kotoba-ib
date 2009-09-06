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
	$languages - список языков.
*}
{include file='header.tpl' page_title='Редактирование языков' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_languages.php" method="post">
<table border="1">
<tr>
	<td colspan="2">Пометьте языки для удаления или введине имя нового языка, чтобы создать новый язык.</td>
</tr>
{section name=i loop=$languages}
<tr>
	<td>{$languages[i].name}</td><td><input type="checkbox" name="delete_{$languages[i].id}" value="1"></td>
</tr>
{/section}
<tr>
	<td colspan="2"><input type="text" name="new_language"></td>
</tr>
</table><br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}