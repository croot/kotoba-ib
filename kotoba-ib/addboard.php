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

// Заметки:
//
// Наверное излишне будет добавлять сюда сбор статистики. Новые доски создаются очень редко.

require 'common.php';

$HEAD = 
'<html>
<head>
	<title>Kotoba добавление доски</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="' . KOTOBA_DIR_PATH . '/kotoba.css">
</head>
<body>
';

$FORM =	'
<form action="' . KOTOBA_DIR_PATH . '/addboard.php" method="post">
	<p>Имя доски:
	<input name="Boardname" type="text" size="30" maxlength="16"> 
	<input type="submit" value="Добавить">
	</p>
</form>
';

$FOOTER = 
'
</body>
</html>';

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);    // 1 день.
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

if(isset($_SESSION['isLoggedIn']))  // Только для зарегистрированных пользователей.
{
	require 'databaseconnect.php';

	if(isset($_POST['Boardname']))
	{
		$boardname_code   = RawUrlEncode($_POST['Boardname']);
		$boardname_length = strlen($boardname_code);

		if($boardname_length >= 1 && $boardname_length <= 16 && strpos($boardname_code, '%') === false)
		{
			if(mysql_query("insert into `boards` (`Name`, `MaxPostNum`, `Board Settings`) values ('$boardname_code', 0, null)") == false)
				$temp = '<span class="error">Ошибка. Добаление доски завершилось неудачей. Причина: ' . mysql_error() . '</span><br>';
			else
			{
				$base = sprintf("%s%s/%s", $_SERVER['DOCUMENT_ROOT'], KOTOBA_DIR_PATH, $boardname_code);
				if(mkdir ($base)) {
					chmod ($base, 0777);
					$subdirs = array("arch", "img", "thumb");
					foreach($subdirs as $dir) {
						$subdir = sprintf("$base/%s", $dir);
						if(mkdir($subdir)) {
							chmod($subdir, 0777);
						}
						else {
							die("create $subdir failed: $php_errormsg");
						}
					}
				}
				/*mkdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$boardname_code/arch/", '0777', true);
				mkdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$boardname_code/img/", '0777', true);
				mkdir($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/$boardname_code/thumb/", '0777', true);*/
				$temp = "<p>Доска $boardname_code успешно добавлена.</p>";
			}
		}
		else
			$temp = '<span class="error">Ошибка. Неверный формат имени доски.</span><br>';
	}

    if(($result = mysql_query('select `Name` from `boards` order by `Name`')) !== false)
    {
        if(mysql_num_rows($result) == 0)
            $BODY = '<span class="error">Ошибка. Не создано ни одной доски.</span><br>' . $FORM . $temp;
        else
        {
            $BODY = "<p>Список досок: ";

            while (($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false)
                $BODY .= '/<a href="' . KOTOBA_DIR_PATH . "/$row[Name]/\">$row[Name]</a>/ ";

            $BODY = substr($BODY, 0, strlen($BODY) - 1);
			$BODY .= '</p>' . $FORM . $temp;
        }

        mysql_free_result($result);
    }
    else
        $BODY = '<span class="error">Ошибка. Невозможно получить список досок. Причина: ' . mysql_error() . '.</span><br>' . $FORM . $temp;
}
else
	$BODY = "Вы не вошли в систему.<br>\n<a href=\"" . KOTOBA_DIR_PATH . '/login.php">Войти</a>';

$BODY .= "<br>\n<a href=\"" . KOTOBA_DIR_PATH . '/index.php">На главную</a>';

echo $HEAD . $BODY . $FOOTER;
?>