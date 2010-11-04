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
Код конца страницы просмотра нити.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$board - доска, на которой расположена просматриваемая нить.
	$boards - доски.
	$hidden_threads - скрытые пользователем нити на текущей доске.
*}
</div>
</div>
<br clear="left">
<hr>
{if count($hidden_threads) > 0}
Скрытые вами нити:
{section name=i loop=$hidden_threads}
<a href="{$DIR_PATH}/unhide_thread.php?thread={$hidden_threads[i].thread}" title="Нажмите, чтобы отменить скрытие нити.">{$hidden_threads[i].thread_number}</a>
{/section}
{/if}<br>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="footer" style="clear: both;">- Kotoba 1.1 -</div>
{include file='footer.tpl'}