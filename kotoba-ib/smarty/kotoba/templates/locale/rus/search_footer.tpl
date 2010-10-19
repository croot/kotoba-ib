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
Код конца страницы поиска сообщений.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $pages - номера страниц.
    $page - номер просматриваемой страницы.
    $keyword - искомая фраза.
    $boards - доски.
*}
<hr/>
{include file='search_pages_list.tpl' pages=$pages page=$page keyword=$keyword boards=$boards}

<div class="footer" style="clear: both;">- Kotoba 1.1 -</div>
{include file='footer.tpl'}