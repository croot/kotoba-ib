{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of thread.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $original_post_html - html code of original post.
    $simple_posts_html - html code of simple posts.
    $is_board_view -
    $board - board.
*}
<div>
{$original_post_html}
    <div>
{if $simple_posts_html}{$simple_posts_html}{else}        <!-- There is no replies -->{/if}

    </div>
    {if !$is_board_view}<span style="float: right;">&#91;<a href="{$DIR_PATH}/{$board.name}/">Back</a>&#93;</span>{/if}

</div>
<br clear="left">
<hr>