{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of simple message.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $ATTACHMENT_TYPE_FILE - attachment type is file (see config.default).
    $ATTACHMENT_TYPE_LINK - attachment type is link (see config.default).
    $ATTACHMENT_TYPE_VIDEO - attachment type is video (see config.default).
    $ATTACHMENT_TYPE_IMAGE - attachment type is image (see config.default).
    $enable_doubledash - Show >> near post.
    $post - Simple post.
    $enable_remove_post - Show remove post link.
    $enable_anchor - Enable anchor.
    $enable_extrabtns - Show extra buttons.
    $enable_geoip - GeoIP flag (see config.default).
    $country - GeoIP data if GeoIP flag up.
    $author_admin - Author of this post is admin.
    $is_board_view -
    $enable_postid - Post identification flag.
    $postid - Post identifer if post identification flag up.
    $is_admin - Current user is admin.
    $attachments - Attachments.
    $enable_translation - Translation flag. (see config.default).
*}
<table>
    <tbody>
        <tr>
            {if $enable_doubledash}<td class="doubledash"> &gt;&gt; </td>{else}<!-- Doubledash disabled -->{/if}

            <td class="reply">
                {if $enable_anchor}<a name="{$post.number}"></a>{else}<!-- Anchor disabled -->{/if}

                {if $enable_remove_post}<span><a href="{$DIR_PATH}/remove_post.php?post={$post.id}"><img src="{$DIR_PATH}/css/delete.png" alt="[Удалить]" title="Удалить сообщение" border="0"/></a></span>{else}<!-- Link to remove post disabled -->{/if}

                {if $enable_extrabtns}<span class="extrabtns">
                    {if $post.with_attachments}<a href="{$DIR_PATH}/remove_upload.php?post={$post.id}"><img src="{$DIR_PATH}/css/delfile.png" alt="[Удалить файл]" title="Удалить файл" border="0"/></a>{else}<!-- There is no attachments -->{/if}

                    <a href="{$DIR_PATH}/report.php?post={$post.id}"><img src="{$DIR_PATH}/css/report.png" alt="[Пожаловаться]" title="Пожаловаться на сообщение" border="0"/></a>
                </span>{else}<!-- Extrabuttons disabled -->{/if}

                {if $enable_geoip}<span title="{$country.name}" class="country"><img src="http://410chan.ru/css/flags/{$country.code}.gif" alt="{$country.name}"></span>&nbsp;{else}<!-- GeoIP disabled -->{/if}

                <span class="filetitle">{$post.subject}</span>
                <span class="postername">{$post.name}</span>
                {if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{else}<!-- There is no tripcode -->{/if} {if $author_admin}<span class="admin">❀❀&nbsp;Админ&nbsp;❀❀</span>{else}<!-- Author is not admin -->{/if}

                {$post.date_time}
                <span class="reflink">
                    <a href="{$DIR_PATH}/{$post.board.name}/{$post.thread.original_post}#{$post.number}">#</a>
                    {if $is_board_view}<a href="{$DIR_PATH}/threads.php?board={$post.board.name}&thread={$post.thread.original_post}&quote={$post.number}">{$post.number}</a>{else}<a href="#" onclick="insert('>>{$post.number}');">{$post.number}</a>{/if}

                </span>
                {if $enable_postid} ID:{$postid}{else}<!-- Post identification disabled -->{/if}

                {if $is_admin}{include file='mod_mini_panel.tpl' post_id=$post.id ip=$post.ip board_name=$post.board.name post_num=$post.number}{else}<!-- Admin panel disabled -->{/if}

                <br>
{if $post.with_attachments}
{if $attachments[0].deleted}
                <br>
                <a target="_blank" href="{$DIR_PATH}/img/deleted.png">
                    <img src="{$DIR_PATH}/img/deleted.png" class="thumb" width="200" height="200">
                </a>
{else}
{if $attachments[0].attachment_type == $ATTACHMENT_TYPE_FILE}
                <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].file_link}">{$attachments[0].name}</a>-({$attachments[0].size} Байт)</span>
                <br>
                <a target="_blank" href="{$attachments[0].file_link}">
                    <img src="{$attachments[0].thumbnail_link}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}">
                </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_IMAGE}
                <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].image_link}">{$attachments[0].name}</a>-({$attachments[0].size} Байт, {$attachments[0].widht}x{$attachments[0].height})</span>
                <br>
                <a target="_blank" href="{$attachments[0].image_link}">
                    <img src="{if $attachments[0].spoiler}{$DIR_PATH}/img/spoiler.png{else}{$attachments[0].thumbnail_link}{/if}" class="thumb"{if !$attachments[0].spoiler} width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}{/if}">
                </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_LINK}
                <span class="filesize">Файл: <a target="_blank" href="{$attachments[0].url}">{$attachments[0].url}</a>-({$attachments[0].size} Байт, {$attachments[0].widht}x{$attachments[0].height})</span>
                <br>
                <a target="_blank" href="{$attachments[0].url}">
                    <img src="{$attachments[0].thumbnail}" class="thumb" width="{$attachments[0].thumbnail_w}" height="{$attachments[0].thumbnail_h}">
                </a>
{elseif $attachments[0].attachment_type == $ATTACHMENT_TYPE_VIDEO}
                <br>
                <br>{$attachments[0].video_link}
{else}
                <!-- Unknown attachment type :o -->
{/if}
{/if}
{else}
                <!-- There is no attachments -->
{/if}
                <blockquote id="post{$post.number}">
{$post.text}
                    {if $post.text_cutted == 1}<div class="abbrev">Нажмите "Ответ" для просмотра сообщения целиком.</div>{else}<!-- Text not cutted -->{/if}

                </blockquote>
                {if $enable_translation && $post.text}<blockquote id="translation{$post.number}"></blockquote><a href="#" onclick="javascript:translate('{$post.number}'); return false;">Lolšto?</a>{else}<!-- Translation disabled or empty message -->{/if}

            </td>
        </tr>
    </tbody>
</table>