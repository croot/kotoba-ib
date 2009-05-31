<?
function print_form($action, $id) {
	echo $action;
	if($action == 'save_edit') {
	}
	elseif($action == 'save_new') {
	}
	else {
		die("no, wut?");
	}
?>
	<form action="edboard.php" method="GET">
	<input type="hidden" name="action" value="<?=$action?>">
	<input type="hidden" name="id" value="<?=$id?>">
	Name: <input type="text" name="name" value="<?=$name?>"><br>
	Desc: <input type="text" name="description" value="<?=$name?>"><br>
	BL: <input type="text" name="bumplimit" value="<?=$name?>"><br>
	Threads: <input type="text" name="threads" value="<?=$name?>"><br>
<input type="submit">
</form>
<?
}

$action = $_GET['action'];
if($action == 'new') {
	print_form('save_new', 0);
}
elseif($action == 'edit') {
	$id = $_GET['id'];
	print_form('save_edit', $id);
}
elseif($action == 'save_edit') {
}
elseif($action == 'save_new') {
	$name = $_GET['name'];
	$desc = $_GET['description'];
	$blim = $_GET['bumplimit'];
	$threads = $_GET['threads'];

require_once('dbconn.php');

	$link = conn();
	$result = mysqli_query($link, sprintf("call sp_create_board('%s', '%s', %d, %d)",
		$name, $desc, $blim, $threads));
	if(! $result) {
		die(mysqli_error($link));
	}
	header("Location: index.php");
}
else {
	die("wut?");
}


?>

