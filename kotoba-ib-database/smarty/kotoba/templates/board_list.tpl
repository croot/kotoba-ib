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
{section name=i loop=$board_list}/<a href="{$KOTOBA_DIR_PATH}/{$board_list[i].board_name}/">{$board_list[i].board_name}</a> ({$board_list[i].board_description})/ {/section}
