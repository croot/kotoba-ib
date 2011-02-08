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
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Report'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<br/>
<div class="logo">You want to report about post with id {$id}</div>
<br/>
<form action="{$DIR_PATH}/report.php" method="post">
<input type="hidden" name="post" value="{$id}">
<table align="center" border="0">
<tr valign="top">
    <td>Captcha:</td>
    <td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/captcha/image.php?' + Math.random(); return false"><img border="0" id="captcha" src="{$DIR_PATH}/captcha/image.php" alt="Kotoba capcha v0.4" align="middle" /></a> <input type="text" name="captcha_code" size="10" maxlength="6" /></td>
</tr>
<tr valign="top">
    <td colspan=2><input type="submit" value="Report"></td>
</tr>
</table>
</form>
<br>
<br>
<a href="{$DIR_PATH}/">Home</a>
{include file="footer.tpl"}