{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of archive thread.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $ib_name - imageboard name  (see config.default).
    $board - board.
    $thread - thread.
    $original_post_html - html code of original post.
    $simple_posts_html - html code of simple posts.
*}

{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Archive thread `$thread.original_post`"}

<div class="logo">{$ib_name} — /{$board.name}/{$thread.original_post}</div>
<hr>
<div>
{$original_post_html}
    <div>
{if $simple_posts_html}{$simple_posts_html}{else}        <!-- There is no replies -->{/if}

    </div>
    <span style="float: right;">&#91;<a href="{$DIR_PATH}/{$board.name}/">Back</a>&#93;</span>

</div>
<br clear="left">
<hr>
<div class="footer" style="clear: both;">- <a href="http://code.google.com/p/kotoba-ib/" target="_top">Kotoba 1.3</a> -</div>
{include file='footer.tpl'}