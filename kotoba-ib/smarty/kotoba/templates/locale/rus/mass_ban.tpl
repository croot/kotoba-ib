{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of mass ban page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $MAX_FILE_SIZE - maximum size of uploaded file in bytes (see config.default).
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Бан по списку"}

<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>
<script src="{$DIR_PATH}/kotoba.js"></script>

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Бан по списку</div>

<hr>
<div class="postarea">
<form name="postform" id="postform" action="{$DIR_PATH}/admin/mass_ban.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE}">
<table align="center" border="0">
<tbody>
    <tr valign="top">
        <td class="postblock">Список поциентов: </td>
        <td><input type="file" name="file" size="54"> <input type="submit" value="Забанить"></td>
    </tr>
</tbody>
</table>
</form>
Выберите файл , содержащий диапазоны блокируемых адресов. Каждый новый диапазон<br/>
должен начинаться с новой строки (быть разделены символом \n). Например:<br/>
127.0.0.1 127.0.0.3<br/>
127.0.0.5 127.0.0.5<br/>
Забанит поциентов с адресами с 127.0.0.1 по 127.0.0.3 включительно и поциента с адресом 127.0.0.5<br/>
</div>
{include file='footer.tpl'}