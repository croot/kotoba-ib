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
Код страницы ввода пароля для удаления сообщения.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления.
	$id - идентификатор сообщения.
	$password - паролья для удаления сообщения.
*}
{include file='header.tpl' page_title='Ввод пароля для удаления сообщения' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<h2>Вы удаляете сообщение {$id}</h2>
<form action="{$DIR_PATH}/remove_post.php" method="post">
<input type="hidden" name="post" value="{$id}">
<table align="center" border="0">
<tr valign="top">
	<td>Пароль:</td>
	<td><input type="password" name="password" size="30" value="{$password}"> <input type="submit" value="Удалить"></td>
</tr>
</table>
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file="footer.tpl"}