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
    $boards - доски.
*}
{assign var="category" value=""}
{section name=i loop=$boards}
{if $category != $boards[i].category}{if $smarty.section.i.index > 0}] {/if}
[{assign var="category" value=$boards[i].category} {$category}: {else} / {/if}
<a href="{$DIR_PATH}/{$boards[i].name}/">{$boards[i].name}</a>{/section}
{if $smarty.section.i.index > 0}]{/if}