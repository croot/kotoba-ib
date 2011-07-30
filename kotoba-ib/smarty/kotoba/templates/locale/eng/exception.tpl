{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of exception page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see
                config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $ib_name - imageboard name.
    $title - Exception title.
    $image - Exception image.
    $text - Exception text.
    $debug_info - Information about exception.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET
         page_title='Exception'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

<div style="display: block;" class="logo">{$ib_name}</div>
<hr>
<br>
<div style="display: block;" class="replymode">{$title}</div>
<p align="center">
    <img src="{$image}" alt="{$image}" />
    <br>
    {$text}
</p>
<pre>{$debug_info}</pre>
<hr>
<div class="footer" style="clear: both;">
    - <a href="http://code.google.com/p/kotoba-ib/" target="_top">Kotoba 1.2</a> -
</div>
{include file='footer.tpl'}