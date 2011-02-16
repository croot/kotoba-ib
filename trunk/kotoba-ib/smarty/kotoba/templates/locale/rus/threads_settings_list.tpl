{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit thread settings form.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $boards - boards.
    $threads - threads.
*}
<form action="{$DIR_PATH}/admin/edit_threads.php" method="post">
<table border="1">
<tr><td colspan="7">Измените параметры и сохраните изменения.</td></tr>
<tr>
    <td>Идентификатор</td>
    <td>Доска</td>
    <td>Оригинальное сообщение</td>
    <td>Бамплимит специфичный для нити</td>
    <td>Флаг закрепления</td>
    <td>Флаг поднятия нити при ответе</td>
    <td>Флаг загрузки изображений</td>
</tr>
{section name=i loop=$threads}
<tr>
    <td>{$threads[i].id}</td>
    <td>{$threads[i].board.name}</td>
    <td>{$threads[i].original_post}</td>
    <td><input type="text" name="bump_limit_{$threads[i].id}" value="{$threads[i].bump_limit}"></td>
    <td><input type="checkbox" name="sticky_{$threads[i].id}" value="1"{if $threads[i].sticky} checked{/if}></td>
    <td><input type="checkbox" name="sage_{$threads[i].id}" value="1"{if $threads[i].sage} checked{/if}></td>
    <td>
        <select name="with_attachments_{$threads[i].id}">
            <option value=""{if $threads[i].with_attachments === null} selected{/if}>Inherit</option>
            <option value="1"{if $threads[i].with_attachments == '1'} selected{/if}>Up</option>
            <option value="0"{if $threads[i].with_attachments == '0'} selected{/if}>Down</option>
        </select>
    </td>
</tr>
{/section}
</table>
<br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>