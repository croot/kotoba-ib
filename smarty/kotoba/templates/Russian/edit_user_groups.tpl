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
	$user_groups - список существующих пользователей и их группы.
	$groups - список существующих групп с их идентификатором.
*}
{include file='header.tpl' page_title='Редактировать принадлежность пользователей группам' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_user_groups.php" method="post">
<table border="1">
<tr>
	<td colspan="2">Введите id пользователя и выберите ему группу из списка, чтобы закрепить пользователя ещё за одной группой<br>
	или найдите id пользователя в списке и измените группу, за которой он закреплён.<br>
	Чтобы удалить закрпление, перезакрепите пользователя за фиктивной группой Remove.</td>
</tr>
{section name=i loop=$user_groups}
<tr>
	<td>{$user_groups[i].user}</td>
	<td>
		<select name="group_{$user_groups[i].user}_{$user_groups[i].group}">
		{section name=j loop=$groups}
			<option value="{$groups[j].id}"{if $groups[j].id == $user_groups[i].group} selected{/if}>{$groups[j].name}</option>

		{/section}
		</select>
	</td>
</tr>
{/section}
<tr>
	<td><input type="text" name="new_bind_user"></td>
	<td>
		<select name="new_bind_group">
		{section name=j loop=$groups}
			<option value="{$groups[j].id}">{$groups[j].name}</option>

		{/section}
		</select>
	</td>
</tr>
</table><br>
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}