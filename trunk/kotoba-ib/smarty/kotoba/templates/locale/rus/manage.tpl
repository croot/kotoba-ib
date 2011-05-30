{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of manage page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $categories - categories.
    $boards - boards.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Административные функции и функции модераторов'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH categories=$categories boards=$boards}

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