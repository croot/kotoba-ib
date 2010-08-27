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
Код заголовка страницы жалоб.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $boards - доски.
*}
{include file='header.tpl' page_title='Страница жалоб' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/reports.php" method="post">
<table border="1">
<tr>
	<td colspan=3>доска
		<select name="filter_board">
			<option value="" selected></option>
		{if $is_admin}
			<option value="all">Все</option>
		{/if}
		{section name=i loop=$boards}
			<option value="{$boards[i].id}">{$boards[i].name}</option>

		{/section}
		</select>
	</td>
	<td><input type="submit" name="filter" value="Выбрать"> <input type="reset" value="Сброс"></td>
</tr>
</table>
</form>
<hr>
<form action="{$DIR_PATH}/admin/reports.php" method="post">
	<table border="1">
	<tr>
		<td>Тип бана<br>
			[<input type="radio" name="ban_type" value="none" checked>Не банить]<br>
			[<input type="radio" name="ban_type" value="simple">Бан]<br>
			[<input type="radio" name="ban_type" value="hard">Бан в фаерволе]
		</td>
		<td>Тип удаления<br>
			[<input type="radio" name="del_type" value="none" checked>Не удалять]<br>
			[<input type="radio" name="del_type" value="post">Удалить сообщение]<br>
			[<input type="radio" name="del_type" value="file">Удалить файл]<br>
			[<input type="radio" name="del_type" value="last">Удалить последние сообщения]
		</td>
		<td>Действие с жалобой<br>
			[<input type="radio" name="report_act" value=1 checked> Удалить]<br>
			[<input type="radio" name="report_act" value=0> Не удалять]
		</td>
		<td><input type="submit" name="action" value="Ок"> <input type="reset" value="Сброс"></td>
	</tr>
	<tr>
		<td>Отметьте сообщения</td>
		<td colspan="3">Сообщение</td>
	</tr>