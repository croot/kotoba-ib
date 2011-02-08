{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Pages list on search page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $pages - pages numbers.
    $page - page number.
    $keyword - keyword.
    $boards - boards.
*}
{if isset($pages) && isset($page) && count($pages) > 0}<div class="boardpages">Pages: {section name=i loop=$pages}
{if $pages[i] == $page} ({$pages[i]}){else} <a href="{$DIR_PATH}/search.php?search[page]={$pages[i]}&search[keyword]={$keyword}{section name=j loop=$boards}{if isset($boards[j].selected)}&search[boards][]={$boards[j].id}{/if}{/section}">({$pages[i]})</a>{/if}{/section}
</div>{/if}