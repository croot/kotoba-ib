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
    $pages - массив номеров страниц.
    $page - номер текущей страницы.
    $keyword - искомая фраза.
    $boards - доски.
*}
{if isset($pages) && isset($page) && count($pages) > 0}<div class="boardpages">Страницы: {section name=i loop=$pages}
{if $pages[i] == $page} ({$pages[i]}){else} <a href="{$DIR_PATH}/search.php?search[page]={$pages[i]}&search[keyword]={$keyword}{section name=j loop=$boards}{if isset($boards[j].selected)}&search[boards][]={$boards[j].id}{/if}{/section}">({$pages[i]})</a>{/if}{/section}
</div>{/if}