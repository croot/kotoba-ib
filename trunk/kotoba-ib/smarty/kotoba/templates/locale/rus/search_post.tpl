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
Код сообщения в результатах поиска.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $post - сообщение.
    $enable_translation - Включение перевода текста сообщения (см. config.default).
    $enable_geoip - Включение отображения страны автора сообщения (см. config.default).
    $country - Данные о стране автора.
    $enable_postid - Включение идентификатора поста.
    $postid - Идентификатор поста.
    $author_admin - Сообщение было оставлено администратором.
*}
<hr/>
<table>
    <tbody>
        <tr>
            <td class="reply">
                <a name="{$post.number}"></a>
                {if $enable_geoip}<span title="{$country.name}" class="country"><img src="http://410chan.ru/css/flags/{$country.code}.gif" alt="{$country.name}"></span>&nbsp;{/if}

                <span class="filetitle">{$post.subject}</span>
                <span class="postername">{$post.name}</span>
                {if $post.tripcode != null}<span class="postertrip">!{$post.tripcode}</span>{/if}
                {if $author_admin} <span class="admin">❀❀&nbsp;Админ&nbsp;❀❀</span>{/if}
                {$post.date_time}
                <span class="reflink">
                    <span onclick="insert('>>{$post.number}');">#</span>
                    <a href="{$DIR_PATH}/{$post.board.name}/{$post.thread.original_post}#{$post.number}">{$post.number}</a>
                </span>
                {if $enable_postid} ID:{$postid}{/if}
                <br>
                <blockquote id="post{$post.number}">
                    {$post.text}
                    {if $post.text_cutted == 1}
                        <div class="abbrev">Нажмите "Ответ" для просмотра сообщения целиком.</div>
                    {/if}
                </blockquote>
                {if $enable_translation && $post.text}<blockquote id="translation{$post.number}"></blockquote><a href="#" onclick="javascript:translate('{$post.number}'); return false;">Lolšto?</a>{/if}

            </td>
        </tr>
    </tbody>
</table>
