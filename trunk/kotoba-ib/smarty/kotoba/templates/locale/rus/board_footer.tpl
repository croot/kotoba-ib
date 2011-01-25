{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of footer of boards page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $hidden_threads - hidden threads.
    $pages - pages numbers.
    $page - page number.
    $boards - boards.
    $board - board.
*}
{if count($hidden_threads) > 0}
Скрытые вами нити:
{section name=i loop=$hidden_threads}
<a href="{$DIR_PATH}/unhide_thread.php?thread={$hidden_threads[i].thread}" title="Нажмите, чтобы отменить скрытие нити.">{$hidden_threads[i].thread_number}</a>
{/section}
{/if}<br>
{include file='pages_list.tpl' board_name=$board.name pages=$pages page=$page}
<br>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="footer" style="clear: both;">- Kotoba 1.1 -</div>
{include file='footer.tpl'}