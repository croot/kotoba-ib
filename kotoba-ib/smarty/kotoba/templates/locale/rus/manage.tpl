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
Код страницы административных фукнций и фукнций модераторов.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления (см. config.default).
*}
{include file='header.tpl' page_title='Административные фукнции и функции модераторов' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
{if isset($adm_panel)}
<p>Панель администратора:<br>
{include file='adm_panel.tpl' DIR_PATH=$DIR_PATH}</p>
{/if}
{if isset($mod_panel)}
<p>Панель модератора:<br>
{include file='mod_panel.tpl' DIR_PATH=$DIR_PATH}</p>
{/if}
{include file='footer.tpl'}