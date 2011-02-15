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
    $upload_types - upload types.
    $board_upload_types - board upload types relations.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Edit board upload types relations"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Edit board upload types relations</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_board_upload_types.php" method="post">
<table border="1">
<tr>
    <td colspan="3">Mark relations to delete it. Select board name and type to add new relation.</td>
</tr>
{section name=i loop=$board_upload_types}
<tr>
    <td>
        {section name=j loop=$boards}
            {if $boards[j].id == $board_upload_types[i].board}{$boards[j].name}{/if}
        {/section}
    </td>
    <td>
        {section name=k loop=$upload_types}
            {if $upload_types[k].id == $board_upload_types[i].upload_type}{$upload_types[k].extension}{/if}
        {/section}
    </td>
    <td>
        <input type="checkbox" name="delete_{$board_upload_types[i].board}_{$board_upload_types[i].upload_type}" value="1">
    </td>
</tr>
{/section}
<tr>
    <td>
        <select name="new_bind_board">
            <option value="" selected></option>

            {section name=m loop=$boards}
            <option value="{$boards[m].id}">{$boards[m].name}</option>

            {/section}
        </select>
    </td>
    <td colspan="2">
        <select name="new_bind_upload_type">
            <option value="" selected></option>

            {section name=n loop=$upload_types}
            <option value="{$upload_types[n].id}">{$upload_types[n].extension}</option>

            {/section}
        </select>
    </td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>
{include file='footer.tpl'}