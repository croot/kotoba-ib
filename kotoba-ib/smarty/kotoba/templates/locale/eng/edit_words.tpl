{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit word filter page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $words - words.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Word filter"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Word filter</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_words.php" method="post">
<table border="1">
<tr>
    <td colspan="4">To add word input requied attributes.
    To edit attributed edit it in the table. To delete word mark it.</td>
</tr>
<tr>
    <td>Board</td>
    <td>Word</td>
    <td>Replacement</td>
    <td>Delete word</td>
</tr>
{section name=i loop=$words}
    <tr>
        <td>{section name=j loop=$boards}{if $boards[j].id == $words[i].board_id}{$boards[j].name}{/if}{/section}</td>
        <td><input type="text" name="word_{$words[i].id}" value="{$words[i].word}"></td>
        <td><input type="text" name="replace_{$words[i].id}" value="{$words[i].replace}"></td>
        <td><input type="checkbox" name="delete_{$words[i].id}" value="1"></td>
    </tr>
{/section}
<tr>
    <td>
        <select name="new_bind_board">
            <option value="" selected></option>
            {section name=m loop=$boards}
            <option value="{$boards[m].id}">{$boards[m].name}</option>{/section}

        </select>
    </td>
    <td><input type="text" name="new_word" value=""></td>
    <td colspan="2"><input type="text" name="new_replace" value=""></td>
</tr>
</table>
<br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>
{include file='footer.tpl'}