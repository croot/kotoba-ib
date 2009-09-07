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
	$upload_handlers - список существующих групп с их идентификаторами.
	$upload_types - список существующих досок с их идентификаторами.
*}
{include file='header.tpl' page_title='Редактирование типов загружаемых файлов' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_upload_types.php" method="post">
<table border="1">
<tr>
	<td colspan="5">Чтобы изменить обработчик загружаемых файлов для этого типа файлов, выберите другой обработчик из списка.<br>
	Чтобы изменить сохраняемый тип файла или изменить уменьшенную копию для какого-то типа файлов, измените значение в соотвествующих<br>
	полях ввода. Чтобы удалить тип файлов, пометьте его в соотвествующей колонке. Чтобы добавить тип файлов, введите все<br>
	необходимые данные и сохраните изменения.</td>
</tr>
<tr>
	<td>Тип файла</td><td>Сохраняемый тип файла</td><td>Обработчик</td><td>Уменьшенная копия</td><td>Удалить тип</td>
</tr>
{section name=i loop=$upload_types}
<tr>
	<td>
	{$upload_types[i].extension}
	</td>
	<td>
	<input type="text" name="store_extension_{$upload_types[i].id}" value="{$upload_types[i].store_extension}">
	</td>
	<td>
	<select name="upload_handler_{$upload_types[i].id}">
	{section name=j loop=$upload_handlers}
		<option value="{$upload_handlers[j].id}"{if $upload_types[i].upload_handler == $upload_handlers[j].id} selected{/if}>{$upload_handlers[j].name}</option>

	{/section}
	</select>
	</td>
	<td>
	<input type="text" name="thumbnail_image_{$upload_types[i].id}" value="{$upload_types[i].thumbnail_image}">
	</td>
	<td>
	<input type="checkbox" name="delete_{$upload_types[i].id}" value="1">
	</td>
</tr>
{/section}
<tr>
	<td><input type="text" name="new_extension"></td>
	<td><input type="text" name="new_store_extension"></td>
	<td>
	<select name="new_upload_handler">
	{section name=j loop=$upload_handlers}
		<option value="{$upload_handlers[j].id}">{$upload_handlers[j].name}</option>

	{/section}
	</select>
	</td>
	<td colspan="2"><input type="text" name="new_thumbnail_image"></td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}