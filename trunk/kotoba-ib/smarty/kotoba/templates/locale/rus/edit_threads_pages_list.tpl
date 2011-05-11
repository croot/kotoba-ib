{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of list of pages.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $pages - pages.
    $page - current page.
*}
<div class="boardpages">Страницы:
{section name=i loop=$pages}
{if $pages[i] == $page} ({$pages[i]})
{else}
 <a href="{$DIR_PATH}/admin/edit_threads.php?page={$pages[i]}">({$pages[i]})</a>
{/if}
{/section}</div>