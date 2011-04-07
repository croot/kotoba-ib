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
    $filter - posts filter.
*}
<div class="boardpages">Pages:
{section name=i loop=$pages}
{if $pages[i] == $page} ({$pages[i]})
{else}
 <a href="{$DIR_PATH}/admin/moderate.php?page={$pages[i]}&filter[board]={$filter.board}&filter[date_time]={$filter.date_time}&filter[number]={$filter.number}&filter[ip]={$filter.ip}">({$pages[i]})</a>
{/if}
{/section}</div>