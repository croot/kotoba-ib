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
Этот шаблон содержит код оригинального сообщения, с которого начинается нить.

Описание переменных:
    $with_image - логическая переменная, указывает на то, содержит ли
        сообщение прикреплённую картинку или нет.
    $thread - массив обычных сообщений треда.

    $original_theme - тема сообщения.
    $original_name - имя отправителя.
    $original_time - время получения (время сервера).
    $original_file_link - ссылка на файл, прикреплённый к сообщению.
    $original_file_name - имя файла, прикреплённого к сообщению.
    $original_file_size - размер файла (в байтах), прикреплённого к сообщению.
    $original_file_width - ширина (для изображений).
    $original_file_heigth - высота (для изображений).
    $original_id - номер (он же идентификатор) сообщения.
    $original_link - ссылка на сообщение.
    $original_remove_link - ссылка для удаления сообщения.
    $original_file_thumbnail_link - ссылка на уменьшенную копию изображения или
        иконку для других типов файлов.
    $original_file_thumbnail_width - ширина уменьшенной копии (для изображений).
    $original_file_thumbnail_heigth - высота уменьшенной копии (для изображений).
    $original_text - текст сообщения.
*}
<div>
<span class="filetitle">{$original_theme}</span> <span class="postername">{$original_name}</span> {$original_time}
{if $with_image == true}<span class="filesize">Файл: <a target="_blank" href="{$original_file_link}">{$original_file_name}</a> -(<em>{$original_file_size} Байт {$original_file_width}x{$original_file_heigth}</em>)</span>
{/if}
<span class="reflink"><span onclick="insert('>>{$original_id}');">#</span> <a href="{$original_link}">{$original_id}</a></span>
<span class="delbtn">[<a href="{$original_remove_link}" title="Удалить">×</a>]</span>
<a name="{$original_id}"></a>
{if $with_image == true}<br><a target="_blank" href="{$original_file_link}"><img src="{$original_file_thumbnail_link}" class="thumb" width="{$original_file_thumbnail_width}" heigth="{$original_file_thumbnail_heigth}"></a>
{/if}
<blockquote>
{$original_text}
</blockquote>
<div>
{section name = simple_post loop = $thread}
{include file = 'post_simple.tpl'
    with_image = $thread[simple_post].with_image
    simple_theme = $thread[simple_post].simple_theme
    simple_name = $thread[simple_post].simple_name
    simple_time = $thread[simple_post].simple_time
    simple_file_link = $thread[simple_post].simple_file_link
    simple_file_name = $thread[simple_post].simple_file_name
    simple_file_size = $thread[simple_post].simple_file_size
    simple_file_width = $thread[simple_post].simple_file_width
    simple_file_heigth = $thread[simple_post].simple_file_heigth
    simple_id = $thread[simple_post].simple_id
    simple_link = $thread[simple_post].simple_link
    simple_remove_link = $thread[simple_post].simple_remove_link
    simple_file_thumbnail_link = $thread[simple_post].simple_file_thumbnail_link
    simple_file_thumbnail_width = $thread[simple_post].simple_file_thumbnail_width
    simple_file_thumbnail_heigth = $thread[simple_post].simple_file_thumbnail_heigth
    simple_text = $thread[simple_post].simple_text}

{/section}
</div>
</div>
<br clear="left">
<hr>
