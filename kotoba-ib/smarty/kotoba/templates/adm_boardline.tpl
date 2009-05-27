<tr>
<td>
{$id}
</td>
<td>
<input type="text" name="board_name" value="{$board.board_name}"
</td>
<td>
<input type="text" name="board_description" value="{$board.board_description}"
</td>
<td>
<input type="text" name="board_title" value="{$board.board_title}"
</td>
<td>
<input type="text" name="bump_limit" value="{$board.bump_limit}"
</td>
<td>
<input type="checkbox" name="rubberboard" value="{if $board.rubber_board == 1}on{/if}"
</td>
<td>
<input type="text" name="visible_threads" value="{$board.visible_threads}"
</td>
<td>
<input type="text" name="same_upload" value="{$board.same_upload}"
</td>
<td>
{$board.threads}
</td>
<td>
<input type="submit" value="Save">
</td>
</tr>
