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
Код страницы, показывающей id пользователя.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль (см. config.default).
    $show_control - показывать ссылку на страницу административных функций и функций модераторов в панели администратора.
    $boards - доски.
    $id - идентификактор пользователя.
    $groups - группы пользователя.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Мой id'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Мой id</div>
<br/>
Ваш id: {$id}<br>
Вы входите в группы: {section name=i loop=$groups}{$groups[i]} {/section}

{include file='footer.tpl'}