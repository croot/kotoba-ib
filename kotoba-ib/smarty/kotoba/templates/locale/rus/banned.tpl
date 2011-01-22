{* Smarty *}
{********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of "banned" page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $ip - banned IP-address.
    $reason - ban reason.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Адрес заблокирован'}

Адрес <b>{$ip}</b>, с которого вы сделали запрос, был заблокирован по причине: &quot;<b>{$reason}</b>&quot;.
{include file='footer.tpl'}