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
Этот шаблон содержит код просмотра нити.

Описание переменных:
    $KOTOBA_DIR_PATH - глобальная переменная, автоматически определяемая для
        всех шаблонов при инициализации Smarty.
    $page_title - заголовок страницы для шаблона header.
    $board_list - список досок для шаблона board_list.
    $thread_location - положение нити в иерархии имейджборды.
    $REPLY_PASS - пароль для удаления сообщения.
    $BOARD_NAME - имя доски, на которой расположена нить.
    $THREAD_NUM - номер нити.

    Описание переменных $with_image, $thread и переменных с префиксом original_
    смотри в шаблоне post_original.
*}
{include file='header.tpl' page_title=$page_title}
{include file='board_list.tpl' board_list=$board_list}<br>
<h4 align=center>βchan</h4>
<br><center><b>{$thread_location}</b></center>
<hr>
<form name="Reply_form" action="{$KOTOBA_DIR_PATH}/reply.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1560576">
<table align="center" border="0">
<tr valign="top"><td>Name: </td><td><input type="text" name="Message_name" size="30"></td></tr>
<tr valign="top"><td>Theme: </td><td><input type="text" name="Message_theme" size="56"> <input type="submit" value="Ответить"></td></tr>
<tr valign="top"><td>Message: </td><td><textarea name="Message_text" rows="7" cols="50"></textarea></td></tr>
<tr valign="top"><td>Image: </td><td><input type="file" name="Message_img" size="54"></td></tr>
<tr valign="top"><td>Password: </td><td><input type="password" name="Message_pass" size="30" value="{$REPLY_PASS}"></td></tr>
<tr valign="top"><td>GoTo: </td><td>(thread: <input type="radio" name="goto" value="t" checked>) (board: <input type="radio" name="goto" value="b">)</td></tr>
<tr valign="top"><td>Sage: </td><td><input type="checkbox" name="Sage" value="sage"></td></tr>
</table>
<input type="hidden" name="b" value="{$BOARD_NAME}">
<input type="hidden" name="t" value="{$THREAD_NUM}">
</form>
<hr>
