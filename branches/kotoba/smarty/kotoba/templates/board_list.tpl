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
    $KOTOBA_DIR_PATH - должна быть объявлена в вызывающем шаблоне.
*}
{assign var="cid" value=0}
{section name=i loop=$board_list}
{if $cid != $board_list[i].cid}
{if $smarty.section.i.index > 0}] {/if}
[ {assign var="cid" value=$board_list[i].cid}{else} / {/if}
<a title="{$board_list[i].board_description}" href="{$KOTOBA_DIR_PATH}/{$board_list[i].board_name}/">{$board_list[i].board_name}</a> <!-- () -->
{/section}{if $smarty.section.i.index > 0}]{/if}
