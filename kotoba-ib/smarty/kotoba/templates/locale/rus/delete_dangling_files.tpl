{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of delete dangling attachments page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $attachments - attachments.
    $delete_count - deleted attahcments count.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Удаление висячих вложений'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Удаление висячих вложений</div>
<hr>
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