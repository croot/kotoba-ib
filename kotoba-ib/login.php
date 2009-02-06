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
	<title>Kotoba Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="kotoba.css">
</head>
<body>
';
/*$BODY =
'<form action="login.php" method="post">
	<p>Keyword: 
	<input name="Keyword" type="text" size="80" maxlength="32"> 
	<input type="submit" value="Login">
	</p>
	</form>
';*/
$BODY = '
<form action="login.php" method="post">
<p align="center">
<table width="80%" align="center">
	<div class="pass">
		<center>
			<img src="img/ahs.png"><br>
			Keyword: <input name="Keyword" type="text" maxlength="32">
			<input type="submit" value="Login">
			<br><br><br><br><br><br><br><br><br><br>
			<small>- Kotoba 0.00.α -</small>
		</center>
	</div>
</table>
</form>
';
$FOOTER = '
</body>
</html>';
$ERROR = '';

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/k/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);

session_start();

if(isset($_SESSION['isLoggedIn']))
{
	die($HEAD . '<p>Already logged in.<br><a href="logout.php">[Logout]</a></p>' . $FOOTER);
}

if(isset($_POST['Keyword']))
{
	$keyword_code   = RawUrlEncode($_POST['Keyword']);
	$keyword_length = strlen($keyword_code);

	if($keyword_length >= 16 && $keyword_length <= 32 && strpos($keyword_code, '%') === false)
	{
		$keyword_hash = md5($keyword_code);
		require 'database_connect.php';
		
		if(($result = mysql_query('select `id` from `users` where `Key` = ' . "\"$keyword_hash\"")) != false)
		{
			if(mysql_num_rows($result) == 0)
			{
				$BODY .= '<p>Error.<br>Not registred.</p>' . "\n<br>[<a href=\"index.php\">Home</a>]";
			}
			else
			{
				if(mysql_query('update `users` set `SID` = "' . session_id() . '" where `key` = ' . "\"$keyword_hash\"") == false)
				{
					$BODY = '<p>Error.<br>Session binding failed by reason: ' . mysql_error() . '.</p>';
				}
				else
				{
					$_SESSION['isLoggedIn'] = session_id();
					$BODY = '<p>Logged in.<br><a href="logout.php">[Logout]</a></p>' . "\n<br>[<a href=\"index.php\">Home</a>]";
				}
			}
		}
		else
		{
			$BODY = '<p>Error.<br>Searching in database falied by reason: ' . mysql_error() . '.</p>';
		}
	}
	else
	{
		$BODY .= '<br><p>Error.<br>Keyword: 16-32, A-Za-z0-9_-.</p>';
	}
}
	
echo ($HEAD . $BODY . $FOOTER);
?>