<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

$HEAD_KOTOBA_CONNECTION = 
"<html>
<head>
	<title>Kotoba connection.</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"kotoba.css\">
</head>
<body>
";

$FOOTER_KOTOBA_CONNECTION = 
'
</body>
</html>';

$hostname = 'localhost';
$username = 'root';
$password = '';
$dbName   = 'kotoba';

if(mysql_connect($hostname, $username, $password) == 0)
	die($HEAD_KOTOBA_CONNECTION . '<p>Error.<br>Connection to database failed.</p>' . $FOOTER_KOTOBA_CONNECTION);

if(mysql_select_db($dbName) == false)
	die($HEAD_KOTOBA_CONNECTION . '<p>Error.<br>Selection database failed by reason: ' . mysql_error() . '.</p>' . $FOOTER_KOTOBA_CONNECTION);
?>