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
    $prev_filter_board - previous value of board filter between two script calls.
*}
<div class="boardpages">Pages:
{section name=i loop=$pages}
{if $pages[i] == $page} ({$pages[i]})
{else}
 <a href="{$DIR_PATH}/admin/reports.php?filter=1&page={$pages[i]}&bf={$prev_filter_board}">({$pages[i]})</a>
{/if}
{/section}</div>