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
    $KOTOBA_DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $boardNames - массив с именами досок и их категориями.
    $stylesheet - стиль оформления.
*}
{include file='header.tpl' page_title='Главная страница' stylesheet=$stylesheet}
<p>Версия {$version}. Время модификации {$date}</p>
{if isset($BOARDS_EXIST)}
Список досок: {include file='board_list.tpl' board_list=$boardNames}
{else}
<span class="error">Ошибка. Не создано ни одной доски.</span>
{/if}

<p><a href="{$KOTOBA_DIR_PATH}/edit_settings.php">Мои настройки</a><br></p>
{include file='footer.tpl'}