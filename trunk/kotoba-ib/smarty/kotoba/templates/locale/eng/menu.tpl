{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of imageboard main page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $style_name - stylesheet name.
    $ib_name - name of imageboard (see config.default).
    $categories - categories.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET
         page_title='Menu'}

<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script type="text/javascript"  src="{$DIR_PATH}/protoaculous-compressed.js"></script>
<script type="text/javascript"  src="{$DIR_PATH}/kotoba.js"></script>
<style type="text/css" media="all">
    @import url({$DIR_PATH}/css/{$style_name}.css/{$style_name}_menu.css);
</style>
<h1>{$ib_name}</h1>
<ul>
<li><a href="{$DIR_PATH}/news.php" target="main">Home</a></li>
<li id="removeframes"><a href="#" onclick="javascript:return menu_removeframes('Frames removed.');" target="_self">[Remove frames]</a></li>
</ul>
{foreach $categories as $category}
<h2><span class="plus" onclick="menu_toggle(this, '{$category.id}');" title="Click to show/hide.">&minus;</span>&nbsp;{$category.name}</h2>
<div id="{$category.id}" style="">
<ul>
    <!-- boardlink is a fake class to endentify this links. Used in js function menu_removeframes-->
    {foreach $category.boards as $board}
    <li><a href="{if isset($board.url)}{$board.url}{else}{$DIR_PATH}/{$board.name}/{/if}" class="boardlink" target="main">{$board.title} - /{$board.name}/</a></li>
    {/foreach}
</ul>
</div>
{/foreach}
{include file='footer.tpl'}
