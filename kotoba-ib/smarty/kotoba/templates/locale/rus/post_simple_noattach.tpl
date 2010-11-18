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
Код обычного сообщения без вложений.

$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
$post - Сообщение.
$country - Информация о местонахождении отправителя.
$enable_geoip - Включение отображения страны автора сообщения. (см. config.default).
$author_admin - Сообщение было оставлено администратором.
$is_board_view - Сообщение выводится при просмотре доски.
$enable_postid - Включение идентификатора сообщения. (см. config.default).
$is_admin - Сообщение просматривает администратор.
$enable_translation - Включение перевода текста сообщения (см. config.default).
*}
{extends file="post_simple_basic.tpl"}

{block name=doubledash}<td class="doubledash"> &gt;&gt; </td>{/block}

{block name=anchor}<a name="{$post.number}"></a>{/block}

{block name=remove_post}<span> <a href="{$DIR_PATH}/remove_post.php?post={$post.id}"><img src="{$DIR_PATH}/css/delete.png" alt="[Удалить]" title="Удалить сообщение"/></a> </span>{/block}

{block name=extrabtns}<span class="extrabtns"> <a href="{$DIR_PATH}/report.php?post={$post.id}"><img src="{$DIR_PATH}/css/report.png" alt="[Пожаловаться]" title="Пожаловаться на сообщение"/></a> </span>{/block}

{block name=geoip}{if $enable_geoip}<span title="{$country.name}" class="country"> <img src="http://410chan.ru/css/flags/{$country.code}.gif" alt="{$country.name}"> </span>{else}<!-- GeoIP disabled. -->{/if}
{/block}

{block name=subject}<span class="filetitle"> {$post.subject} </span>{/block}

{block name=postername}<span class="postername"> {$post.name}</span>{if $post.tripcode != null}<span class="postertrip">!{$post.tripcode} </span>{else}<!-- There is no tripcode. -->{/if}
{/block}

{block name=author_admin}{if $author_admin}<span class="admin"> Админ </span>{else}<!-- Author is not admin. -->{/if}
{/block}

{block name=date_time}{$post.date_time}{/block}

{block name=reflink}<span class="reflink"><a href="{$DIR_PATH}/{$post.board.name}/{$post.thread.original_post}#{$post.number}">#</a>
                {if $is_board_view}<a href="{$DIR_PATH}/threads.php?board={$post.board.name}&thread={$post.thread.original_post}&quote={$post.number}">{$post.number}</a>
                {else}<a href="#" onclick="insert('>>{$post.number}');">{$post.number}</a>{/if}</span>{/block}

{block name=postid}{if $enable_postid} ID:{$postid} {else}<!-- PostID disabled. -->{/if}
{/block}

{block name=mod_mini_panel}{if $is_admin}{include file='mod_mini_panel.tpl' post=$post}{else}<!-- You are not admin. -->{/if}
{/block}

{block name=text}<blockquote id="post{$post.number}">
                    {$post.text}
                    {if $post.text_cutted}
                    <div class="abbrev">Нажмите "Ответ" для просмотра сообщения целиком.</div>
                    {else}<!-- Text is not cutted. -->{/if}</blockquote>{/block}

{block name=translation}{if $enable_translation && $post.text}<blockquote id="translation{$post.number}"></blockquote><a href="#" onclick="javascript:translate('{$post.number}'); return false;">Lolšto?</a>{else}<!-- Translation disabled or text is empty. -->{/if}
{/block}
