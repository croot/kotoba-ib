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
    $groups - groups.
    $acl - ACL.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Edit ACL'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Edit ACL</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_acl.php" method="post">
<table border="1">
<tr>
    <td colspan="8">To change existed rule edit permission and Save changes.<br>
    To delete rule mark it and Save changes.<br>
    To create new rule enter group and/or board and/or thread and/or post id, edit permission and Save changes.</td>
</tr>
<tr>
    <td>Group</td>
    <td>Board</td>
    <td>Thread</td>
    <td>Post</td>
    <td>View</td>
    <td>Change</td>
    <td>Moderate</td>
    <td>Delete rule</td>
</tr>
{section name=i loop=$acl}
<tr>
    <td>
    {if $acl[i].group === null}
        &nbsp;
    {else}
        {section name=j loop=$groups}
            {if $acl[i].group == $groups[j].id}{$groups[j].name}{/if}
        {/section}
    {/if}
    </td>
    <td>
    {if $acl[i].board === null}
        &nbsp;
    {else}
        {section name=j loop=$boards}
            {if $acl[i].board == $boards[j].id}{$boards[j].name}{/if}
        {/section}
    {/if}
    </td>
    <td>
    {if $acl[i].thread === null}
        &nbsp;
    {else}
        {$acl[i].thread}
    {/if}
    </td>
    <td>
    {if $acl[i].post === null}
        &nbsp;
    {else}
        {$acl[i].post}
    {/if}
    </td>
    <td>
    <input type="checkbox" name="view_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1"{if $acl[i].view == 1} checked{/if}>
    </td>
    <td>
    <input type="checkbox" name="change_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1"{if $acl[i].change == 1} checked{/if}>
    </td>
    <td>
    <input type="checkbox" name="moderate_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1"{if $acl[i].moderate == 1} checked{/if}>
    </td>
    <td>
    <input type="checkbox" name="delete_{$acl[i].group}_{$acl[i].board}_{$acl[i].thread}_{$acl[i].post}" value="1">
    </td>
</tr>
{/section}
<tr>
    <td>
        <select name="new_group">
            <option value="" selected></option>
        {section name=i loop=$groups}
            <option value="{$groups[i].id}">{$groups[i].name}</option>

        {/section}
        </select>
    </td>
    <td>
        <select name="new_board">
            <option value="" selected></option>
        {section name=i loop=$boards}
            <option value="{$boards[i].id}">{$boards[i].name}</option>

        {/section}
        </select>
    </td>
    <td><input type="text" name="new_thread"></td>
    <td><input type="text" name="new_post"></td>
    <td><input type="checkbox" name="new_view" value="1"></td>
    <td><input type="checkbox" name="new_change" value="1"></td>
    <td colspan="2"><input type="checkbox" name="new_moderate" value="1"></td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>
{include file='footer.tpl'}