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
Код, выводящийся при возникновении критических ошибок.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль (см. config.default).
    $msg - текст сообщения об ошибке.
*}
{include file='header.tpl' page_title='Ошибка' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<span class="error">{$msg}</span>
{include file='footer.tpl'}