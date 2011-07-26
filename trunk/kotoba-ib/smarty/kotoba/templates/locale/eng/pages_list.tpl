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
    $page - current page.
*}
{assign var=debug value=false}
{php}
    global $pages;
    $this->assign('pages_count', count($pages));
{/php}

{assign var=hide_range1_beg value=5}
{assign var=hide_range1_end value=`$page-3`}

{if $page <= 4}
    {assign var=hide_range2_beg value=5}
{else}
    {assign var=hide_range2_beg value=`$page+1`}
{/if}
{assign var=hide_range2_end value=`$pages_count-2`}

{if $hide_range1_beg > $hide_range1_end}
    {php}$this->clear_assign('hide_range1_beg', 'hide_range1_end');{/php}
{/if}

{if $hide_range2_beg > $hide_range2_end}
    {php}$this->clear_assign('hide_range2_beg', 'hide_range2_end');{/php}
{/if}

{if isset($debug) && $debug && isset($hide_range1_beg)}
    Range 1: [{$hide_range1_beg}, {$hide_range1_end}]
{/if}
{if isset($debug) && $debug && isset($hide_range2_beg)}
    Range 2: [{$hide_range2_beg}, {$hide_range2_end}]
{/if}

<div class="boardpages">Pages:
{section name=i loop=$pages}
{if $pages[i] == $page} ({$pages[i]})
{else}
 <a href="{$DIR_PATH}/{$board_name}/p{$pages[i]}">({$pages[i]})</a>
{/if}
{/section}</div>