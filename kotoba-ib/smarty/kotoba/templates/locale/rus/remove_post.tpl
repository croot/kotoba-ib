{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Remove post.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $post - post.
    $password - password.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Ввод пароля для удаления сообщения'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<br/>
<div class="logo">Вы удаляете сообщение с идентификатором {$post.id}</div>
<br/>
{if $post.password}
<form action="{$DIR_PATH}/remove_post.php" method="post">
<input type="hidden" name="post" value="{$post.id}">
<table align="center" border="0">
<tr valign="top">
    <td>Пароль:</td>
    <td><input type="password" name="password" size="30" value="{$password}"> <input type="submit" value="Удалить"></td>
</tr>
</table>
</form>
{else}
<span class="error">У этого сообщения отсутствует пароль для удаления.
<br/>
Если вы хотите удалить это сообщение и можете доказать, что оно оставлено вами, обратитесь к администратору
по электронной почте: {$_SERVER.SERVER_ADMIN}</span>
{/if}

{include file="footer.tpl"}