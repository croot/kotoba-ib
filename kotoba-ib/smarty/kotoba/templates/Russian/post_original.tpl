{* Smarty *}
{*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.		   *
 *************************************
 *********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Этот шаблон содержит код оригинального сообщения, используемый при просмотре
нити.

Описание переменных:
	$DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$board_name - имя доски, на которой расположена нить (см. config.default).
	$thread_num - нимер нити.

	$original_with_files - сообщение содержит прикреплённый файл.
	$original_theme - тема сообщения.
	$original_name - имя отправителя.
	$original_time - время получения (время сервера).
	$original_file_link - ссылка на файл, прикреплённый к сообщению.
	$original_file_name - имя файла, прикреплённого к сообщению.
	$original_file_size - размер файла (в байтах), прикреплённого к сообщению.
	$original_file_width - ширина (для изображений).
	$original_file_heigth - высота (для изображений).
	$original_num - номер сообщения.
	$original_file_thumbnail_link - ссылка на уменьшенную копию изображения или
		иконку для других типов файлов.
	$original_file_thumbnail_width - ширина уменьшенной копии (для изображений).
	$original_file_thumbnail_heigth - высота уменьшенной копии (для изображений).
	$original_text - текст сообщения.
	$original_hascode - ?
	$original_tripcode - трипкод.
	$sticky - флаг закрепления.
*}
<div>
	<span class="filetitle">{$original_theme}</span> <span class="postername">{$original_name}</span>{if $original_hascode == 1}<span class="postertrip">!{$original_tripcode}</span>{/if} {$original_time}
{if $original_with_files == true}			<span class="filesize">Файл: <a target="_blank" href="{$original_file_link}">{$original_file_name}</a> -(<em>{$original_file_size} Байт {$original_file_width}x{$original_file_heigth}</em>)</span>
{/if}
	<span class="reflink"><span onclick="insert('>>{$original_num}');">#</span> <a href="{$KDIR_PATH}/{$board_name}/{$thread_num}#{$original_num}">{$original_num}</a></span>
	<span class="hidebtn">[<a href="{$DIR_PATH}/{$board_name}/h{$original_num}" title="Скрыть">-</a>]</span>
	<span class="delbtn">[<a href="{$DIR_PATH}/{$board_name}/r{$original_num}" title="Удалить">×</a>]</span>
	{if $sticky} Нить закреплена.{/if}
	<a name="{$original_num}"></a>
{if $original_with_files == true}			<br><a target="_blank" href="{$original_file_link}"><img src="{$original_file_thumbnail_link}" class="thumb" width="{$original_file_thumbnail_width}" heigth="{$original_file_thumbnail_heigth}"></a>
{/if}
	<blockquote>
	{$original_text}
	</blockquote>
<br><br>