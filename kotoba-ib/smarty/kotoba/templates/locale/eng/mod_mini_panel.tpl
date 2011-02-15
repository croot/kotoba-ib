{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Administrators and Moderators mini panel.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $post_id - post id.
    $ip - IP-address.
*}
<span class="postadmin">[<span class="posterip">{$ip}</span>
<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
    <input type="submit" name="submit" value="B" title="Ban">
    <input type="hidden" name="new_range_beg" value="{$ip}">
    <input type="hidden" name="new_range_end" value="{$ip}">
    <input type="hidden" name="new_reason" value="">
    <input type="hidden" name="new_untill" value="30">
</form>
<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
    <input type="submit" name="submit" value="BT" title="Ban and add text">
    <input type="hidden" name="new_range_beg" value="{$ip}">
    <input type="hidden" name="new_range_end" value="{$ip}">
    <input type="hidden" name="new_reason" value="">
    <input type="hidden" name="new_untill" value="30">
    <input type="hidden" name="add_text" value="1">
    <input type="hidden" name="post" value="{$post_id}">
</form>
<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
    <input type="submit" name="submit" value="BD" title="Ban and delete post">
    <input type="hidden" name="new_range_beg" value="{$ip}">
    <input type="hidden" name="new_range_end" value="{$ip}">
    <input type="hidden" name="new_reason" value="">
    <input type="hidden" name="new_untill" value="30">
    <input type="hidden" name="del_post" value="1">
    <input type="hidden" name="post" value="{$post_id}">
</form>
<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
    <input type="submit" name="submit" value="BL" title="Ban and delete last posts">
    <input type="hidden" name="new_range_beg" value="{$ip}">
    <input type="hidden" name="new_range_end" value="{$ip}">
    <input type="hidden" name="new_reason" value="">
    <input type="hidden" name="new_untill" value="30">
    <input type="hidden" name="del_all" value="1">
    <input type="hidden" name="post" value="{$post_id}">
</form>
<form action="{$DIR_PATH}/admin/hard_ban.php" method="post">
    <input type="submit" name="submit" value="BF" title="Ban in firewall">
    <input type="hidden" name="new_range_beg" value="{$ip}">
    <input type="hidden" name="new_range_end" value="{$ip}">
</form>]
</span>