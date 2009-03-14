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

$HEAD = "<html>\n<head>\n\t<title>Kotoba Main</title>\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n\t" .
		"<link rel=\"stylesheet\" type=\"text/css\" href=\"" . KOTOBA_DIR_PATH . "/kotoba.css\">\n</head>\n<body>\n";
$FOOTER = "\n</body>\n</html>";

if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/index.stat', 'a')) == false)
        die($HEAD . '<span class="error">Ошибка. Неудалось открыть или создать файл статистики.</span>' . $FOOTER);

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/sessions/');
ini_set('session.gc_maxlifetime', 60 * 60 * 24);
ini_set('session.cookie_lifetime', 60 * 60 * 24);
session_start();

require 'databaseconnect.php';

// Получение списка досок.
$BOARDS_LIST = '';

if(($result = mysql_query('select `Name`, `id` from `boards` order by `Name`')) !== false)
{
	if(mysql_num_rows($result) == 0)
		$BOARDS_LIST = '<span class="error">Ошибка. Не создано ни одной доски.</span><br>';
	else
	{
		$BOARDS_LIST = "Список досок: ";
		
		while (($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false)
			$BOARDS_LIST .= '/<a href="' . KOTOBA_DIR_PATH . "/$row[Name]/\">$row[Name]</a>/ ";
    }

	mysql_free_result($result);
}
else
{
    if(KOTOBA_ENABLE_STAT)
            kotoba_stat(sprintf(ERR_BOARDS_LIST, mysql_error()));

	die($HEAD . '<span class="error">Ошибка. Невозможно получить список досок. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
}

if(isset($_SESSION['isLoggedIn']))
	$BOARDS_LIST .= "\n<p>\n\t<a href=\"" . KOTOBA_DIR_PATH . "/logout.php\">Выйти</a><br>\n\t" .
					"<a href=\"" . KOTOBA_DIR_PATH . "/add_board.php\">Добавить доску</a><br>\n\t" .
					"<a href=\"" . KOTOBA_DIR_PATH . "/rem_board.php\">Удалить доску</a>\n</p>";
else
	$BOARDS_LIST .= "\n<p>\n\t<a href=\"" . KOTOBA_DIR_PATH . "/login.php\">Войти</a>\n</p>";

echo $HEAD . $BOARDS_LIST . $FOOTER;
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