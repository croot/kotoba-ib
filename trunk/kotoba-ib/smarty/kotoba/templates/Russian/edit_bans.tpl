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
Код страницы редактирования банов.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$bans_decoded - баны с проеобразованной датой и ip в обычный формат из целого.
*}
{include file='header.tpl' page_title='Редактирование банов' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
<table border="1">
<tr>
	<td colspan="5">Чтобы удалить баны, пометьте нужные строки. Чтобы забанить определёный ip, введите его<br>
	в качестве начала и конца диапазона, опционально введите причину и выберите время бана из предложенного списка.<br>
	Чтобы забанить сеть, введине начало и конец диапазона её адресов. Чтобы разбанить определённый ip, введите его<br>
	в соотвествующее поле ввода; помните, при этом будут удалены все баны, в диапазон которых входит этот ip.</td>
</tr>
<tr>
	<td>Начало диапазона</td><td>Конец диапазона</td><td>Причина</td><td>Время истечения</td><td>Удалить запись</td>
</tr>
{section name=i loop=$bans_decoded}
<tr>
	<td>{$bans_decoded[i].range_beg}</td>
	<td>{$bans_decoded[i].range_end}</td>
	<td>{$bans_decoded[i].reason}</td>
	<td>{$bans_decoded[i].untill}</td>
	<td><input type="checkbox" name="delete_{$bans_decoded[i].id}"></td>
</tr>
{/section}
<tr>
	<td><input type="text" name="new_range_beg"></td>
	<td><input type="text" name="new_range_end"></td>
	<td><input type="text" name="new_reason"></td>
	<td colspan="2">
		<select name="new_untill">
			<option value="10">10 секунд</option>
			<option value="30">30 секунд</option>
			<option value="60">1 минута</option>
			<option value="900">15 минут</option>
			<option value="1800">30 минут</option>
			<option value="3600">1 час</option>
		</select>
	</td>
</tr>
</table><br>
Разбанить заданный ip: <input type="text" name="unban"><br><br>
<input type="reset" value="Сброс"> <input type="submit" name="submit" value="Сохранить">
</form>
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}