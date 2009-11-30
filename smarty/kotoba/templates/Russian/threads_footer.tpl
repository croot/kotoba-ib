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
	$board_name - имя просматриваемой доски.
	$hidden_threads - скрытые пользователем нити на текущей доске.
*}
</div>
<br clear="left">
<hr>
{if count($hidden_threads) > 0}
Скрытые вами нити:
{section name=i loop=$hidden_threads}
 <a href="{$DIR_PATH}/{$board_name}/u{$hidden_threads[i].id}" title="Нажмите, чтобы отменить скрытие нити.">{$hidden_threads[i].number}</a>
{/section}
{/if}
{include file='footer.tpl'}