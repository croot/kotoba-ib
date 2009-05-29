<tr>
<td>
{$id}
</td>
<td>
<input type="text" name="board_name" value="{$board.board_name}">
</td>
<td>
<input type="text" name="board_description" value="{$board.board_description}">
</td>
<td>
<input type="text" name="board_title" value="{$board.board_title}">
</td>
<td>
<a href="board-filetypes.php?board_id={$board.id}">Supported filetypes:</a><br>
{assign var='id' value=$board.id}
{foreach from=$board_types.$id item=type}{$type} {/foreach}
</td>
<td>
<input type="text" name="bump_limit" value="{$board.bump_limit}">
</td>
<td>
<input type="checkbox" name="rubberboard" value="on" {if $board.rubber_board == 1}checked{/if}>
</td>
<td>
<input type="text" name="visible_threads" value="{$board.visible_threads}">
</td>
<td>
<select name="same_upload">
<option value="0" {if $board.same_upload == "yes"}selected{/if}>yes</option>
<option value="1" {if $board.same_upload == "once"}selected{/if}>store</option>
<option value="2" {if $board.same_upload == "no"}selected{/if}>no</option>
</select>
<!-- <input type="text" name="same_upload" value="{$board.same_upload}" -->
</td>
<td>
{$board.threads}
</td>
<td>
<input type="submit" value="Save">
<input type="submit" name="delete" value="delete">
<input type="reset" name="delete" value="Undo">
</td>
</tr>
