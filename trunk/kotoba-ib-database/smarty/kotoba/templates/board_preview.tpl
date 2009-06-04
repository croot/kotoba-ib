{include file='header.tpl' page_title=$page_title}
{include file='board_list.tpl' board_list=$board_list}<br>
<h4 align=center>βchan</h4>
<center><b>/{$BOARD_NAME}/</b></center>Постлимит: {$POST_COUNT}/{$KOTOBA_POST_LIMIT}<br>
Бамплимит: {$BOARD_BUMPLIMIT}
<hr>

<form action="{$KOTOBA_DIR_PATH}/createthread.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1560576">
<table align="center" border="0">
<tr valign="top"><td>Name: </td><td><input type="text" name="Message_name" size="30"></td></tr>
<tr valign="top"><td>Theme: </td><td><input type="text" name="Message_theme" size="48"> <input type="submit" value="Create Thread"></td></tr>
<tr valign="top"><td>Message: </td><td><textarea name="Message_text" rows="7" cols="50"></textarea></td></tr>
<tr valign="top"><td>Image: </td><td><input type="file" name="Message_img" size="54"></td></tr>
<tr valign="top"><td>Password: </td><td><input type="password" name="Message_pass" size="30" value="{$OPPOST_PASS}"></td></tr>
<tr valign="top"><td>GoTo: </td><td>(thread: <input type="radio" name="goto" value="t">) (board: <input type="radio" name="goto" value="b" checked>)</td></tr>
</table>
<input type="hidden" name="b" value="{$BOARD_NAME}">
</form>

{foreach from=$THREADS item=thread}
<p>
{$thread}
</p>
{/foreach}

<hr>
<br>
{foreach from=$PAGES item=page}
({if $page.selected == 1}{$page.page}{else}<a href="{$KOTOBA_DIR_PATH}/{$BOARD_NAME}/p{$page.page}">{$page.page}</a>{/if})
{/foreach}
{include file='footer.tpl'}
