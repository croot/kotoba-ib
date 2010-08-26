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
Код страницы подтверждения жалобы.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления.
    $id - идентификатор сообщения.
*}
{include file='header.tpl' page_title='Подтверждение жалобы на сообщение' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<h2>Введите капчу для подтверждения</h2>
<form action="{$DIR_PATH}/report.php" method="post">
<input type="hidden" name="post" value="{$id}">
<input type="hidden" name="submit" value=1>
<table align="center" border="0">
<tr valign="top">
	<td>Капча:</td>
	<td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/securimage/securimage_show.php?' + Math.random(); return false"><img id="captcha" src="{$DIR_PATH}/securimage/securimage_show.php" alt="CAPTCHA Image" /></a> <input type="text" name="captcha_code" size="10" maxlength="6" /></td>
</tr>
<tr valign="top">
	<td colspan=2><input type="submit" value="Пожаловаться"></td>
</tr>
</table>
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file="footer.tpl"}