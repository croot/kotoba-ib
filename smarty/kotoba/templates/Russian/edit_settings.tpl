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
Код страницы редактирования настроек.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления.
	$threads_per_page - количество нитей на странице.
	$posts_per_thread - количество сообщений в нити.
	$lines_per_post - количество строк в сообщении.
	$languages - массив имен языков.
	$language -	язык.
	$stylesheets - массив имен стилей оформления.
*}
{include file='header.tpl' page_title='Мои настройки' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<form action="{$DIR_PATH}/edit_settings.php" method="post">
<i>Введите ключевое слово, чтобы загрузить ваши настройки.</i><br>
    <input type="text" name="keyword_load" size="32">
    <input type="submit" value="Загрузить">
</form>
<br>
<form action="{$DIR_PATH}/edit_settings.php" method="post">
<b>Опции предпросмотра доски:</b>
    <table border="0">
        <tr valign="top"><td>Количество нитей на странице: </td><td><input type="text" name="threads_per_page" size="10" value="{$threads_per_page}"></td></tr>
        <tr valign="top"><td>Количество сообщений в нити: </td><td><input type="text" name="posts_per_thread" size="10" value="{$posts_per_thread}"></td></tr>
        <tr valign="top"><td>Количество строк в сообщении: </td><td><input type="text" name="lines_per_post" size="10" value="{$lines_per_post}"></td></tr>
    </table>
<b>Другое:</b>
    <table border="0">
        <tr valign="top"><td>Язык: </td><td><select name="language_id">{section name=j loop=$languages}<option value="{$languages[j].id}"{if $language == $languages[j].name} selected{/if}>{$languages[j].name}</option>{/section}</select></td></tr>
        <tr valign="top"><td>Стиль оформления: </td><td><select name="stylesheet_id">{section name=i loop=$stylesheets}<option value="{$stylesheets[i].id}"{if $STYLESHEET == $stylesheets[i].name} selected{/if}>{$stylesheets[i].name}</option>{/section}</select></td></tr>
    </table>
<i>Введите ключевое слово, чтобы сохранить эти настройки.<br>
В дальнейшем вы можете загрузить их, введя ключевое слово.</i><br>
    <input type="text" name="keyword_save" size="32">
    <input type="submit" value="Сохранить">
</form>
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}