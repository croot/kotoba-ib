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
Этот шаблон содержит код обычного сообщения, которые образуют нити. Это не
то же самое, что оригинальное сообщение, с которого начинается нить.

Описание переменных:
    $with_image - логическая переменная, указывает на то, содержит ли
        сообщение прикреплённую картинку или нет.

    $simple_theme - тема сообщения.
    $simple_name - имя отправителя.
    $simple_time - время получения (время сервера).
    $simple_file_link - ссылка на файл, прикреплённый к сообщению.
    $simple_file_name - имя файла, прикреплённого к сообщению.
    $simple_file_size - размер файла (в байтах), прикреплённого к сообщению.
    $simple_file_width - ширина (для изображений).
    $simple_file_heigth - высота (для изображений).
    $simple_id - номер (он же идентификатор) сообщения.
    $simple_link - ссылка на сообщение.
    $simple_remove_link - ссылка для удаления сообщения.
    $simple_file_thumbnail_link - ссылка на уменьшенную копию изображения или
        иконку для других типов файлов.
    $simple_file_thumbnail_width - ширина уменьшенной копии (для изображений).
    $simple_file_thumbnail_heigth - высота уменьшенной копии (для изображений).
    $simple_text - текст сообщения.
*}
    <table>
    <tr>2
        <td class="reply">
            <span class="filetitle">{$simple_theme}</span> <span class="postername">{$simple_name}</span> {$simple_time}
{if $with_image == true}            <span class="filesize">Файл: <a target="_blank" href="{$simple_file_link}">{$simple_file_name}</a> -(<em>{$simple_file_size} Байт {$simple_file_width}x{$simple_file_heigth}</em>)</span>
{/if}
            <span class="reflink"><span onclick="insert('>>{$simple_id}');">#</span> <a href="{$simple_link}">{$simple_id}</a></span>
            <span class="delbtn">[<a href="{$KOTOBA_DIR_PATH}/{$BOARD_NAME}/r{$original_id}" title="Удалить">×</a>]</span>
            <a name="{$simple_id}"></a>
{if $with_image == true}            <br><a target="_blank" href="{$simple_file_link}"><img src="{$simple_file_thumbnail_link}" class="thumb" width="{$simple_file_thumbnail_width}" heigth="{$simple_file_thumbnail_heigth}"></a>
{/if}
            <blockquote>
            {$simple_text}
            </blockquote>
        </td>
    </tr>
    </table>
