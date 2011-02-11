{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of mass ban page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $MAX_FILE_SIZE - maximum size of uploaded file in bytes (see config.default).
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Mass ban"}

<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>
<script src="{$DIR_PATH}/kotoba.js"></script>

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="logo">Mass ban</div>

<hr>
<div class="postarea">
<form name="postform" id="postform" action="{$DIR_PATH}/admin/mass_ban.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE}">
<table align="center" border="0">
<tbody>
    <tr valign="top">
        <td class="postblock">Fags list: </td>
        <td><input type="file" name="file" size="54"> <input type="submit" value="Ban"></td>
    </tr>
</tbody>
</table>
</form>
Select file what contains ranges of IP-addresses for ban it. Every range must ends with \n (new line) sign.
Example:<br/>
127.0.0.1 127.0.0.3<br/>
127.0.0.5 127.0.0.5<br/>
Will ban fags with IP's at 127.0.0.1 to 127.0.0.3 inclusive and fag with IP 127.0.0.5<br/>
</div>
{include file='footer.tpl'}