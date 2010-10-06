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
Код страницы просмотра нитей доски.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $boards - доски.
    $board - доска, на которой расположены проматриваемые нити.
    $ATTACHMENT_TYPE_FILE - тип вложения файл.
    $ATTACHMENT_TYPE_LINK - тип вложения ссылка на изображение.
    $ATTACHMENT_TYPE_VIDEO - тип вложения встроенное видео.
    $ATTACHMENT_TYPE_IMAGE - тип вложения изображение.
    $posts - оригинальные сообщения нитей доски.

Необязательные переменные:
    $posts_attachments - связи сообщений и их вложений.
    $attachments - вложения.
*}
{include file='header.tpl' page_title="Просмотр нитей доски /`$board.name`/ `$board.title`" DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<script src="{$DIR_PATH}/kotoba.js"></script>
<div class="navbar">{include file='board_list.tpl' boards=$boards DIR_PATH=$DIR_PATH} [<a href="{$DIR_PATH}/">Главная</a>]</div>

{if isset($banner)}
<div class="logo"><img src="{$DIR_PATH}/misc/img/{$banner.name}" alt="{$banner.name}" width="{$banner.widht}" height="{$banner.height}"></div>
{/if}

<br />
<br />
<div style="float: left;">
{section name=i loop=$posts}
<a href="{$DIR_PATH}/{$board.name}/{$posts[i].number}">
<table border="1" style="float: left; min-height: 150px; height: 150px; min-width: 150px; width: 150px;">
{if $board.with_attachments}
{section name=j loop=$posts_attachments}
{section name=k loop=$attachments}
{if $attachments[k].attachment_type == $ATTACHMENT_TYPE_FILE}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].file == $attachments[k].id}
    <tr><td rowspan="3"><img src="{$DIR_PATH}/img/{$attachments[k].thumbnail}" class="thumb" width="80"></td></tr>
{/if}
{elseif $attachments[k].attachment_type == $ATTACHMENT_TYPE_IMAGE}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].image == $attachments[k].id}
    <tr><td rowspan="3"><img src="{$DIR_PATH}/{$board.name}/thumb/{$attachments[k].thumbnail}" class="thumb" width="80"></td></tr>
{/if}
{elseif $attachments[k].attachment_type == $ATTACHMENT_TYPE_LINK}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].link == $attachments[k].id}
    <tr><td rowspan="3"><img src="{$attachments[k].thumbnail}" class="thumb" width="80"></td></tr>
{/if}
{elseif $attachments[k].attachment_type == $ATTACHMENT_TYPE_VIDEO}
{if $posts[i].id == $posts_attachments[j].post && $posts_attachments[j].video == $attachments[k].id}
    <tr><td rowspan="3"><br><br>
    {*include file='youtube.tpl' code=$attachments[k].code*}
    </td></tr>
{/if}
{/if}
{/section}
{/section}
{else}
    <tr><td><span class="filetitle">{$posts[i].subject}</span></td></tr>
    <tr><td><span class="postername">{$posts[i].name}</span>{if $posts[i].tripcode != null}<span class="postertrip">!{$posts[i].tripcode}</span>{/if}</td></tr>
    <tr><td>{$posts[i].date_time}</td></tr>
    {/if}
</table>
</a>
{/section}
</div>

<div class="navbar">{include file='board_list.tpl' boards=$boards DIR_PATH=$DIR_PATH} [<a href="{$DIR_PATH}/">Главная</a>]</div>
<div class="footer" style="clear: both;">- Kotoba 1.1 -</div>
{include file='footer.tpl'}