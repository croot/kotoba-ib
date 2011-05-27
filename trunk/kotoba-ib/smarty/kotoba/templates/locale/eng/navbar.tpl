{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Navigation bar.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $INVISIBLE_BOARDS - list of board names what will not shown in navigation bar. (see config.default).
    $categories - categories.
    $boards - boards.
*}
<div class="navbar">
{if isset($categories)}{strip}
    {foreach from=$categories item=c name=categories}
        [&nbsp;{$c.name}:&nbsp;
        {foreach from=$c.boards item=b name=boards}
            <a href="{$DIR_PATH}/{$b.name}/">{$b.name}</a>&nbsp;
            {if !$smarty.foreach.boards.last}/&nbsp;{/if}
        {/foreach}
        ]&nbsp;
    {/foreach}
{/strip}{else}
This script uses obsolete call of navbar template.
{assign var="category" value=""}
{assign var="count" value=0}
{section name=i loop=$boards}
{if !isset($INVISIBLE_BOARDS) || !in_array($boards[i].name, $INVISIBLE_BOARDS)}
{if $category != $boards[i].category_name}{if $count > 0}]
{/if}
[{assign var="category" value=$boards[i].category_name} {$category}: {else} / {/if}
<a href="{$DIR_PATH}/{$boards[i].name}/">{$boards[i].name}</a>{math equation="c+1" c=$count assign=count}{/if}{/section}
{if $count > 0} ]
{/if}
{/if}[<a href="{$DIR_PATH}/">Home</a>]
</div>
