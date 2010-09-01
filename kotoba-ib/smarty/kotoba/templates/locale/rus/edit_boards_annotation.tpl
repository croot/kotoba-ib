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
Код страницы редактирования досок.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$boards - доски.
*}
{include file='header.tpl' page_title='Редактирование досок' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_boards_annotation.php" method="post">
<table border="1">
<tr>
	<td colspan="2">Измените аннотацию доски и сохраните изменения.</td>
</tr>
<tr>
	<td>Имя доски</td>
	<td>Аннотация</td>
</tr>
{section name=i loop=$boards}
<tr>
	<td>{$boards[i].name}</td>
	<td><textarea name="annotation_{$boards[i].id}" rows="5" cols="50">{$boards[i].annotation}</textarea></td>
</tr>
{/section}
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}