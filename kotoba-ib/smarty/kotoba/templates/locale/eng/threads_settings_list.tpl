{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit thread settings form.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $boards - boards.
    $threads - threads.
*}
<form action="{$DIR_PATH}/admin/edit_threads.php" method="post">
<table border="1">
<tr><td colspan="8">Change settings and save.</td></tr>
<tr>
    <td>Id</td>
    <td>Board</td>
    <td>Number</td>
    <td>Specific bumplimit</td>
    <td>Sticky</td>
    <td>Sage</td>
    <td>With attachments</td>
    <td>Closed</td>
</tr>
{section name=i loop=$threads}
<tr>
    <td>{$threads[i].id}</td>
    <td>{$threads[i].board.name}</td>
    <td>{$threads[i].original_post}</td>
    <td><input type="text" name="bump_limit_{$threads[i].id}" value="{$threads[i].bump_limit}"></td>
    <td><input type="checkbox" name="sticky_{$threads[i].id}" value="1"{if $threads[i].sticky} checked{/if}></td>
    <td><input type="checkbox" name="sage_{$threads[i].id}" value="1"{if $threads[i].sage} checked{/if}></td>
    <td>
        <select name="with_attachments_{$threads[i].id}">
            <option value=""{if $threads[i].with_attachments === null} selected{/if}>Inherit</option>
            <option value="1"{if $threads[i].with_attachments == '1'} selected{/if}>Up</option>
            <option value="0"{if $threads[i].with_attachments == '0'} selected{/if}>Down</option>
        </select>
    </td>
    <td>
        <select name="closed_{$threads[i].id}">
            <option value="1"{if $threads[i].closed == '1'} selected{/if}>Closed</option>
            <option value="0"{if $threads[i].closed == '0'} selected{/if}>Open</option>
        </select>
    </td>
</tr>
{/section}
</table>
<br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>