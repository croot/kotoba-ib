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

require 'config.php';
require 'common.php';

$HEAD = '<html>
<head>
	<title>Kotoba Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="' . KOTOBA_DIR_PATH . '/kotoba.css">
</head>
<body>
';

$BODY = '
<form action="' . KOTOBA_DIR_PATH . '/login.php" method="post">
<p align="center">
<table width="80%" align="center">
	<div class="pass">
		<center>
			<img src="' . KOTOBA_DIR_PATH . '/img/logo.png"><br>
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

if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/login.stat', 'a')) == false)
        die($HEAD . '<span class="error">Ошибка. Неудалось открыть или создать файл статистики.</span>' . $FOOTER);

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

if(isset($_SESSION['isLoggedIn']))
	die($HEAD . "<p>\n\tВы уже вошли.<br>\n\t<a href=\"" . KOTOBA_DIR_PATH . "/logout.php\">Выйти</a>\n</p>" . $FOOTER);

if(isset($_POST['Keyword']))
{
	$keyword_code   = RawUrlEncode($_POST['Keyword']);
	$keyword_length = strlen($keyword_code);

	if($keyword_length >= 16 && $keyword_length <= 32 && strpos($keyword_code, '%') === false)
	{
		$keyword_hash = md5($keyword_code);
		require 'databaseconnect.php';

		if(($result = mysql_query("select `id` from `users` where `Key` = '$keyword_hash'")) != false)
		{
			if(@mysql_num_rows($result) == 0)
				$BODY .= "<p>\n\tОшибка. Вы не зарегистрированы.<br>\n\t<a href=\"" . KOTOBA_DIR_PATH . "/index.php\">На главную</a>\n</p>";
			else
			{
				if(mysql_query('update `users` set `SID` = "' . session_id() . "\" where `key` = '$keyword_hash'") == false)
				{
					if(KOTOBA_ENABLE_STAT)
						kotoba_stat(sprintf(ERR_UPDATE_USER_SID, mysql_error()));

					die($HEAD . '<span class="error">Ошибка. Неудалось обновить идентификатор сессии пользователя. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
				}
				else
				{
					$_SESSION['isLoggedIn'] = session_id();
					$BODY = "<p>Вы вошли.<br>\n\t<a href=\"" . KOTOBA_DIR_PATH . "/logout.php\">Выйти</a><br>\n\t" .
							"<a href=\"" . KOTOBA_DIR_PATH . "/index.php\">На главную</a>\n</p>";
				}
			}
		}
		else
		{
			if(KOTOBA_ENABLE_STAT)
				kotoba_stat(sprintf(ERR_USER_DATA, mysql_error()));

			die($HEAD . '<span class="error">Ошибка. Невозможно получить данные пользователя. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
		}
	}
	else
		$BODY .= "<p>\n\tОшибка. Ключевое слово должно иметь длину 16-32 свимолов A-Z a-z 0-9 _ -\n</p>";
}

echo $HEAD . $BODY . $FOOTER;
?>
<?php
/*
 * Выводит сообщение $errmsg в файл статистики $stat_file.
 */
function kotoba_stat($errmsg)
{
    global $stat_file;
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
    fclose($stat_file);
}
?>