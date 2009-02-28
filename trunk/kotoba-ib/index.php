<?php
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/
?>
<?php
$HEAD = 
'<html>
<head>
	<title>Kotoba Main</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="kotoba.css">
</head>
<body>
';
$FOOTER = 
'
</body>
</html>';

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/k/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

require 'databaseconnect.php';

if(($result = mysql_query('select `Name` from `boards` order by `Name`')) != false)
{
	if(mysql_num_rows($result) == 0)
	{
		$BODY = '<p>There is no boards.</p>';
	}
	else
	{
		$BODY = "<p>Boards list: ";

		while ($row = mysql_fetch_array($result, MYSQL_NUM))
		{
        	$BODY .= "[/<a href=\"$row[0]/\">$row[0]</a>/] ";
		}

		$BODY = substr($BODY, 0, strlen($BODY) - 1);
		$BODY .= '</p>';
    }

	mysql_free_result($result);
}
else
{
	$BODY = '<p>Error.<br>Searching in database falied by reason: ' . mysql_error() . '.</p>';
}

if(isset($_SESSION['isLoggedIn']))
{
	$BODY .= '<p><a href="logout.php">[logout]</a><br>';
	$BODY .= '<a href="add_board.php">[add]</a><br>';
	$BODY .= '<a href="rem_board.php">[remove]</a></p>';
}
else
{
	$BODY .= '<p><a href="login.php">[login]</a></p>';
}

echo $HEAD . $BODY . $FOOTER;
?>