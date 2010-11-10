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
Код страницы бана по списку.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $show_control - показывать ссылку на страницу административных фукнций и фукнций модераторов в панели администратора.
    $boards - доски.
    $MAX_FILE_SIZE - максимальный размер загружаемого файла (в байтах) (см. config.default).
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Бан по списку"}

<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script src="{$DIR_PATH}/kotoba.js"></script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>

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
        <td><input type="file" name="file" size="54"> <input type="submit" value="Забанить"></td></tr>
</tbody>
</table>
</form>
Выберите файл , содержащий диапазоны блокируемых адресов. Каждый новый диапазон<br/>
должен начинаться с новой строки (быть разделены символом \n). Например:<br/>
127.0.0.1 127.0.0.3<br/>
127.0.0.5 127.0.0.5<br/>
Забанит поциентов с адресами с 127.0.0.1 по 127.0.0.3 включительно и поциента с адресом 127.0.0.5<br/>
</div>