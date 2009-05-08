<?
$host = 'localhost';
$user = 'kotoba2';
$pass = 'kotoba';
$database = 'kotoba2';

function conn() {
	global $host, $user, $pass, $database;
	$link = @mysqli_connect($host, $user, $pass, $database);

	if(!$link) {
		die(mysqli_connect_error());
	}
	return $link;
}

