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
	$boards - доски.
	$threads - нити.
*}
{include file='header.tpl' page_title='Редактирование настроек нити' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
{include file='threads_settings_list.tpl' boards=$boards threads=$threads DIR_PATH=$DIR_PATH}
<a href="{$DIR_PATH}/">На главную</a>
{include file='footer.tpl'}