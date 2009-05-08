<?
require_once 'dbconn.php';

function show_posts($link, $board, $thread) {
	$counter = 0;
	$st = mysqli_prepare($link, "select id,board_id,text,image,date_time,sage from posts where board_id = ? and thread_id = ? order by post_number asc, date_time asc");
	if(!$st) {
		die(sprintf("sql: %s", mysqli_error($link)));
	}
	mysqli_stmt_bind_param($st, 'ii', $board, $thread);
	if(!mysqli_stmt_execute($st)) {
		die(sprintf("statment: %s", mysqli_stmt_error($st)));
	}
	if(!mysqli_stmt_bind_result($st, $pid,$board_id,$text,$image,$dt,$s)) {
		die("bind");
	}
	while(mysqli_stmt_fetch($st)) {
		echo "<p>";
		if($counter == 0) echo "<a href=\"thread.php?board=$board_id&thread=$thread\">#$thread:</a><br>";
?>
		<?=$dt?><br><?=$image?><?=$text?>
<?
		echo "</p>";
		$counter ++;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
}

function cleanup_link($link) {
	do {
		$result = mysqli_use_result($link);
		if($result)
			mysqli_free_result($result);

	} while(mysqli_next_result($link));
}
$board = intval($_GET['board']);
$thread = intval($_GET['thread']);
if(!$thread || !$board) {
	die("wut?");
}

$link = conn();
show_posts($link, $board, $thread);

?>
<form action="post.php">
<input type="hidden" name="action" value="post">
<input type="hidden" name="board" value="<?=$board?>">
<input type="hidden" name="thread" value="<?=$thread?>">
<h3>post in thread #<?=$thread?></h3>
text: <input type="text" name="text"><br>
image: <input type="text" name="image"><br>
<input type="checkbox" name="sage">sage</input><br>
<input type="submit">
</form>
</body>
</html>
