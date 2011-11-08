{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of threads catalog page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $categories - categories.
    $boards - boards.
    $threads_html - html code of catalog entries.
    $page - page number.
    $pages - page numbers.
    $board - board.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Просмотр нитей доски /`$board.name`/ `$board.title`"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH categories=$categories boards=$boards}

<br>
<br>
{include file='catalog_pages_list.tpl' pages=$pages page=$page board=$board}

<div style="float: left;">
{$threads_html}
</div>
<br clear="left">
<br>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH categories=$categories boards=$boards}

<div class="footer" style="clear: both;">- <a href="http://code.google.com/p/kotoba-ib/" target="_top">Kotoba 1.3</a> -</div>
{include file='footer.tpl'}