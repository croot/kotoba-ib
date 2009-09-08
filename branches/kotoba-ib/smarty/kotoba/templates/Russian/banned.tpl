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
Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления.
	$ip - заблокированный адрес.
	$reason - прична блокировки.
*}
{include file='header.tpl' page_title='Этот адрес заблокирован' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
Адрес <b>{$ip}</b>, с которого вы делаете запрос, был заблокирован по причине &quot;<b>{$reason}</b>&quot;.
{include file='footer.tpl'}