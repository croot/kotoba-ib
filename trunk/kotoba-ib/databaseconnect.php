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

if(@mysql_connect('localhost', 'root', '') == false)
{
	require 'events.php';

	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_DB_CONNECT);

	die("<html>\n<head>\n\t<title>Kotoba установление соединения</title>\n" .
		"\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n" .
		"\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . KOTOBA_DIR_PATH . "/kotoba.css\">\n</head>\n<body>\n" .
		"\t<span class=\"error\">Ошибка. Неудалось установить соединение с сервром БД.</span>\n</body>\n</html>");
}

if(mysql_select_db('kotoba') == false)
{
	require 'events.php';

	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_DB_SELECT, mysql_error()));

	die("<html>\n<head>\n\t<title>Kotoba установление соединения</title>\n" .
		"\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n" .
		"\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . KOTOBA_DIR_PATH . "/kotoba.css\">\n</head>\n<body>\n" .
		"\t<span class=\"error\">Ошибка. Неудалось выбрать базу данных. Причина: " . mysql_error() . "</span>\n</body>\n</html>");
}
?>