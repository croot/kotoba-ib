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
Код окончания страницы просмотра доски.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$board_name - имя просматриваемой доски (см. config.default).
	$hidden_threads - скрытые пользователем нити на текущей доске.
	$pages - номера страниц.
	$page - номер просматриваемой страницы.
*}
{if count($hidden_threads) > 0}
Скрытые вами нити:
{section name=i loop=$hidden_threads}
 <a href="{$DIR_PATH}/{$board_name}/u{$hidden_threads[i].number}" title="Нажмите, чтобы отменить скрытие нити.">{$hidden_threads[i].number}</a>
{/section}
{/if}<br>
{include file='pages_list.tpl' board_name=$board_name pages=$pages page=$page}
{include file='footer.tpl'}