{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Admin bar.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $show_control - show link to manage page.
*}
<div class="adminbar">
[<a href="{$DIR_PATH}/edit_settings.php">Settings</a>]
[<a href="{$DIR_PATH}/search.php">Search</a>]
{if $show_control}[<a href="{$DIR_PATH}/manage.php">Manage</a>]
{/if}
</div>