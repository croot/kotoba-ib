{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of report page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $id - post id.
    $enable_captcha - laptcha flag (see config.default).
    $captcha - used captcha (see config.default).
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
{if $enable_captcha}
<tr valign="top">
    <td>Капча:</td>
{if $captcha == 'captcha'}    <td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/captcha/image.php?' + Math.random(); return false"><img border="0" id="captcha" src="{$DIR_PATH}/captcha/image.php" alt="Kotoba capcha v0.4" align="middle" /></a> <input type="text" name="captcha_code" size="28" maxlength="64" accesskey="f"></td>{/if}
{if $captcha == 'animaptcha'}    <td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/animaptcha/animaptcha.php?' + Math.random(); return false"><img border="0" id="captcha" src="{$DIR_PATH}/animaptcha/animaptcha.php" alt="Kotoba animapcha v0.1" align="middle" /></a> <input type="text" name="animaptcha_code" size="28" maxlength="64" accesskey="f"></td>{/if}
</tr>
{/if}

<tr valign="top">
    <td colspan=2><input type="submit" value="Пожаловаться"></td>
</tr>
</table>
</form>
{include file="footer.tpl"}