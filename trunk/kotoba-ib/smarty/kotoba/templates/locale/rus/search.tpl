{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of search page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $pages - pages numbers.
    $page - page number.
    $keyword - keyword.
    $count - count of founded posts.
    $posts_html - code of founded posts.
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

{if $count > 0}
<hr/>
<div class="replymode">Результаты поиска</div>
<b>Найдено {$count} сообщений:</b>
{$posts_html}
{/if}

<hr/>
{include file='search_pages_list.tpl' pages=$pages page=$page keyword=$keyword boards=$boards}

<div class="footer" style="clear: both;">- Kotoba 1.1 -</div>
{include file='footer.tpl'}