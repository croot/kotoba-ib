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
Скрипт выводит форму редактирования настроек нитией.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$boards - доски.
	$threads - нити.
*}
<form action="{$DIR_PATH}/admin/edit_threads.php" method="post">
<table border="1">
<tr>
	<td colspan="6">Измените параметры и сохраните изменения.</td>
</tr>
<tr>
	<td>id</td>
	<td>board</td>
	<td>original_post</td>
	<td>bump limit</td>
	<td>sage</td>
	<td>with images</td>
</tr>
{section name=i loop=$threads}
<tr>
	<td>
		{$threads[i].id}
	</td>
	<td>
		{section name=j loop=$boards}
			{if $threads[i].board == $boards[j].id}{$boards[j].name}{/if}
		{/section}
	</td>
	<td>
		{$threads[i].original_post}
	</td>
	<td>
		<input type="text" name="bump_limit_{$threads[i].id}" value="{$threads[i].bump_limit}">
	</td>
	<td>
		<input type="checkbox" name="sage_{$threads[i].id}" value="1"{if $threads[i].sage} checked{/if}>
	</td>
	<td>
		<input type="checkbox" name="with_images_{$threads[i].id}" value="1"{if $threads[i].with_images} checked{/if}>
	</td>
</tr>
{/section}
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>