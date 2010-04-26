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
Код мини панели администратора и модератора.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
	$STYLESHEET - стиль оформления (см. config.default).
	$post_id - идентификатор сообщения.
	$ip - IP адрес автора сообщения.
	$board_name - имя доски.
	$post_num - номер сообщения.
*}
<span class="postadmin">[<span class="posterip">{$ip}</span>
[<form action="{$DIR_PATH}/admin/edit_bans.php" method="post">
	<input type="submit" value="Бан"><input type="checkbox" name="add_text" value="1">
	<input type="hidden" name="new_range_beg" value="{$ip}">
	<input type="hidden" name="new_range_end" value="{$ip}">
	<input type="hidden" name="new_reason" value="">
	<input type="hidden" name="new_untill" value="10">
	<input type="hidden" name="post" value="{$post_id}">
	<input type="hidden" name="submited" value="1">
</form>]
[<form action="{$DIR_PATH}/admin/hard_ban.php" method="post">
	<input type="submit" value="Бан ф."><input type="checkbox" name="add_text" value="1">
	<input type="hidden" name="range_beg" value="{$ip}">
	<input type="hidden" name="range_end" value="{$ip}">
	<input type="hidden" name="post" value="{$post_id}">
</form>]
<form action="{$DIR_PATH}/admin/remove_post.php" method="post">
	<input type="submit" value="Удалить">
	<input type="hidden" name="board" value="{$board_name}">
	<input type="hidden" name="post" value="{$post_num}">
</form>
]</span>