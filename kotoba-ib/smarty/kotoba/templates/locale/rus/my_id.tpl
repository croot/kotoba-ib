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
    $STYLESHEET - стиль оформления (см. config.default).
    $id - идентификактор пользователя.
    $groups - группы пользователя.
*}
{include file='header.tpl' page_title='Мой id' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
Ваш id: {$id}<br>
Вы входите в группы: 
{section name=i loop=$groups}
{$groups[i]} 
{/section}
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}