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

require 'common.php';

$HEAD = 
'<html>
<head>
	<title>Kotoba удаление доски</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="' . KOTOBA_DIR_PATH . '/kotoba.css">
</head>
<body>
';

$FOOTER = 
'
</body>
</html>';

die($HEAD . 'Удаление разумно лишь при полном или частичном архивировании данных с доски и потому временно выпилено. ЭТОТ СКРИПТ УДОЛИТ ВСЁ!' . $FOOTER);

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);    // 1 день.
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

if(isset($_SESSION['isLoggedIn']))  // Только для зарегистрированных пользователей.
{
	require 'database_connect.php';
	
	if(isset($_GET['Boardname']))
	{
		$boardname_code   = RawUrlEncode($_GET['Boardname']);
		$boardname_length = strlen($boardname_code);

		if($boardname_length >= 1 && $boardname_length <= 16 && strpos($boardname_code, '%') === false)
		{
			if(mysql_query("delete from `boards` where `Name` = '$boardname_code'") === false)
			{
				$temp = '<span class="error">Ошибка. Удаление доски завершилось неудачей. Причина: ' . mysql_error() . '</span><br>';
			}
			else
			{
				rmdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$boardname_code/arch/");
				rmdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$boardname_code/img/");
				rmdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$boardname_code/thumb/");
				rmdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$boardname_code/");
				$temp = "<p>Доска $boardname_code успешно удалена.</p>";
			}
		}
		else
		{
			$temp = '<p>Wrong format.</p>';
		}
	}

    if(($result = mysql_query('select `Name` from `boards` order by `Name`')) !== false)
    {
        if(mysql_num_rows($result) == 0)
        {
            $BODY = '<span class="error">Ошибка. Не создано ни одной доски.</span><br>' . $temp;
        }
        else
        {
            $BODY = "<p>Список досок: ";
            
            while (($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false)
                $BODY .= '/<a href="' . KOTOBA_DIR_PATH . "/rem_board.php?Boardname=$row[Name]\">$row[Name]</a>/ ";

            $BODY = substr($BODY, 0, strlen($BODY) - 1);
			$BODY .= '</p>' . $temp;
        }

        mysql_free_result($result);
    }
    else
    {
        $BODY = '<span class="error">Ошибка. Невозможно получить список досок. Причина: ' . mysql_error() . '.</span><br>' . $temp;
    }
}
else
{
	$BODY = '<span class="error">Залогиньтесь: <a href="login.php">[login]</a></span>';
}

$BODY .= '<br>[<a href="index.php">Home</a>]';

echo $HEAD . $BODY . $FOOTER;
?>