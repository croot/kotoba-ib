{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of post on search page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $post - post.
    $enable_geoip - GeoIP flag (see config.default).
    $country - GeoIP data if GeoIP flag up.
    $author_admin - Author of this post is admin.
    $enable_postid - Post identification flag.
    $postid - Post identifer if post identification flag up.
    $enable_translation - Translation flag. (see config.default).
*}
<hr/>
<table>
    <tbody>
        <tr>
            <td class="reply">
                <a name="{$post.number}"></a>
                {if $enable_geoip}<span title="{$country.name}" class="country"><img src="http://410chan.ru/css/flags/{$country.code}.gif" alt="{$country.name}"></span>&nbsp;{else}<!-- GeoIP disabled -->{/if}

                <span class="filetitle">{$post.subject}</span>
                <span class="postername">{$post.name}</span>
                {if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{else}<!-- There is no tripcode -->{/if} {if $author_admin}<span class="admin">❀❀&nbsp;Admin&nbsp;❀❀</span>{else}<!-- Author is not admin -->{/if}

                {$post.date_time}
                <span class="reflink">
                    <a href="{$DIR_PATH}/{$post.board.name}/{$post.thread.original_post}#{$post.number}">#</a>
                    <a href="{$DIR_PATH}/threads.php?board={$post.board.name}&thread={$post.thread.original_post}&quote={$post.number}">{$post.number}</a>
                </span>
                {if $enable_postid} ID:{$postid}{else}<!-- Post identification disabled -->{/if}

                <br>
                <blockquote id="post{$post.number}">
{$post.text}
                </blockquote>
                {if $enable_translation && $post.text}<blockquote id="translation{$post.number}"></blockquote><a href="#" onclick="javascript:translate('{$post.number}'); return false;">Lolšto?</a>{else}<!-- Translation disabled or empty message -->{/if}

            </td>
        </tr>
    </tbody>
</table>