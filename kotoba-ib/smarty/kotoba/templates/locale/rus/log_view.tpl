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
Код страницы просмотра лога.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $show_control - показывать ссылку на страницу административных фукнций и фукнций модераторов в панели администратора.
    $log - лог.
    $boards - доски.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Просмотр лога"}

<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script src="{$DIR_PATH}/kotoba.js"></script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Просмотр лога</div>

<hr>
<table border="1">
<tr><td>Дата</td><td>id пользователя</td><td>Группы пользователя</td><td>IP-адрес</td><td>Сообщение</td></tr>
{section name=i loop=$log}
    <tr><td>{$log[i][0]}</td><td>{$log[i][1]}</td><td>{$log[i][2]}</td><td>{$log[i][3]}</td><td>{$log[i][4]}</td></tr>
{/section}
</table>

{include file='footer.tpl'}