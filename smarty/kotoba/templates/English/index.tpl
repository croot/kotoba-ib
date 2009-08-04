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
    $KOTOBA_DIR_PATH - см. объявление одноимённой константы в config.default.
    $boardNames - массив с именами досок и их категориями.
    $stylesheet - стиль оформления.
*}
{include file='header.tpl' page_title='Kotoba Main' stylesheet=$stylesheet}
<p>Версия {$version}. Время модификации {$date}</p>
{if isset($BOARDS_EXIST)}
Список досок: {include file='board_list.tpl' board_list=$boardNames}
{else}
<span class="error">Ошибка. Не создано ни одной доски.</span>
{/if}

<p>
{if isset($isLoggedIn)}
	<a href="{$KOTOBA_DIR_PATH}/logout.php">Выйти</a><br>
{else}
	<a href="{$KOTOBA_DIR_PATH}/login.php">Войти</a>
	(<a href="{$KOTOBA_DIR_PATH}/register.php">Регистрация</a>)
{/if}
</p>
{include file='footer.tpl'}