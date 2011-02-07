{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of moderator panel.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
*}
<ul>
<li><a href="{$DIR_PATH}/admin/moderate.php">Main moderators script.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_bans.php">Bans.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_threads.php">Threads.</a></li>
</ul>