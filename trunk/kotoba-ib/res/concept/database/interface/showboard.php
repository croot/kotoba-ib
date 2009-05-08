<html>
</body>
<?
require_once 'dbconn.php';

function show_pages($link, $board_id, $threads) {
	$st = mysqli_prepare($link, "select get_pages(?, ?)");
	mysqli_stmt_bind_param($st, 'ii', $board_id, $threads);
	mysqli_stmt_execute($st);
	mysqli_stmt_bind_result($st, $pages);

	mysqli_stmt_fetch($st);

	mysqli_stmt_close($st);
	cleanup_link($link);
	if($pages == 0) {
		echo "no posts<br>";
	}
	else {
		for($page = 0; $page < $pages; $page ++) {
?>
	<a href="showboard.php?id=<?=$board_id?>&page=<?=$page?>"><?=$page?></a>&nbsp;
<?
		}
	}
}

function get_threads($link, $board_id, $page, $threads_qty) {
	$st = mysqli_prepare($link, "call sp_threads_on_page(?, ?, ?)");
	mysqli_stmt_bind_param($st, 'iii', $board_id, $threads_qty, $page);
	mysqli_stmt_execute($st);
	mysqli_stmt_bind_result($st, $thread_id);

	$threads = array();

	while(mysqli_stmt_fetch($st)) {
		array_push($threads, $thread_id);
	}

	mysqli_stmt_close($st);
	cleanup_link($link);

	return $threads;
}
function show_posts_preview($link, $previews) {
	$counter = 0;
	foreach($previews as $preview) {
		$thread = $preview[0];
		$id = $preview[1];
		$posts = $preview[3];
		$images = $preview[4];
		$st = mysqli_prepare($link, "select id,board_id,text,image,date_time,sage from posts where id = ?");
		if(!$st) {
			die(sprintf("sql: %s", mysqli_error($link)));
		}
		mysqli_stmt_bind_param($st, 'i', $id);
		if(!mysqli_stmt_execute($st)) {
			die(sprintf("statment: %s", mysqli_stmt_error($st)));
		}
		if(!mysqli_stmt_bind_result($st, $pid,$board_id,$text,$image,$dt,$s)) {
			die("bind");
		}
		mysqli_stmt_fetch($st);
		echo "<p>";
		if($counter == 0) echo "<a href=\"thread.php?board=$board_id&thread=$thread\">#$thread:</a><br>";
?>
		<?=$dt?><br><?=$image?><?=$text?>
<?
		if($counter == 0 && $posts > 0) {
			echo "$posts | $images<br>";
		}
		echo "</p>";
		mysqli_stmt_close($st);
		//cleanup_link($link);
		$counter ++;
	}
}
function get_thread_preview($link, $board_id, $thread, $posts_preview) {
	$st = mysqli_prepare($link, "call sp_thread_preview(?, ?, ?)");
	if(!$st) {
		die(sprintf("statment: %s", mysqli_error($link)));
	}
	mysqli_stmt_bind_param($st, 'iii', $board_id, $thread, $posts_preview);
	mysqli_stmt_execute($st);
	mysqli_stmt_bind_result($st, $post_id, $post_number, $posts, $images);
	//post_id, post_number, posts_skip, with_images_skip
	$preview = array();
	while(mysqli_stmt_fetch($st)) {
		array_push($preview, array($thread, $post_id, $post_number, $posts, $images));
	}

	mysqli_stmt_close($st);
	cleanup_link($link);

	return $preview;
}

function cleanup_link($link) {
	do {
		$result = mysqli_use_result($link);
		if($result)
			mysqli_free_result($result);

	} while(mysqli_next_result($link));
}


$link = conn();

$id = intval($_GET['id']);
$page = intval($_GET['page']);

if(!is_int($id)) {
	die("incorrect id");
}
if(!is_int($page)) {
	$page = 0;
}

$N = 10;
$PN = 10;
show_pages($link, $id, $N);

$threads = get_threads($link, $id, $page, $N);

foreach ($threads as $thread) {
	show_posts_preview($link, get_thread_preview($link, $id, $thread, $PN));
}


mysqli_close($link);
?>
<form action="post.php">
<input type="hidden" name="action" value="newthread">
<input type="hidden" name="board" value="<?=$id?>">
<h3>new thread</h3>
text: <input type="text" name="text"><br>
image: <input type="text" name="image"><br>
<input type="checkbox" name="sage">sage</input><br>
<input type="submit">
</form>
</body>
</html>
