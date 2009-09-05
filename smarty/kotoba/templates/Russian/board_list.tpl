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
    $board_list - массив должен быть передан из вызывающего шаблона.
*}
{assign var="category" value=""}
{section name=i loop=$board_list}
{if $category != $board_list[i].category}{if $smarty.section.i.index > 0}] {/if}
[{assign var="category" value=$board_list[i].category} {$category}: {else} / {/if}
<a href="{$DIR_PATH}/{$board_list[i].name}/">{$board_list[i].name}</a>{/section}
{if $smarty.section.i.index > 0}]{/if}
