{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of error page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $ib_name - imageboard name.
    $error - Error data.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET
         page_title='Ошибка'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

<div style="display: block;" class="logo">{$ib_name}</div>
<hr>
<br>
<div style="display: block;" class="replymode">{$error.title}</div>
<p align="center">
    <img src="{$error.image}" alt="{$error.title}" />
    <br>
    {$error.text}
</p>
<hr>
<div class="footer" style="clear: both;">
    - <a href="http://code.google.com/p/kotoba-ib/" target="_top">Kotoba 1.2</a> -
</div>
{include file='footer.tpl'}