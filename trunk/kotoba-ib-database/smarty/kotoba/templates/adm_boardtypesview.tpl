<html>
<head>
	<title>Admin: boards</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
<body>
<h1>Link filetypes to board {$name}</h1>
<form method="POST" action="board-filetypes.php?board_id={$id}">
<input type="hidden" name="action" value="link">
<select name="types[]" rows=10 multiple>
{foreach from=$types item=type}
<option value="{$type.id}" {if $type.checked == 1}selected{/if}>{$type.extension}</option>
{/foreach}
</select>
<input type="submit">
</form>
</body>
</html>
