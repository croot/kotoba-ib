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
	$board_name - имя доски.
	$post_num - номер сообщения.
	$password - паролья для удаления сообщения.
*}
{include file='header.tpl' page_title='Ввод пароля для удаления сообщения' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<h2>Вы удаляете сообщение {$board_name}/{$post_num}</h2>
<form action="{$DIR_PATH}/remove_post.php" method="post">
<input type="hidden" name="board" value="{$board_name}">
<input type="hidden" name="post" value="{$post_num}">
<table align="center" border="0">
<tr valign="top">
	<td>Password:</td>
	<td><input type="password" name="rempass" size="30" value="{$password}"> <input type="submit" value="Удалить"></td>
</tr>
</table>
{include file="footer.tpl"}