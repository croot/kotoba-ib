{include file="header.tpl" page_title="Hide thread"}
<form method="GET" action="un-hide.php">
<input type="hidden" name="action" value="dohide">
<input type="hidden" name="b" value="{$board}">
<input type="hidden" name="t" value="{$thread}">
Спрятать нить {$thread} на доске {$board} по причине <input type="text" name="reason"><input type="submit" value="&gt;&gt;&gt;">
</form>
{include file="footer.tpl"}
