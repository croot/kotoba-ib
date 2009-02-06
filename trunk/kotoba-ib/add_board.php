<?php
$HEAD = 
'<html>
<head>
	<title>Kotoba add board.</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="kotoba.css">
</head>
<body>
';
$FORM =	'
<form action="add_board.php" method="post">
	<p>Boardname: 
	<input name="Boardname" type="text" size="30" maxlength="16"> 
	<input type="submit" value="Add">
	</p>
</form>
';
$FOOTER = 
'
</body>
</html>';

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/k/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

// For registred users only.
if(isset($_SESSION['isLoggedIn']))
{
	require 'database_connect.php';
	
	// Board addition.
	if(isset($_POST['Boardname']))
	{
		$boardname_code   = RawUrlEncode($_POST['Boardname']);
		$boardname_length = strlen($boardname_code);

		if($boardname_length >= 1 && $boardname_length <= 16 && strpos($boardname_code, '%') === false)
		{
			if(mysql_query('insert into `boards` (`Name`) values (\'' . $boardname_code . '\')') == false)
			{
				$temp = '<p>Error.<br>Board addition falied by reason: ' . mysql_error() . '</p>';
			}
			else
			{
				mkdir($_SERVER['DOCUMENT_ROOT'] . '/k/' . $boardname_code . "/arch/", '0777', true);
				mkdir($_SERVER['DOCUMENT_ROOT'] . '/k/' . $boardname_code . "/thumb/", '0777', true);
				mkdir($_SERVER['DOCUMENT_ROOT'] . '/k/' . $boardname_code . "/img/", '0777', true);
				$temp = '<p>' . $boardname_code . ' Successfully added.</p>';
			}
		}
		else
		{
			$temp = '<p>Wrong format.</p>';
		}
	}

	// Getting boards list.
	if(($result = mysql_query('select `Name` from `boards`')) == false)
	{
		$BODY = '<p>Error.<br>Getting boards list falied by reason: ' . mysql_error() . '.</p>' . $FORM . $temp;
	}
	else
	{
		if(mysql_num_rows($result) == 0)
		{
			$BODY = '<p>There is no boards.</p>' . $FORM . $temp;
		}
		else
		{
			$BODY = "<p>Boards list: ";
	
			while ($row = mysql_fetch_array($result, MYSQL_NUM))
			{
	        	$BODY .= "[/<a href=\"$row[0]/\">$row[0]</a>/] ";
			}
	
			$BODY = substr($BODY, 0, strlen($BODY) - 1);
			$BODY .= '</p>' . $FORM . $temp;
	    }

		mysql_free_result($result);
	}
}
else
{
	$BODY = '<p>Not logged in. Plz login here <a href="login.php">[login]</a></p>';
}

$BODY .= '<br><a href="index.php">[Home]</a>';

echo $HEAD . $BODY . $FOOTER;
?>