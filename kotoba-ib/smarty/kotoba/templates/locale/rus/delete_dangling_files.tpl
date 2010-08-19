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
Код страницы удаления висячих вложений.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $attachments - вложения.
    $delete_count - количество удалённых вложений.
*}
{include file='header.tpl' page_title='Удаление висячих вложений' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
{if isset($delete_count)}
    Было удалено {$delete_count} висячих вложений.
{else}
    <form action="{$DIR_PATH}/admin/delete_dangling_attachments.php" method="post" enctype="text/html">
    <table border="1">
    <tbody>
    <tr>
    <td><input type="submit" name="submit" id="submit" value="Удалить"></td>
    <td><input type="checkbox" name="delete_all" id="delete_all" value="1">Все</td>
    </tr>
    {section name=i loop=$attachments}
        {if isset($attachments[i].flag)}
            {if $attachments[i].attachment_type == $ATTACHMENT_TYPE_FILE}
                <tr>
                <td><input type="checkbox" name="delete_file_{$attachments[i].id}" id="delete_file_{$attachments[i].id}" value="1"></td>
                <td><a target="_blank" href="{$attachments[i].link}"><img src="{$attachments[i].thumbnail}" class="thumb" width="{$attachments[i].thumbnail_w}" height="{$attachments[i].thumbnail_h}"></a></td>
                </tr>
            {elseif $attachments[i].attachment_type == $ATTACHMENT_TYPE_IMAGE}
                <tr>
                <td><input type="checkbox" name="delete_image_{$attachments[i].id}" id="delete_image_{$attachments[i].id}" value="1"></td>
                <td><a target="_blank" href="{$attachments[i].link}"><img src="{$attachments[i].thumbnail}" class="thumb" width="{$attachments[i].thumbnail_w}" height="{$attachments[i].thumbnail_h}"></a></td>
                </tr>
            {elseif $attachments[i].attachment_type == $ATTACHMENT_TYPE_LINK}
                <tr>
                <td><input type="checkbox" name="delete_link_{$attachments[i].id}" id="delete_link_{$attachments[i].id}" value="1"></td>
                <td><a target="_blank" href="{$attachments[i].link}"><img src="{$attachments[i].thumbnail}" class="thumb" width="{$attachments[i].thumbnail_w}" height="{$attachments[i].thumbnail_h}"></a></td>
                </tr>
            {elseif $attachments[i].attachment_type == $ATTACHMENT_TYPE_VIDEO}
                <tr>
                <td><input type="checkbox" name="delete_video_{$attachments[i].id}" id="delete_video_{$attachments[i].id}" value="1"></td>
                <td>{$attachments[i].link}</td>
                </tr>
            {/if}
        {/if}
    {/section}
    </tbody>
    </table>
    </form>
{/if}
{include file='footer.tpl'}