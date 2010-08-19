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
Код формы редактирования настроек нитией.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$boards - доски.
	$threads - нити.
*}
<form action="{$DIR_PATH}/admin/edit_threads.php" method="post">
<table border="1">
<tr><td colspan="7">Измените параметры и сохраните изменения.</td></tr>
<tr>
	<td>Идентификатор</td>
	<td>Доска</td>
	<td>Оригинальное сообщение</td>
	<td>Бамплимит специфичный для нити</td>
	<td>Флаг закрепления</td>
	<td>Флаг поднятия нити при ответе</td>
	<td>Флаг загрузки изображений</td>
</tr>
{section name=i loop=$threads}
<tr>
	<td>{$threads[i].id}</td>
	<td>
		{section name=j loop=$boards}
			{if $threads[i].board == $boards[j].id}{$boards[j].name}{/if}
		{/section}
	</td>
	<td>{$threads[i].original_post}</td>
	<td><input type="text" name="bump_limit_{$threads[i].id}" value="{$threads[i].bump_limit}"></td>
	<td><input type="checkbox" name="sticky_{$threads[i].id}" value="1"{if $threads[i].sticky} checked{/if}></td>
	<td><input type="checkbox" name="sage_{$threads[i].id}" value="1"{if $threads[i].sage} checked{/if}></td>
	<td>
		<select name="with_attachments_{$threads[i].id}">
		{section name=k loop=$boards}
			{if $threads[i].board == $boards[k].id}
			<option value=""{if $threads[i].with_attachments === null} selected{/if}>Унаследован</option>
			<option value="1"{if $threads[i].with_attachments == '1'} selected{/if}>Установлен</option>
			<option value="0"{if $threads[i].with_attachments == '0'} selected{/if}>Сброшен</option>
			{/if}
		{/section}
		</select>
	</td>
</tr>
{/section}
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>