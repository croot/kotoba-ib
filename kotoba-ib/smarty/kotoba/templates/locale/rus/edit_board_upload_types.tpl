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
Код страницы редактирования связей загружаемых типов файлов с досками.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$upload_types - типы загружаемых файлов.
	$boards - доски.
	$board_upload_types - типы загружаемых файлов для сдосок.
*}
{include file='header.tpl' page_title='Редактирование связей загружаемых типов файлов с досками' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_board_upload_types.php" method="post">
<table border="1">
<tr>
	<td colspan="3">Чтобы удалить связи типов файлов с доской, пометьте соответствующие записи.<br>
	Чтобы добавить связь типа файла с доской, выберите доску, тип и сохраните изменения (дублирующиеся записи<br>
	будут проигнорированы).</td>
</tr>
{section name=i loop=$board_upload_types}
<tr>
	<td>
		{section name=j loop=$boards}
			{if $boards[j].id == $board_upload_types[i].board}{$boards[j].name}{/if}
		{/section}
	</td>
	<td>
		{section name=k loop=$upload_types}
			{if $upload_types[k].id == $board_upload_types[i].upload_type}{$upload_types[k].extension}{/if}
		{/section}
	</td>
	<td>
		<input type="checkbox" name="delete_{$board_upload_types[i].board}_{$board_upload_types[i].upload_type}" value="1">
	</td>
</tr>
{/section}
<tr>
	<td>
		<select name="new_bind_board">
			<option value="" selected></option>

		{section name=m loop=$boards}
			<option value="{$boards[m].id}">{$boards[m].name}</option>

		{/section}
		</select>
	</td>
	<td colspan="2">
		<select name="new_bind_upload_type">
			<option value="" selected></option>

		{section name=n loop=$upload_types}
			<option value="{$upload_types[n].id}">{$upload_types[n].extension}</option>

		{/section}
		</select>
	</td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}