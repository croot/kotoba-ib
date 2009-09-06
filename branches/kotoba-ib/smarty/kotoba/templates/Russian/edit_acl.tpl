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
	$groups - список существующих групп с их идентификаторами.
	$boards - список существующих досок с их идентификаторами.
	$acl - список контроля доступа.
*}
{include file='header.tpl' page_title='Редактирование списка контроля доступа' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_acl.php" method="post">
<table border="1">
<tr>
	<td colspan="8">Чтобы изменить права в существующей записи, измение их с помощью флажков. Помните, что если, к примеру, вы<br>
	запретили какой-то группе просматривать какую-то доску, то права на редактирование и модерирование этой доски для этой группы<br>
	не имеют смысла и будут сняты автоматически. Чтобы удалить запись, пометьте её в соотвествующей колонке. Чтобы добавить запись<br>
	введите все необходимые данные и установите желаемые права.</td>
</tr>
<tr>
	<td>Группа</td><td>Доска</td><td>Нить</td><td>Сообщение</td><td>Просматривать</td><td>Изменять</td><td>Модерировать</td><td>Удалить запись</td>
</tr>
{section name=i loop=$acl}
<tr>
	<td>
	{if $acl[i].group == null}NULL
	{else}
		{section name=j loop=$groups}
			{if $acl[i].group == $groups[j].id}{$groups[j].name}{/if}
		{/section}
	{/if}
	</td>
	<td>
	{if $acl[i].board == null}NULL
	{else}
		{section name=j loop=$boards}
			{if $acl[i].board == $boards[j].id}{$boards[j].name}{/if}
		{/section}
	{/if}
	</td>
	<td>
	{if $acl[i].thread == null}NULL{else}{$acl[i].thread}{/if}
	</td>
	<td>
	{if $acl[i].post == null}NULL{else}{$acl[i].post}{/if}
	</td>
	<td>
	<input type="checkbox" name="view_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1"{if $acl[i].view == 1} checked{/if}>
	</td>
	<td>
	<input type="checkbox" name="change_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1"{if $acl[i].change == 1} checked{/if}>
	</td>
	<td>
	<input type="checkbox" name="moderate_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1"{if $acl[i].moderate == 1} checked{/if}>
	</td>
	<td>
	<input type="checkbox" name="delete_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1">
	</td>
</tr>
{/section}
<tr>
	<td><input type="text" name="new_group"></td>
	<td><input type="text" name="new_board"></td>
	<td><input type="text" name="new_thread"></td>
	<td><input type="text" name="new_post"></td>
	<td><input type="checkbox" name="new_view" value="1"></td>
	<td><input type="checkbox" name="new_change" value="1"></td>
	<td colspan="2"><input type="checkbox" name="new_moderate" value="1"></td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}