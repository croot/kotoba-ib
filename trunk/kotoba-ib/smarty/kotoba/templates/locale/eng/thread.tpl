{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of thread.

Variables:
    $original_post_html -
    $simple_posts_html -
*}
<div>
{$original_post_html}
    <div>
{if $simple_posts_html}{$simple_posts_html}{else}        <!-- There is no replies -->{/if}

    </div>
</div>
<br clear="left">
<hr>