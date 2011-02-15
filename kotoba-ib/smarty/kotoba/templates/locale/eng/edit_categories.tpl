{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit categories page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $categories - categories.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Edit categories"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Edit categories</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_categories.php" method="post">
<table border="1">
<tr>
    <td colspan="2">Enter new category name to add it. Mark categories to delete.</td>
</tr>
{section name=i loop=$categories}
<tr>
    <td>{$categories[i].name}</td><td><input type="checkbox" name="delete_{$categories[i].id}" value="1"></td>
</tr>
{/section}
<tr>
    <td colspan="2"><input type="text" name="new_category"></td>
</tr>
</table><br>
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>
{include file='footer.tpl'}