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
    $boards - boards.
    $threads_html - html code of catalog entries.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Threads catalog /`$board.name`/ `$board.title`"}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<br>
<br>
<div style="float: left;">
{$threads_html}
</div>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="footer" style="clear: both;">- <a href="http://code.google.com/p/kotoba-ib/" target="_top">Kotoba 1.2</a> -</div>
{include file='footer.tpl'}