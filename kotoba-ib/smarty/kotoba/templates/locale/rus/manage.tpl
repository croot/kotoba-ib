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
Код страницы административных функций и функций модераторов.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль (см. config.default).
    $INVISIBLE_BOARDS - Имена досок, которые будут принудительно не видны в списке досок. (см. config.default).
    $show_control - показывать ссылку на страницу административных функций и функций модераторов в панели администратора.
    $boards - доски.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Административные функции и функции модераторов'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Административные функции и функции модераторов</div>
{if isset($adm_panel)}
<p>Панель администратора.<br/>
{include file='adm_panel.tpl' DIR_PATH=$DIR_PATH}</p>
{/if}
{if isset($mod_panel)}
<p>Панель модератора.<br/>
{include file='mod_panel.tpl' DIR_PATH=$DIR_PATH}</p>
{/if}
{include file='footer.tpl'}