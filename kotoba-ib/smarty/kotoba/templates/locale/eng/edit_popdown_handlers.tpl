{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit spamfilter page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $popdown_handlers - popdown handelers.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Edit popdown handlers"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Edit popdown handlers</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_popdown_handlers.php" method="post">
<table border="1">
<tr>
    <td colspan="2">Input name to add new popdown handler. Mark handlers to delete.</td>
</tr>
{section name=i loop=$popdown_handlers}
<tr>
    <td>{$popdown_handlers[i].name}</td><td><input type="checkbox" name="delete_{$popdown_handlers[i].id}" value="1"></td>
</tr>
{/section}
<tr>
    <td colspan="2"><input type="text" name="new_popdown_handler"></td>
</tr>
</table><br>
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>
{include file='footer.tpl'}