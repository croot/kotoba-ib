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
Код страницы редактирования спамфильтра.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $patterns - шаблоны.
*}
{include file='header.tpl' page_title='Редактирование спамфильтра' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<br><br><form action="{$DIR_PATH}/admin/edit_spamfilter.php" method="post">
<table border="1">
<tr>
    <td colspan="2">Введите новый шаблон и нажмите Сохранить, чтобы добавить
    новый шаблон. Пометьте шаблоны и нажмите Сохранить, чтобы удалить помеченные
    шаблоны.</td>
</tr>
<tr>
    <td>Шаблон</td>
    <td>Удалить шаблон</td>
</tr>
{section name=i loop=$patterns}
<tr>
    <td>{$patterns[i].pattern}</td>
    <td><input type="checkbox" name="delete_{$patterns[i].id}" value="1"></td>
</tr>
{/section}
<tr>
    <td colspan="2"><input type="text" name="new_pattern" value=""></td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Сброс"> <input type="submit" value="Сохранить">
</form>
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}