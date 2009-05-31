<?
function create_post($link, $board, $thread, $text, $image, $sage) {
	if($sage) $sage = 1;
	else $sage = 0;
	if(strlen($image) > 0) {
		$st = mysqli_prepare($link, "call sp_post(?, ?, ?, null, ?, ?)");
		mysqli_stmt_bind_param($st, 'iissi', $board, $thread, $text, $image, $sage);
	}
	elseif(strlen($image) == 0) {
		$st = mysqli_prepare($link, "call sp_post(?, ?, ?, null, null, ?)");
		mysqli_stmt_bind_param($st, 'iisi', $board, $thread, $text, $sage);
	}
	else {
		die("image?");
	}
	if(!$st) {
		die(sprintf("statment: %s", mysqli_error($link)));
	}
	mysqli_stmt_execute($st);

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
$action = strval($_GET['action']);

require_once 'dbconn.php';
if($action == 'newthread') {
	$thread = 0;
}
elseif($action == 'post') {
	$thread = intval($_GET['thread']);
}

$text = strval($_GET['text']);
$image = strval($_GET['image']);
$strsage = strval($_GET['sage']);

$sage = false;
if($strsage == 'on') $sage = true;

$link = conn();
create_post($link, $board, $thread, $text, $image, $sage);
mysqli_close($link);
header("Location: showboard?id=$board");
?>
