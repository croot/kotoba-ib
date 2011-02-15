{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit stylesheets page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $stylesheets - stylesheets.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Edit stylesheets"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Edit stylesheets</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_stylesheets.php" method="post">
<table border="1">
<tr>
    <td colspan="2">Input name of new stylesheet to add. Mark stylesheets to delete.</td>
</tr>
{section name=i loop=$stylesheets}
<tr><td>{$stylesheets[i].name}</td><td><input type="checkbox" name="delete_{$stylesheets[i].id}" value="1"></td></tr>{/section}

<tr>
    <td colspan="2"><input type="text" name="new_stylesheet"></td>
</tr>
</table>
<br>
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>
{include file='footer.tpl'}