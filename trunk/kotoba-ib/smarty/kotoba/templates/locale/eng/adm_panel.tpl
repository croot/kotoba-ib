{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of administrator panel.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
*}
<ul>
<li><a href="{$DIR_PATH}/admin/moderate.php">Main moderators script.</a></li>
<li><a href="{$DIR_PATH}/admin/reports.php">Reports.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_bans.php">Bans.</a></li>
<li><a href="{$DIR_PATH}/admin/hard_ban.php">Ban on firewall.</a></li>
<li><a href="{$DIR_PATH}/admin/mass_ban.php">Mass bans.</a></li>
</ul>
Boards:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_boards.php">Boards.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_categories.php">Categories.</a></li>
</ul>
Threads:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_threads.php">Threads.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_popdown_handlers.php">Popdown handlers.</a></li>
<li><a href="{$DIR_PATH}/admin/move_thread.php">Move thread.</a></li>
</ul>
Attachments:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_upload_types.php">Upload types.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_board_upload_types.php">Boards upload types relations.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_upload_handlers.php">Upload handlers.</a></li>
<li><a href="{$DIR_PATH}/admin/delete_dangling_attachments.php">Delete dangling attachments.</a></li>
<li><a href="{$DIR_PATH}/admin/update_macrochan.php">Update macrochan data.</a></li>
</ul>
Users & Groups:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_groups.php">User groups.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_user_groups.php">Users groups relations.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_acl.php">Access Control List.</a></li>
</ul>
Delete & Archive:
<ul>
<li><a href="{$DIR_PATH}/admin/archive.php">Archive marked threads.</a></li>
<li><a href="{$DIR_PATH}/admin/delete_marked.php">Delete marked threads.</a></li>
</ul>
Other:
<ul>
<li><a href="{$DIR_PATH}/admin/edit_stylesheets.php">Styles.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_languages.php">Languages.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_words.php">Word filter.</a></li>
<li><a href="{$DIR_PATH}/admin/edit_spamfilter.php">Spam filter.</a></li>
<li><a href="{$DIR_PATH}/admin/log_view.php">View log.</a></li>
</ul>