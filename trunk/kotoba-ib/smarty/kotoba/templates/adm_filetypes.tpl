<html>
<head>
	<title>Admin: file types</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
<body>
<table>
{foreach from=$types item=filetype}
<form method="POST" action="filetypes.php" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$filetype.id}">
<input type="hidden" name="action" value="save">
<tr>
<td>
Image: <input type="checkbox" name="image" value="1" {if $filetype.image == 1}checked{/if}>
</td>
<td>
<input type="text" name="extension" value="{$filetype.extension}">
</td>
<td>
<input type="text" name="store_extension" value="{$filetype.store_extension}">
</td>
<td>
<select name="handler">
<option value=2 {if $filetype.handler == "internal"}selected{/if}>Internal</option>
<option value=3 {if $filetype.handler == "internal_png"}selected{/if}>Internal (png)</option>
<option value=1 {if $filetype.handler == "store"}selected{/if}>Store (provide thumbnail)</option>
</select>
</td>
<td>
{if $filetype.handler == "store"}<img src="{$filetype.thumbnail_image}">{/if}
<input type="file" name="thumbnail_image" value="{$filetype.thumbnail_image}">
</td>
<td>
<input type="submit" value="Save">
<input type="submit" name="delete" value="delete">
<input type="reset" name="delete" value="Undo">
</td>
</tr>
</form>
{/foreach}
<form method="POST" action="filetypes.php" enctype="multipart/form-data">
<input type="hidden" name="id" value="0">
<input type="hidden" name="action" value="new">
<tr border=1>
<td>
Image: <input type="checkbox" name="image" value="1">
</td>
<td>
<input type="text" name="extension" value="">
</td>
<td>
<input type="text" name="store_extension" value="">
</td>
<td>
<select name="handler">
<option value=2 >Internal</option>
<option value=3 >Internal (png)</option>
<option value=1 >Store (provide thumbnail)</option>
</select>
</td>
<td>
<input type="file" name="thumbnail_image" value="">
</td>
<td>
<input type="submit" value="Save">
<input type="submit" name="delete" value="delete">
<input type="reset" name="delete" value="Undo">
</td>
</tr>
</form>
</table>
</body>
</html>
