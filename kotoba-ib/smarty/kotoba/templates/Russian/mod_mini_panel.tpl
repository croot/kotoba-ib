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
Код мини панели администратора и модератора.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления (см. config.default).
	$post_id - идентификатор сообщения.
	$ip - IP адрес автора сообщения.
*}
<table border="1">
<tr valign="top">
	<td>{$ip}</td>
	<td>
		<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
		<input type="submit" value="Бан">
		<input type="hidden" name="new_range_beg" value="{$ip}">
		<input type="hidden" name="new_range_end" value="{$ip}">
		<input type="hidden" name="new_reason" value="">
		<input type="hidden" name="new_untill" value="10">
		<input type="hidden" name="submited" value="1">
		</form>
	<td>
		<form action="{$DIR_PATH}/admin/hard_ban.php" method="post">
		<input type="submit" value="Бан ф.">
		<input type="hidden" name="range_beg" value="{$ip}">
		<input type="hidden" name="range_end" value="{$ip}">
		</form>
	</td>
</tr>
</table>
{*
<table border="1">
<tr valign="top">
	<td>{$ip}</td>
	<td>
		<select name="action">
			<option value="1">Мягкий бан</option>
			<option value="2">Бан в фаерволе</option>
			<option value="3">Удалить</option>
			<option value="4">Мягкий бан и удаление</option>
			<option value="5">Бан в фаерволе и удаление</option>
			<option value="6">Удалить последние 10 сообщений</option>
			<option value="7">Бан в фаерволе и удалить последние 10 сообщений</option>
			<option value="8">Бан в фаерволе и удалить последние 10 сообщений</option>
		</select>
	</td>
	<td><input type="submit" value="Выполнить"></td>
</tr>
</table>
*}