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
Код страницы жалобы на сообщение.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль (см. config.default).
    $show_control - показывать ссылку на страницу административных фукнций и фукнций модераторов в панели администратора.
    $boards - доски.
    $id - идентификатор сообщения.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Жалоба на сообщение'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<br/>
<div class="logo">Вы собираетесь пожаловать на сообщение с идентификатором {$id}</div>
<br/>
<form action="{$DIR_PATH}/report.php" method="post">
<input type="hidden" name="post" value="{$id}">
<table align="center" border="0">
<tr valign="top">
    <td>Капча:</td>
    <td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/captcha/image.php?' + Math.random(); return false"><img border="0" id="captcha" src="{$DIR_PATH}/captcha/image.php" alt="Kotoba capcha v0.4" align="middle" /></a> <input type="text" name="captcha_code" size="10" maxlength="6" /></td>
</tr>
<tr valign="top">
    <td colspan=2><input type="submit" value="Пожаловаться"></td>
</tr>
</table>
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file="footer.tpl"}