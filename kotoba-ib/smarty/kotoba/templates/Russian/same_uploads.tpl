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
Код страницы, выводящей ссылки на одинаковые файлы.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$board_name - имя просматриваемой доски (см. config.default).
    $same_uploads - загруженные ранее файлы.
*}
{include file='header.tpl' page_title='Одинаковые файлы' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<p>Файл был загружен ранее в следующих сообщениях:</p>
{section name=i loop=$same_uploads}
{if $same_uploads[i].view}<a href="{$DIR_PATH}/{$board_name}/{$same_uploads[i].thread_number}#{$same_uploads[i].post_number}">&gt;&gt;{$same_uploads[i].post_number}</a><br>{/if}
{/section}
{include file='footer.tpl'}