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
Код начала страницы поиска сообщений.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль (см. config.default).
    $show_control - показывать ссылку на страницу административных функций и функций модераторов в панели администратора.
    $boards - доски.
    $pages - номера страниц.
    $page - номер просматриваемой страницы.
    $keyword - искомая фраза.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Поиск сообщений'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Поиск сообщений</div>
{include file='search_pages_list.tpl' pages=$pages page=$page keyword=$keyword boards=$boards}

<hr/>
<div class="postarea">
<table class="postform">
<form method="POST" action="{$DIR_PATH}/search.php">
<tr>
    <td class="postblock">Поиск</td>
    <td><input type="text" size="55" maxlength="75" name="search[keyword]" value="{if $keyword}{$keyword}{/if}"><input type="submit" value="Искать"></td>
</tr>
<tr>
    <td class="postblock">Доски</td>
    <td>{section name=i loop=$boards}

        <input type="checkbox" name="search[boards][]" value="{$boards[i].id}" {if isset($boards[i].selected)} checked{/if}>/{$boards[i].name}/</input>{/section}

    </td>
</tr>
<tr>
    <td colspan="2" class="rules"><ul>
        <li>Минимальная длина слова: 4.</li>
        <li>Отметьте доски для поиска. Если ни одна доска не отмечена, то поиск производится по всем доскам.</li></ul>
    </td>
</tr>
</form>
</table>
</div>
