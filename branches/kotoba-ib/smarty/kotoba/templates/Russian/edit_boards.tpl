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
	$popdown_handlers - обработчики удаления нитей.
	$categories - категории досок.
	$boards - доски.
*}
{include file='header.tpl' page_title='Редактирование досок' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/admin/edit_boards.php" method="post">
<table border="1">
<tr>
	<td colspan="7">Чтобы добавить доску, введите все необходимые параметры и сохраните изменения.<br>
	Чтобы отредактировать параметры существующих досок, введите новые значения в соотвествующие поля.<br>
	Чтобы удалить доску, отметьте её. Помните, что доску нельзя удалить, если к ней привязаны какие-либо<br>
	данные, будь то нити, сообщения, типы файлов, права доступа и т.д.</td>
</tr>
<tr>
	<td>
		Имя
	</td>
	<td>
		Заголовок
	</td>
	<td>
		Бамп-лимит
	</td>
	<td>
		Одинаковые загрузки
	</td>
	<td>
		Обработчик тредов
	</td>
	<td>
		Категория
	</td>
	<td>
		X
	</td>
</tr>
{section name=i loop=$boards}
<tr>
	<td>
		{$boards[i].name}
	</td>
	<td>
		<input type="text" name="title_{$boards[i].id}" value="{$boards[i].title}">
	</td>
	<td>
		<input type="text" name="bump_limit_{$boards[i].id}" value="{$boards[i].bump_limit}">
	</td>
	<td>
		<input type="text" name="same_upload_{$boards[i].id}" value="{$boards[i].same_upload}">
	</td>
	<td>
		<select name="popdown_handler_{$boards[i].id}">
		{section name=j loop=$popdown_handlers}
			<option value="{$popdown_handlers[j].id}"{if $popdown_handlers[j].id == $boards[i].popdown_handler} selected{/if}>{$popdown_handlers[j].name}</option>

		{/section}
		</select>
	</td>
	<td>
		<select name="category_{$boards[i].id}">
		{section name=k loop=$categories}
			<option value="{$categories[k].id}"{if $categories[k].id == $boards[i].category} selected{/if}>{$categories[k].name}</option>

		{/section}
		</select>
	</td>
	<td>
		<input type="checkbox" name="delete_{$boards[i].id}" value="1">
	</td>
</tr>
{/section}
<tr>
	<td>
		<input type="text" name="new_name" value="">
	</td>
	<td>
		<input type="text" name="new_title" value="">
	</td>
	<td>
		<input type="text" name="new_bump_limit" value="">
	</td>
	<td>
		<input type="text" name="new_same_upload" value="">
	</td>
	<td>
		<select name="new_popdown_handler">
			<option value="" selected></option>
		{section name=m loop=$popdown_handlers}
			<option value="{$popdown_handlers[m].id}">{$popdown_handlers[m].name}</option>

		{/section}
		</select>
	</td>
	<td colaspan="2">
		<select name="new_category">
			<option value="" selected></option>
		{section name=n loop=$categories}
			<option value="{$categories[n].id}">{$categories[n].name}</option>

		{/section}
		</select>
	</td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}
