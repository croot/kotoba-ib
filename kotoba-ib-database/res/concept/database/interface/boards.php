<html>
<body>
<h1>boards</h1>
<?
require_once 'dbconn.php';

function show_board($id, $description) {
?>
<a href="showboard.php?id=<?=$id?>">* <?=$description?></a><br>
<?
}

$link = conn();

$st = mysqli_prepare($link, "select id, board_description from boards");
if(! $st) {
	die(mysqli_error($link));
}
if(!mysqli_stmt_execute($st)) {
	die(sprintf("statment: %s", mysqli_stmt_error()));
}
mysqli_stmt_bind_result($st, $id, $description);
while(mysqli_stmt_fetch($st)) {
	show_board($id, $description);
}

mysqli_stmt_close($st);
mysqli_close($link);
?>
</body>
</html>
