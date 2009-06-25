{include file=header.tpl page_title="Board categories"}
<table>
{foreach from=$categories item=category}
<tr>
<form action="categories.php" method="GET">
<input type="hidden" name="id" value="{$category.cid}"
<input type="hidden" name="action" value="save"
<td>{$category.cid}</td>
<td><input type="text" name="corder" value="{$category.corder}" size="3"></td>
<td><input type="text" name="cname" value="{$category.cname}"></td>
<td><input type="submit" name="submit" value="Save"></td>
</form>
</tr>
{/foreach}
{* new category *}
<tr>
<form action="categories.php" method="GET">
<input type="hidden" name="id" value="{$category.cid}"
<input type="hidden" name="action" value="new"
<td>New</td>
<td><input type="text" name="corder"  size="3"></td>
<td><input type="text" name="cname" ></td>
<td><input type="submit" name="submit" value="Create"></td>
</form>
</tr>
</table>
{include file=footer.tpl}
