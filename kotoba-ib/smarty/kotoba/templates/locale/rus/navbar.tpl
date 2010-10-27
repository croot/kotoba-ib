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
Код панели навигации.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $INVISIBLE_BOARDS - Имена досок, которые будут принудительно не видны в списке досок. (см. config.default).
    $boards - доски.
*}
<div class="navbar">
{assign var="category" value=""}
{section name=i loop=$boards}
{if !isset($INVISIBLE_BOARDS) || !in_array($boards[i].name, $INVISIBLE_BOARDS)}
{if $category != $boards[i].category_name}{if $smarty.section.i.index > 0}]
{/if}
[{assign var="category" value=$boards[i].category_name} {$category}: {else} / {/if}
<a href="{$DIR_PATH}/{$boards[i].name}/">{$boards[i].name}</a>{/if}{/section}
{if $smarty.section.i.index > 0}]
{/if}
[<a href="{$DIR_PATH}/">Главная</a>]
</div>