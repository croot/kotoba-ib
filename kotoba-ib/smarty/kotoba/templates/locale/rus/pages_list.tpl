{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of list of pages.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $board_name - board name.
    $pages - pages.
    $pages_count - pages count.
    $page - current page.
*}
{assign var=debug value=false}
{assign var=hide_range1_beg value=5}
{assign var=hide_range1_end value=$page-3}

{if $page <= 4}
    {assign var=hide_range2_beg value=5}
{else}
    {assign var=hide_range2_beg value=$page+1}
{/if}
{assign var=hide_range2_end value=$pages_count-2}

{if $hide_range1_beg > $hide_range1_end}
    {assign var=hide_range1_beg value=0}
    {assign var=hide_range1_end value=0}
{/if}

{if $hide_range2_beg > $hide_range2_end}
    {assign var=hide_range2_beg value=0}
    {assign var=hide_range2_end value=0}
{/if}

{if isset($debug) && $debug && $hide_range1_beg > 0}
    Range 1: [{$hide_range1_beg}, {$hide_range1_end}]
{/if}
{if isset($debug) && $debug && $hide_range2_beg > 0}
    Range 2: [{$hide_range2_beg}, {$hide_range2_end}]
{/if}

<div class="boardpages">Страницы:
{section name=i loop=$pages}
{if $hide_range1_beg > 0 && $smarty.section.i.index == $hide_range1_beg} <a onclick="toggle_display('hide_range_1')" href="#">...</a> <span id="hide_range_1" style="display:none;">{/if}
{if $hide_range2_beg > 0 && $smarty.section.i.index == $hide_range2_beg} <a onclick="toggle_display('hide_range_2')" href="#">...</a> <span id="hide_range_2" style="display:none;">{/if}
{if $pages[i] == $page} ({$pages[i]})
{else}
 <a href="{$DIR_PATH}/{$board_name}/p{$pages[i]}">({$pages[i]})</a>
{/if}
{if $hide_range1_end > 0 && $smarty.section.i.index == $hide_range1_end}</span>{/if}
{if $hide_range2_end > 0 && $smarty.section.i.index == $hide_range2_end}</span>{/if}
{/section}</div>