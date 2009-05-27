<html>
<head>
	<title>Admin: boards</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
<body>
<table>
<tr>
<td>
ID
</td>
<td>
Board name
</td>
<td>
Description
</td>
<td>
Title
</td>
<td>
Bump limit
</td>
<td>
Rubber
</td>
<td>
Visible threads
</td>
<td>
Same uploads
</td>
<td>
No of threads
</td>
<td>
Command
</td>
</tr>
{foreach from=$boards item=board}
{assign var='id' value=$board.id}
<form>
<input type="hidden" name="id" value="{$board.id}">
<input type="hidden" name="action" value="save">
{include file='adm_boardline.tpl' board=$board}
</form>
{/foreach}
<form>
{assign var='board' value=0}
{assign var='id' value='new'}
<input type="hidden" name="action" value="new">
{include file='adm_boardline.tpl' board=$board}
</form>
</table>
</body>
</html>
