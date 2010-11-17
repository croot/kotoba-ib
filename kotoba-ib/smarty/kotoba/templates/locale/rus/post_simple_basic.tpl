{* Smarty *}
{*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************
 *********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Код основы обычного сообщения.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $post - Сообщение.
    $board - Доска.
    $thread - Нить.
*}
<table>
    <tbody>
        <tr>
            <td class="doubledash"> &gt;&gt; </td>
            <td class="reply">
                {block name=anchor}{/block}
                {block name=remove_post}{/block}
                {block name=extrabtns}{/block}
                {block name=geoip}{/block}
                <span class="filetitle"> {$post.subject} </span>
                <span class="postername"> {$post.name}</span>{if $post.tripcode != null}<span class="postertrip">!{$post.tripcode} </span>{/if}
                {block name=author_admin}{/block}
                {$post.date_time}
                <span class="reflink">
                    <a href="{$DIR_PATH}/{$board.name}/{$thread.original_post}#{$post.number}">#</a>
                    {block name=post_number}{/block}
                {block name=postid}{/block}
                {block name=mod_mini_panel}{/block}
                <br/>
                {block name=attachment}{/block}
                <blockquote id="post{$post.number}">
                    {$post.text}<br/>
                    {if $post.text_cutted == 1}<div class="abbrev">Нажмите "Ответ" для просмотра сообщения целиком.</div>{/if}</blockquote>
                {block name=translation}{/block}
            </td>
        </tr>
    </tbody>
</table>
