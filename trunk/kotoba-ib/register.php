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
	<title>Kotoba Register</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="kotoba.css">
</head>
<body>
';
$BODY = '<form action="register.php" method="post">
	<p>Keyword: 
	<input name="Keyword" type="text" size="80" maxlength="32"> 
	<input type="submit" value="Register">
	</p>
	</form>';
$FOOTER = 
'
</body>
</html>';

if(isset($_POST['Keyword']))
{
	$keyword_code   = RawUrlEncode($_POST['Keyword']);
	$keyword_length = strlen($keyword_code);
	
	if($keyword_length >= 16 && $keyword_length <= 32)
	{
		$keyword_hash = md5($keyword_code);		
		
		require 'database_connect.php';
		
		if(($result = mysql_query('select `id` from `users` where `Key` = ' . "'$keyword_hash'")) != false)
		{
			if(mysql_num_rows($result) == 0)
			{
				if(mysql_query('insert into `users` (`Key`, `SID`, `User Settings`) values (\'' . $keyword_hash . '\', null, \'\')') != false)
				{
					$BODY = '<p>Registred successfull.</p>';
				}
				else
				{
					$BODY = '<p>Error.<br>Registration failed by reason: ' . mysql_error() . '.</p>';
				}
			}
			else
			{
				if(mysql_query('delete from `users` where `Key` = ' . "'$keyword_hash'") != false)
				{
					$BODY = '<p>UNREGISTRED!</p>';
				}
				else
				{
					$BODY = '<p>Error.<br>Unregistration failed by reason: ' . mysql_error() . '.</p>';
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
		$BODY .= "\n<br><p>Error.<br>Keyword: 16-32, A-Za-z0-9_-.</p>";
	}
}

echo($HEAD . $BODY . $FOOTER);
?>