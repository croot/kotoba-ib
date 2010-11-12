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
Код заголовка основной страницы модератора.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $show_control - показывать ссылку на страницу административных фукнций и фукнций модераторов в панели администратора.
    $is_admin - фалг администратора.
    $boards - доски.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Основная страница модератора'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Основная страница модератора</div>
<hr>
<form action="{$DIR_PATH}/admin/moderate.php" method="post">
<table border="1">
<tr>
	<td>доска
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
	<td>дата <input type="text" name="filter_date_time"></td>
	<td>номер сообщения <input type="text" name="filter_number"></td>
	<td>IP-адрес <input type="text" name="filter_ip"></td>
	<td><input type="submit" name="filter" value="Выбрать"> <input type="reset" value="Сброс"></td>
</tr>
<tr>
    <td colspan="5">Показывать только сообщения с вложениями <input type="checkbox" name="attachments_only" value="1"></td>
</tr>
</table>
</form>
<hr>
<form action="{$DIR_PATH}/admin/moderate.php" method="post">
	<table border="1">
	<tr>
		<td>Тип бана<br>
			[<input type="radio" name="ban_type" value="none" checked>Не банить]<br>
			[<input type="radio" name="ban_type" value="simple">Бан]<br>
			[<input type="radio" name="ban_type" value="hard">Бан в фаерволе]
		</td>
		<td colspan="2">Тип удаления<br>
			[<input type="radio" name="del_type" value="none" checked>Не удалять]<br>
			[<input type="radio" name="del_type" value="post">Удалить сообщение]<br>
			[<input type="radio" name="del_type" value="file">Удалить файл]<br>
			[<input type="radio" name="del_type" value="last">Удалить последние сообщения]
		</td>
		<td><input type="submit" name="action" value="Ок"> <input type="reset" value="Сброс"></td>
	</tr>
	<tr>
		<td>Отметьте сообщения</td>
		<td colspan="3">Сообщение</td>
	</tr>