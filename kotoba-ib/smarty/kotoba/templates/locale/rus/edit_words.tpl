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
Код страницы редактирования фильтрации слов.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
	$words - слова.
*}
{include file='header.tpl' page_title='Редактирование фильтрации слов' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_words.php" method="post">
<table border="1">
<tr>
	<td colspan="3">Чтобы добавить слово, введите все необходимые параметры.
	Чтобы отредактировать параметры существующих слов, отредактируйте
	соотвествующие поля таблицы. Чтобы удалить слово, отметьте её.</td>
</tr>
<tr>
	<td>Доска</td>
	<td>Слово</td>
	<td>Замена</td>
	<td>Удалить слово</td>
</tr>
{section name=i loop=$words}
<tr>
	<td>
		{section name=j loop=$boards}
				{if $boards[j].id == $words[i].board_id}{$boards[j].name}{/if}
		{/section}
	</td>
	<td><input type="text" name="word_{$words[i].id}" value="{$words[i].word}"></td>
	<td><input type="text" name="replace_{$words[i].id}" value="{$words[i].replace}"></td>
	<td><input type="checkbox" name="delete_{$words[i].id}" value="1"></td>
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
	<td><input type="text" name="new_word" value=""></td>
	<td colspan="2"><input type="text" name="new_replace" value=""></td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}