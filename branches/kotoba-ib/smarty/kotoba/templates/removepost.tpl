{include file="header.tpl" page_title="Remove post"}
<h1>Вы удаляете сообщение</h1>
<form action="{$KOTOBA_DIR_PATH}/removepost.php?b={$board}&r={$post}" method="post">
<input type="hidden" name="action" value="password">
<table align="center" border="0">
<tr valign="top">
<td>Password: </td>
<td><input type="password" name="Message_pass" size="30" value="{$pass}">  <input type="submit" value="Remove"></td>
</tr>
</table>
{include file="footer.tpl"}

