{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of move thread page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Move thread'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Move thread</div>
<form action="{$DIR_PATH}/admin/move_thread.php" method="post">
<table border="1">
<tr>
    <td colspan="3">To move the thread select board and thread number. Then select board where thread move.</td>
</tr>
<tr>
    <td><select name="src_board">
        <option value="" selected></option>
    {section name=m loop=$boards}
        <option value="{$boards[m].id}">{$boards[m].name}</option>

    {/section}</td>
    <td><input type="text" name="thread" value=""></td>
    <td><select name="dst_board">
        <option value="" selected></option>
    {section name=m loop=$boards}
        <option value="{$boards[m].id}">{$boards[m].name}</option>

    {/section}</td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Reset"> <input type="submit" value="Move">
</form>
{include file='footer.tpl'}