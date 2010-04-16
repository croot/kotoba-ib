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
Код главной страницы имэйджборды.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления.
    $boards - досоки.
*}
{include file='header.tpl' page_title='Главная страница' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<p>Версия {$version}. Время модификации {$date}</p>
{if isset($boards_exist)}
<div class="navbar">{include file='board_list.tpl' boards=$boards DIR_PATH=$DIR_PATH} [<a href="{$DIR_PATH}/">Главная</a>]</div>
{/if}

<p><a href="{$DIR_PATH}/edit_settings.php">Мои настройки</a><br></p>
{include file='footer.tpl'}