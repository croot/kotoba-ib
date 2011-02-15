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
    $bans_decoded - bans.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Edit bans'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Edit bans</div>
<hr>
<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
<table border="1">
<tr>
    <td colspan="5">To ban enter parameters. To unban mark ban.</td>
</tr>
<tr>
    <td>Begin of range</td><td>End of range</td><td>Reason</td><td>Expiration</td><td>Delete ban</td>
</tr>
{section name=i loop=$bans_decoded}
<tr>
    <td>{$bans_decoded[i].range_beg}</td>
    <td>{$bans_decoded[i].range_end}</td>
    <td>{$bans_decoded[i].reason}</td>
    <td>{$bans_decoded[i].untill}</td>
    <td><input type="checkbox" name="delete_{$bans_decoded[i].id}"></td>
</tr>
{/section}
<tr>
    <td><input type="text" name="new_range_beg"></td>
    <td><input type="text" name="new_range_end"></td>
    <td><input type="text" name="new_reason"></td>
    <td colspan="2">
        <select name="new_untill">
            <option value="10">10 sec</option>
            <option value="30">30 sec</option>
            <option value="60">1 min</option>
            <option value="900">15 min</option>
            <option value="1800">30 min</option>
            <option value="3600">1 hour</option>
            <option value="86400">1 day</option>
            <option value="604800">1 week</option>
            <option value="18144000">1 month (30 days)</option>
        </select>
    </td>
</tr>
</table>
<br>
Unban ip: <input type="text" name="unban"><br><br>
<input type="reset" value="Reset"> <input type="submit" name="submit" value="Save">
</form>
{include file='footer.tpl'}