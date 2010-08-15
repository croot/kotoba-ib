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
Код страницы бана в фаерволе.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
*}
{include file='header.tpl' page_title='Бан в фаерволе' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<br><br><a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}
