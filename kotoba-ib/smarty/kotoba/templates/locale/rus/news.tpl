{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of imageboard main page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $ib_name - imageboard name.
    $version - version of Kotoba.
    $last_modification - date of last Kotoba modification.
    $news_html - news html code.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET
         page_title='Новости'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

<div class="logo">{$ib_name}</div>
<p>Версия {$version}. Время последнего изменения: {$last_modification}.</p>
<p>{$news_html}</p>
{include file='footer.tpl'}